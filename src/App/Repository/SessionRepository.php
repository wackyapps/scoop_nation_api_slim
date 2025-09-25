<?php
declare(strict_types=1);

namespace App\Repository;
require_once __DIR__ . '/SQL_Table_Names.php';

use DB;
use Ramsey\Uuid\Uuid;

class SessionRepository extends BaseRepository
{
    protected $table = TABLE_SESSIONS;
    protected $primaryKey = 'id';

    /**
     * Start a new anonymous session for a visitor
     * Generates a new session_id (UUID) and creates session record
     * 
     * @param string $cookieToken The client-side random token from Next.js frontend
     * @param string $ipAddress Visitor's IP address
     * @param string|null $userAgent Visitor's user agent string
     * @return array The created session record
     * TODO: Add business_id, branch_id into session (if needed in future)
     */
    public function startNewAnonymousSession(string $cookieToken, string $ipAddress, ?string $userAgent = null): array
    {
        /**
         * Generate a new session_id (UUID) is a GUID (globally unique identifier) e.g: 98412935-6939-451a-b9da-cfca6c967293
         */
        $sessionId = Uuid::uuid4()->toString();

        $data = [
            'session_id' => $sessionId,
            'cookie_token' => $cookieToken,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'is_active' => 1,
            'last_activity' => date('Y-m-d H:i:s.u'), // Current timestamp with microseconds
            'created_at' => date('Y-m-d H:i:s.u'),
            // cart_id will be set when first item is added to cart
        ];

        $sessionIdInserted = DB::insert($this->table, $data);
        $insertedId = DB::insertId();

        // Return the complete session record
        return $this->find($insertedId);
    }

    /**
     * Retrieve session by cookie token and session ID
     * 
     * @param string $cookieToken The client-side cookie token
     * @param string $sessionId The server-generated session ID (GUID)
     * @return array|null Session record or null if not found
     */
    public function retrieveSessionByCookieAndSessionId(string $cookieToken, string $sessionId): ?array
    {
        $query = "
            SELECT * FROM {$this->table} 
            WHERE cookie_token = %s 
            AND session_id = %s 
            AND is_active = 1
        ";

        $session = $this->executeQueryFirstRow($query, [$cookieToken, $sessionId]);

        if ($session) {
            // Update last_activity timestamp
            $this->updateLastActivity((int) $session['id']);
        }

        return $session ?: null;
    }

    /**
     * Helper method to update last_activity timestamp
     * 
     * @param int $sessionId Database ID of the session
     * @return int Number of affected rows
     */
    private function updateLastActivity(int $sessionId): int
    {
        $query = "UPDATE {$this->table} SET last_activity = NOW(3) WHERE id = %i";
        return DB::query($query, $sessionId);
    }


    /**
     * Link up session cart to logged in user when user logs in 
     * and session exists in request with cookie_token and session_id
     * @param int $sessionId Database ID of the session
     * @param int $cookieToken The client-side cookie token
     * @param int $userId Database ID of the logged in user
     * @param int|null $businessId Optional business ID to associate with session
     * @param int|null $branchId Optional branch ID to associate with session
     */

    public function linkSessionCartToUser(int $sessionId, string $cookieToken, int $userId, ?int $businessId = null, ?int $branchId = null): void
    {
        $data = [
            'user_id' => $userId,
            'business_id' => $businessId,
            'branch_id' => $branchId,
            'last_activity' => date('Y-m-d H:i:s.u')
        ];

        DB::update($this->table, $data, "id = %i AND cookie_token = %s", $sessionId, $cookieToken);
    }

    /**
     * Convert session cart to order cart when visitor checks out
     * Creates customer, user (if needed), order, and order items records
     * 
     * @param int $sessionId Database ID of the session
     * @param string $fullname Customer's full name
     * @param string $email Customer's email
     * @param string $phone Customer's phone
     * @param string|null $gender Customer's gender
     * @param string|null $dateOfBirth Customer's date of birth (YYYY-MM-DD)
     * @param int|null $branchId Optional branch ID for the order
     * @return array|null Order record with order items, or null if failed
     */
    
    public function convertSessionCartToOrderCart(
        int $sessionId,
        string $fullname,
        string $email,
        string $phone,
        ?string $gender = null,
        ?string $dateOfBirth = null,
        ?int $branchId = null
    ): ?array {
        // Get session to ensure it exists and get cart_id
        $session = $this->find($sessionId);
        if (!$session || !$session['cart_id']) {
            return null;
        }

        $cartId = $session['cart_id'];

        // Check if user already exists by email or phone
        $existingUser = DB::queryFirstRow(
            "SELECT id FROM " . TABLE_USER . " WHERE email = %s OR phone = %s",
            $email,
            $phone
        );

        $userId = null;
        if ($existingUser) {
            $userId = $existingUser['id'];
        } else {
            // Create new user
            $userData = [
                'email' => $email,
                'phone' => $phone,
                'role' => 'customer',
                'email_verified' => 0,
                'phone_verified' => 0,
                'createdAt' => date('Y-m-d H:i:s')
            ];

            DB::insert(TABLE_USER, $userData);
            $userId = DB::insertId();
        }

        // Create or update customer record
        $existingCustomer = DB::queryFirstRow(
            "SELECT id FROM " . TABLE_CUSTOMER . " WHERE user_id = %i",
            $userId
        );

        $customerId = null;
        if ($existingCustomer) {
            $customerId = $existingCustomer['id'];
            // Update existing customer
            DB::update(
                TABLE_CUSTOMER,
                [
                    'fullname' => $fullname,
                    'gender' => $gender,
                    'date_of_birth' => $dateOfBirth,
                    'updatedAt' => date('Y-m-d H:i:s')
                ],
                "id = %i",
                $customerId
            );
        } else {
            // Create new customer
            $customerData = [
                'user_id' => $userId,
                'fullname' => $fullname,
                'gender' => $gender,
                'date_of_birth' => $dateOfBirth,
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s')
            ];

            DB::insert(TABLE_CUSTOMER, $customerData);
            $customerId = DB::insertId();
        }

        // Get cart items
        $cartItems = DB::query(
            "SELECT ci.id, ci.productId, ci.variantId, ci.quantity, p.price 
             FROM " . TABLE_CART_ITEM . " ci 
             JOIN " . TABLE_PRODUCT . " p ON ci.productId = p.id 
             WHERE ci.cartId = %i",
            $cartId
        );

        if (empty($cartItems)) {
            return null; // No items in cart
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Create order
        $orderData = [
            'customer_id' => $customerId,
            'branch_id' => $branchId,
            'dateTime' => date('Y-m-d H:i:s.u'),
            'status' => 'pending',
            'total' => $total,
            'orderNotice' => null
        ];

        DB::insert(TABLE_ORDER, $orderData);
        $orderId = DB::insertId();

        // Create order items
        foreach ($cartItems as $item) {
            $orderItemData = [
                'customerOrderId' => $orderId,
                'productId' => $item['productId'],
                'variantId' => $item['variantId'],
                'quantity' => $item['quantity']
            ];

            DB::insert(TABLE_ORDER_ITEM, $orderItemData);
        }

        // Update session to link with user
        DB::update($this->table, ['user_id' => $userId], "id = %i", $sessionId);

        // Return the created order with items
        $order = DB::queryFirstRow("SELECT * FROM " . TABLE_ORDER . " WHERE id = %i", $orderId);
        $orderItems = DB::query("SELECT * FROM " . TABLE_ORDER_ITEM . " WHERE customerOrderId = %i", $orderId);

        $order['items'] = $orderItems;
        return $order;
    }

    /**
     * Add product to cart in session
     * Creates cart if it doesn't exist, then adds product to cart_item
     * 
     * @param int $sessionId Database ID of the session
     * @param int $productId Product ID to add
     * @param int $variantId Variant ID to add
     * @param int $quantity Quantity to add (default: 1)
     * @return array|null Updated cart item or null if failed
     */
    public function addProductToCartInSession(int $sessionId, int $productId, int $variantId, int $quantity = 1): ?array
    {
        // Get session
        $session = $this->find($sessionId);
        if (!$session) {
            return null;
        }

        $cartId = $session['cart_id'];

        // If no cart exists, create one
        if (!$cartId) {
            $cartData = [
                'sessionId' => $session['session_id'],
                'createdAt' => date('Y-m-d H:i:s.u'),
                'updatedAt' => date('Y-m-d H:i:s.u')
            ];

            DB::insert(TABLE_CART, $cartData);
            $cartId = DB::insertId();

            // Update session with cart_id
            DB::update($this->table, ['cart_id' => $cartId], "id = %i", $sessionId);
        }

        // Check if product/variant already exists in cart
        $existingCartItem = DB::queryFirstRow(
            "SELECT id, quantity FROM " . TABLE_CART_ITEM . " 
             WHERE cartId = %i AND productId = %i AND variantId = %i",
            $cartId,
            $productId,
            $variantId
        );

        if ($existingCartItem) {
            // Update existing cart item
            $newQuantity = $existingCartItem['quantity'] + $quantity;
            DB::update(
                TABLE_CART_ITEM,
                ['quantity' => $newQuantity, 'updatedAt' => date('Y-m-d H:i:s.u')],
                "id = %i",
                $existingCartItem['id']
            );

            return DB::queryFirstRow(
                "SELECT * FROM " . TABLE_CART_ITEM . " WHERE id = %i",
                $existingCartItem['id']
            );
        } else {
            // Create new cart item
            $cartItemData = [
                'cartId' => $cartId,
                'productId' => $productId,
                'variantId' => $variantId,
                'quantity' => $quantity,
                'createdAt' => date('Y-m-d H:i:s.u'),
                'updatedAt' => date('Y-m-d H:i:s.u')
            ];

            DB::insert(TABLE_CART_ITEM, $cartItemData);
            $cartItemId = DB::insertId();

            return DB::queryFirstRow(
                "SELECT * FROM " . TABLE_CART_ITEM . " WHERE id = %i",
                $cartItemId
            );
        }
    }

    /**
     * Increase product quantity in cart
     * 
     * @param int $sessionId Database ID of the session
     * @param int $productId Product ID
     * @param int $variantId Variant ID
     * @param int $increment Amount to increment by (default: 1)
     * @return array|null Updated cart item or null if not found
     */
    public function increaseProductQuantity(int $sessionId, int $productId, int $variantId, int $increment = 1): ?array
    {
        $session = $this->find($sessionId);
        if (!$session || !$session['cart_id']) {
            return null;
        }

        $cartId = $session['cart_id'];

        // Get current cart item
        $cartItem = DB::queryFirstRow(
            "SELECT id, quantity FROM " . TABLE_CART_ITEM . " 
             WHERE cartId = %i AND productId = %i AND variantId = %i",
            $cartId,
            $productId,
            $variantId
        );

        if (!$cartItem) {
            return null;
        }

        // Update quantity
        $newQuantity = $cartItem['quantity'] + $increment;
        DB::update(
            TABLE_CART_ITEM,
            ['quantity' => $newQuantity, 'updatedAt' => date('Y-m-d H:i:s.u')],
            "id = %i",
            $cartItem['id']
        );

        // Return updated cart item
        return DB::queryFirstRow(
            "SELECT * FROM " . TABLE_CART_ITEM . " WHERE id = %i",
            $cartItem['id']
        );
    }

    /**
     * Decrease product quantity in cart
     * If quantity becomes 0 or less, the item is removed
     * 
     * @param int $sessionId Database ID of the session
     * @param int $productId Product ID
     * @param int $variantId Variant ID
     * @param int $decrement Amount to decrement by (default: 1)
     * @return array|null Updated cart item, or null if removed or not found
     */
    public function decreaseProductQuantity(int $sessionId, int $productId, int $variantId, int $decrement = 1): ?array
    {
        $session = $this->find($sessionId);
        if (!$session || !$session['cart_id']) {
            return null;
        }

        $cartId = $session['cart_id'];

        // Get current cart item
        $cartItem = DB::queryFirstRow(
            "SELECT id, quantity FROM " . TABLE_CART_ITEM . " 
             WHERE cartId = %i AND productId = %i AND variantId = %i",
            $cartId,
            $productId,
            $variantId
        );

        if (!$cartItem) {
            return null;
        }

        $newQuantity = $cartItem['quantity'] - $decrement;

        if ($newQuantity <= 0) {
            // Remove the item
            DB::delete(TABLE_CART_ITEM, "id = %i", $cartItem['id']);
            return null;
        } else {
            // Update quantity
            DB::update(
                TABLE_CART_ITEM,
                ['quantity' => $newQuantity, 'updatedAt' => date('Y-m-d H:i:s.u')],
                "id = %i",
                $cartItem['id']
            );

            // Return updated cart item
            return DB::queryFirstRow(
                "SELECT * FROM " . TABLE_CART_ITEM . " WHERE id = %i",
                $cartItem['id']
            );
        }
    }

    /**
     * Remove product item from cart
     * 
     * @param int $sessionId Database ID of the session
     * @param int $productId Product ID
     * @param int $variantId Variant ID
     * @return bool True if item was removed, false if not found
     */
    public function removeProductFromCartInSession(int $sessionId, int $productId, int $variantId): bool
    {
        $session = $this->find($sessionId);
        if (!$session || !$session['cart_id']) {
            return false;
        }

        $cartId = $session['cart_id'];

        // Get cart item ID
        $cartItem = DB::queryFirstRow(
            "SELECT id FROM " . TABLE_CART_ITEM . " 
             WHERE cartId = %i AND productId = %i AND variantId = %i",
            $cartId,
            $productId,
            $variantId
        );

        if (!$cartItem) {
            return false;
        }

        // Delete the item
        $affectedRows = DB::delete(TABLE_CART_ITEM, "id = %i", $cartItem['id']);
        return $affectedRows > 0;
    }

    /**
     * Get cart items for a session
     * 
     * @param int $sessionId Database ID of the session
     * @return array Cart items with product details
     */
    public function getCartItemsForSession(int $sessionId): array
    {
        $session = $this->find($sessionId);
        if (!$session || !$session['cart_id']) {
            return [];
        }

        $query = "
            SELECT 
                ci.id as cart_item_id,
                ci.cartId,
                ci.productId,
                ci.variantId,
                ci.quantity,
                ci.createdAt,
                ci.updatedAt,
                p.title as product_title,
                p.mainImage as product_image,
                p.price as product_price,
                p.slug as product_slug
            FROM " . TABLE_CART_ITEM . " ci
            JOIN " . TABLE_PRODUCT . " p ON ci.productId = p.id
            WHERE ci.cartId = %i
            ORDER BY ci.createdAt ASC
        ";

        return DB::query($query, $session['cart_id']);
    }

    /**
     * Unlink session from user (e.g. on logout)
     * @param string $sessionId Database ID of the session
     * @param string $cookieToken The client-side cookie token
     * @param int $userId Database ID of the user
     * @return void
     */

    public function unlinkSessionFromUser(string $sessionId, string $cookieToken, int $userId): void
    {
        

        DB::update($this->table, ['user_id' => null], "id = %i AND user_id = %i", $sessionId, $userId);
    }

    /** */
}