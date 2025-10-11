<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class CustomerRepository extends BaseRepository
{
    protected $table = 'customer';
    protected $primaryKey = 'id';

    /**
     * Find customer by email
     */
    public function findByEmail(string $email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Search customers by name or email
     */
    public function search(string $query): array
    {
        $sql = "
            SELECT * 
            FROM `customer` 
            WHERE 
                fullname LIKE %s OR 
                email LIKE %s OR 
                phone LIKE %s
            ORDER BY fullname
        ";

        $searchTerm = "%{$query}%";
        return DB::query($sql, $searchTerm, $searchTerm, $searchTerm);
    }

    /**
     * Find customers by city
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city], ['fullname' => 'ASC']);
    }

    /**
     * Find customers by country
     */
    public function findByCountry(string $country): array
    {
        return $this->findBy(['country' => $country], ['city' => 'ASC', 'fullname' => 'ASC']);
    }

    /**
     * Find customers without user accounts (guest customers)
     */
    public function findGuestCustomers(): array
    {
        return $this->findBy(['user_id' => null], ['createdAt' => 'DESC']);
    }

    /**
     * Find customers with user accounts
     */
    public function findRegisteredCustomers(): array
    {
        $sql = "
            SELECT c.*, u.role as user_role 
            FROM `customer` c 
            INNER JOIN `user` u ON c.user_id = u.id 
            ORDER BY c.fullname
        ";

        return DB::query($sql);
    }

    /**
     * Update customer's user_id reference
     */
    public function updateUserId(int $customerId, ?int $userId): bool
    {
        return $this->update($customerId, ['user_id' => $userId]) ? true : false;
    }

    /**
     * Get customer statistics
     */
    public function getStatistics(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_customers,
                SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as registered_customers,
                SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_customers,
                COUNT(DISTINCT country) as countries_count,
                COUNT(DISTINCT city) as cities_count
            FROM `customer`
        ";

        return DB::queryFirstRow($sql);
    }

    /**
     * Get customers with their order count
     */
    public function findCustomersWithOrderCount(): array
    {
        $sql = "
            SELECT 
                c.*,
                COUNT(o.id) as order_count,
                COALESCE(SUM(o.total), 0) as total_spent
            FROM `customer` c
            LEFT JOIN `order` o ON c.id = o.customer_id
            GROUP BY c.id
            ORDER BY total_spent DESC, order_count DESC
        ";

        return DB::query($sql);
    }

    public function findCustomerByUserId(int $userId): array
    {
        return $this->findOneBy(['user_id' => $userId]);
    }

    /**
     * Get customers with search, pagination, and comprehensive order data
     * Supports filtering by: fullname, email, phone, total orders, first/last order dates
     */
    public function getCustomersWithSearchAndPaginated(array $filters = [], array $sort = [], int $page = 1, int $perPage = 10): array
    {
        // Build the base query with LEFT JOINs to include customers without orders
        $sql = "
        SELECT 
            c.id,
            c.fullname,
            u.email,
            u.phone,
            c.gender,
            c.date_of_birth,
            c.createdAt as customer_since,
            u.role as user_role,
            u.createdAt as user_created_at,
            COUNT(DISTINCT o.id) as total_orders,
            COALESCE(SUM(o.total), 0) as total_revenue,
            MIN(o.dateTime) as first_ordered_at,
            MAX(o.dateTime) as last_ordered_at,
            CASE 
                WHEN u.id IS NOT NULL THEN 'Registered'
                ELSE 'Guest'
            END as customer_type,
            COUNT(DISTINCT w.id) as total_favourites
        FROM `customer` c
        LEFT JOIN `user` u ON c.user_id = u.id
        LEFT JOIN `order` o ON c.id = o.customer_id
        LEFT JOIN `wishlist` w ON u.id = w.userId
    ";

        $whereConditions = [];
        $params = [];

        // Apply search filters
        if (!empty($filters['search'])) {
            $searchTerm = "%{$filters['search']}%";
            $whereConditions[] = "(c.fullname LIKE %s OR u.email LIKE %s OR u.phone LIKE %s)";
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
        }

        if (!empty($filters['fullname'])) {
            $whereConditions[] = "c.fullname LIKE %s";
            $params[] = "%{$filters['fullname']}%";
        }

        if (!empty($filters['email'])) {
            $whereConditions[] = "u.email LIKE %s";
            $params[] = "%{$filters['email']}%";
        }

        if (!empty($filters['phone'])) {
            $whereConditions[] = "u.phone LIKE %s";
            $params[] = "%{$filters['phone']}%";
        }

        if (!empty($filters['min_orders'])) {
            $whereConditions[] = "COUNT(DISTINCT o.id) >= %i";
            $params[] = (int) $filters['min_orders'];
        }

        if (!empty($filters['max_orders'])) {
            $whereConditions[] = "COUNT(DISTINCT o.id) <= %i";
            $params[] = (int) $filters['max_orders'];
        }

        if (!empty($filters['min_revenue'])) {
            $whereConditions[] = "COALESCE(SUM(o.total), 0) >= %f";
            $params[] = (float) $filters['min_revenue'];
        }

        if (!empty($filters['max_revenue'])) {
            $whereConditions[] = "COALESCE(SUM(o.total), 0) <= %f";
            $params[] = (float) $filters['max_revenue'];
        }

        if (!empty($filters['date_from'])) {
            $whereConditions[] = "c.createdAt >= %s";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "c.createdAt <= %s";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['customer_type'])) {
            if ($filters['customer_type'] === 'registered') {
                $whereConditions[] = "u.id IS NOT NULL";
            } elseif ($filters['customer_type'] === 'guest') {
                $whereConditions[] = "u.id IS NULL";
            }
        }

        // Add WHERE clause if conditions exist
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }

        // Add GROUP BY
        $sql .= " GROUP BY c.id, c.fullname, u.email, u.phone, c.gender, c.date_of_birth, c.createdAt, u.role, u.createdAt";

        // Apply sorting
        $orderBy = [];
        if (!empty($sort['field'])) {
            $direction = strtoupper($sort['direction'] ?? 'ASC');
            $allowedFields = [
                'fullname',
                'email',
                'phone',
                'total_orders',
                'total_revenue',
                'first_ordered_at',
                'last_ordered_at',
                'customer_since'
            ];

            if (in_array($sort['field'], $allowedFields)) {
                $orderBy[] = "{$sort['field']} {$direction}";
            }
        }

        // Default sorting if none specified
        if (empty($orderBy)) {
            $orderBy[] = "c.createdAt DESC";
        }

        $sql .= " ORDER BY " . implode(', ', $orderBy);

        // Add pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT %i OFFSET %i";

        $params2 = [...$params, $perPage, $offset];
        // Execute the main query
        $customers = DB::query($sql, ...$params2);

        // Get total count for pagination
        $countSql = "
        SELECT COUNT(DISTINCT c.id) as total
        FROM `customer` c
        LEFT JOIN `user` u ON c.user_id = u.id
        LEFT JOIN `order` o ON c.id = o.customer_id
    ";

        if (!empty($whereConditions)) {
            $countSql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        $totalResult = DB::queryFirstRow($countSql, ...$params);
        $total = $totalResult['total'] ?? 0;

        return [
            'customers' => $customers,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }

    /**
     * Get customer details with comprehensive order history
     */
    public function getCustomerDetails(int $customerId): ?array
    {
        $sql = "
        SELECT 
            c.*,
            u.email as user_email,
            u.role as user_role,
            u.phone_verified,
            u.email_verified,
            u.phone,
            u.createdAt as user_created_at,
            COUNT(DISTINCT o.id) as total_orders,
            COALESCE(SUM(o.total), 0) as total_spent,
            MIN(o.dateTime) as first_order_date,
            MAX(o.dateTime) as last_order_date,
            AVG(o.total) as average_order_value,
            COUNT(DISTINCT w.id) as total_favourites
        FROM `customer` c
        LEFT JOIN `user` u ON c.user_id = u.id
        LEFT JOIN `order` o ON c.id = o.customer_id
        LEFT JOIN `wishlist` w ON u.id = w.userId
        WHERE c.id = %i
        GROUP BY c.id, c.user_id, u.email, u.role, u.phone_verified, u.email_verified, u.phone, u.createdAt
    ";

        $customer = DB::queryFirstRow($sql, $customerId);

        if (!$customer) {
            return null;
        }

        // Get recent orders
        $recentOrdersSql = "
        SELECT 
            o.id,
            o.order_number,
            o.dateTime,
            o.status,
            o.total,
            b.name as branch_name
        FROM `order` o
        LEFT JOIN `branch` b ON o.branch_id = b.id
        WHERE o.customer_id = %i
        ORDER BY o.dateTime DESC
        LIMIT 10
    ";
        $orderItemRepository = new OrderItemRepository();
        $productRepository = new ProductRepository();

        $recentOrders = DB::query($recentOrdersSql, $customerId);
        $orders = [];
        foreach ($recentOrders as $order) {
            $items = $orderItemRepository->findByOrderId((int) $order['id']);
            $orderItems = [];
            foreach ($items as $item) {
                $item['product'] = $productRepository->findById((int) $item['productId']);
                $item['variant'] = $productRepository->getProductVariantByVariantId((int) $item['variantId']);
                $orderItems[] = $item;
            }
            $order['items'] = $orderItems;
            $orders[] = $order;
        }

        $customer['recent_orders'] = $orders;

        // Get favorite products with product details
        $favoritesSql = "
        SELECT 
            p.id,
            p.slug,
            p.title,
            p.mainImage,
            p.price,
            p.discountType,
            p.discountValue,
            p.originalPrice,
            p.discountStartDate,
            p.discountEndDate,
            p.rating,
            p.description,
            p.manufacturer,
            p.inStock,
            p.categoryId
        FROM `wishlist` w
        LEFT JOIN `product` p ON w.productId = p.id
        LEFT JOIN `user` u ON w.userId = u.id
        LEFT JOIN `customer` c ON c.user_id = u.id
        WHERE c.id = %i
        ORDER BY w.id DESC
        LIMIT 10
    ";

        $favoriteProducts = DB::query($favoritesSql, $customerId);
        $favorites = [];
        foreach ($favoriteProducts as $product) {
            // Get variants for each favorite product
          

            $mediaRepository = new MediaRepository();
            $product['variants'] = $productRepository->getProductVariantByProductId((int) $product['id']);
            $product['media'] = $mediaRepository->findMediaByProductId((int) $product['id']);
            $favorites[] = $product;
        }

        $customer['favourites'] = $favorites;

        return $customer;
    }
}