<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\SessionRepository;
use App\Repository\ProductRepository;

class SessionController
{
    private $sessionRepository;
    private $productRepository;

    public function __construct(
        SessionRepository $sessionRepository,
        ProductRepository $productRepository
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Start a new anonymous session
     * 
     * @Route POST /api/sessions/start
     */
    public function startNewSession(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            // Validate required fields
            if (!isset($data['cookie_token']) || empty($data['cookie_token'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'cookie_token is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Get IP address from request
            $ipAddress = $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';

            // Get user agent from request
            $userAgent = $request->getServerParams()['HTTP_USER_AGENT'] ?? null;

            // Start new session
            $session = $this->sessionRepository->startNewAnonymousSession(
                $data['cookie_token'],
                $ipAddress,
                $userAgent
            );

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Session started successfully',
                'data' => $session
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to start session: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Retrieve session by cookie token and session ID
     * 
     * @Route GET /api/sessions/verify
     */
    public function verifySession(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            // Validate required fields
            if (
                !isset($data['cookie_token']) || empty($data['cookie_token']) ||
                !isset($data['session_id']) || empty($data['session_id'])
            ) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'cookie_token and session_id are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Retrieve session
            $session = $this->sessionRepository->retrieveSessionByCookieAndSessionId(
                $data['cookie_token'],
                $data['session_id']
            );

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found or invalid'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Session verified successfully',
                'data' => $session
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to verify session: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }


    /**
     * Get cart items for current session
     * 
     * @Route GET /api/sessions/{sessionId}/cart
     */

    public function getCartItems(Request $request, Response $response, array $args): Response
    {
        try {
            // Get session_id from request body
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Find session by session_id (GUID) or ID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
                if ($session) {
                    $sessionId = $session['id'];
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Session not found'
                    ]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Get cart items
            $cartItems = $this->sessionRepository->getCartItemsForSession((int) $sessionId);
            $result = [];
            foreach ($cartItems as $cartItem) {
                $cartItem['product'] = $this->productRepository->find((int) $cartItem['productId']);
                $cartItem['variant'] = $this->productRepository->getProductVariantByVariantId((int) $cartItem['variantId']);
                $result[] = $cartItem;
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result,
                'count' => count($cartItems)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve cart items: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Add product to cart in session
     * 
     * @Route POST /api/sessions/{sessionId}/cart/add
     */

    public function addProductToCart(Request $request, Response $response, array $args): Response
    {
        try {
            // Get session_id from request body instead of route parameter
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            // Validate session_id
            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // If session_id is a GUID (string), find the session by session_id field
            // If it's an integer, find by ID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                // Find session by session_id field (GUID)
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
                if ($session) {
                    $sessionId = $session['id']; // Get the numeric ID for further operations
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Session not found'
                    ]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Validate required fields
            if (!isset($data['product_id']) || !isset($data['variant_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'product_id and variant_id are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $quantity = $data['quantity'] ?? 1;

            // Verify product exists
            $product = $this->productRepository->find($data['product_id']);
            if (!$product) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Product not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Add product to cart
            $cartItem = $this->sessionRepository->addProductToCartInSession(
                (int) $sessionId, // Use the numeric ID here
                (int) $data['product_id'],
                (int) $data['variant_id'],
                (int) $quantity
            );

            if (!$cartItem) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to add product to cart'
                ]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'data' => $cartItem
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to add product to cart: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }



    /**
     * Increase product quantity in cart
     * 
     * @Route POST /api/sessions/{sessionId}/cart/increase
     */
    public function increaseProductQuantity(Request $request, Response $response, array $args): Response
    {
        try {
            // Get session_id from request body instead of route parameter
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            // Validate session_id
            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // If session_id is a GUID (string), find the session by session_id field
            // If it's an integer, find by ID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                // Find session by session_id field (GUID)
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
                if ($session) {
                    $sessionId = $session['id']; // Get the numeric ID for further operations
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Session not found'
                    ]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Validate required fields
            if (!isset($data['product_id']) || !isset($data['variant_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'product_id and variant_id are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $increment = $data['increment'] ?? 1;

            // Increase quantity
            $cartItem = $this->sessionRepository->increaseProductQuantity(
                (int) $sessionId,
                (int) $data['product_id'],
                (int) $data['variant_id'],
                (int) $increment
            );

            if (!$cartItem) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Product not found in cart'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Product quantity increased successfully',
                'data' => $cartItem
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to increase product quantity: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Decrease product quantity in cart
     * 
     * @Route POST /api/sessions/{sessionId}/cart/decrease
     */

    public function decreaseProductQuantity(Request $request, Response $response, array $args): Response
    {
        try {
            // Get session_id from request body instead of route parameter
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            // Validate session_id
            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // If session_id is a GUID (string), find the session by session_id field
            // If it's an integer, find by ID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                // Find session by session_id field (GUID)
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
                if ($session) {
                    $sessionId = $session['id']; // Get the numeric ID for further operations
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Session not found'
                    ]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Validate required fields
            if (!isset($data['product_id']) || !isset($data['variant_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'product_id and variant_id are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $decrement = $data['decrement'] ?? 1;

            // Decrease quantity
            $cartItem = $this->sessionRepository->decreaseProductQuantity(
                (int) $sessionId,
                (int) $data['product_id'],
                (int) $data['variant_id'],
                (int) $decrement
            );

            if ($cartItem === null) {
                // Item was removed because quantity reached 0
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Product removed from cart (quantity reached 0)',
                    'data' => null
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Product quantity decreased successfully',
                    'data' => $cartItem
                ]));
            }

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to decrease product quantity: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Remove product from cart
     * 
     * @Route DELETE /api/sessions/{sessionId}/cart/remove
     */

    public function removeProductFromCart(Request $request, Response $response, array $args): Response
    {
        try {
            // Get session_id from request body instead of route parameter
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            // Validate session_id
            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // If session_id is a GUID (string), find the session by session_id field
            // If it's an integer, find by ID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                // Find session by session_id field (GUID)
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
                if ($session) {
                    $sessionId = $session['id']; // Get the numeric ID for further operations
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Session not found'
                    ]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Validate required fields
            if (!isset($data['product_id']) || !isset($data['variant_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'product_id and variant_id are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Remove product
            $success = $this->sessionRepository->removeProductFromCartInSession(
                (int) $sessionId,
                (int) $data['product_id'],
                (int) $data['variant_id']
            );

            if (!$success) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Product not found in cart'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Product removed from cart successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to remove product from cart: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Convert session cart to order
     * 
     * @Route POST /api/sessions/{sessionId}/checkout
     */

    public function checkoutSessionCart(Request $request, Response $response): Response
    {
        try {
            // Get session_id from request body instead of route parameter
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            // Validate session_id
            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // If session_id is a GUID (string), find the session by session_id field
            // If it's an integer, find by ID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                // Find session by session_id field (GUID)
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
                if ($session) {
                    $sessionId = $session['id']; // Get the numeric ID for further operations
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Session not found'
                    ]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Validate required fields for customer information
            $requiredFields = ['fullname', 'email', 'phone'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => "{$field} is required"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            // Optional fields
            $gender = $data['gender'] ?? null;
            $dateOfBirth = $data['date_of_birth'] ?? null;
            $branchId = isset($data['branch_id']) ? (int) $data['branch_id'] : null;

            // Convert session cart to order
            $order = $this->sessionRepository->convertSessionCartToOrderCart(
                (int) $sessionId,
                $data['fullname'],
                $data['email'],
                $data['phone'],
                $gender,
                $dateOfBirth,
                $branchId
            );

            if (!$order) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to create order. Cart may be empty or session invalid.'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to process checkout: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get active sessions (admin only)
     * 
     * @Route GET /api/sessions/active
     */
    public function getActiveSessions(Request $request, Response $response): Response
    {
        try {
            // Optional: Check if user is admin
            // $currentUser = $request->getAttribute('user');
            // if ($currentUser['role'] !== 'admin') {
            //     $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            //     return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            // }

            // Get optional query parameters
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int) $queryParams['offset'] : null;

            // Get active sessions
            $sessions = $this->sessionRepository->findBy(
                ['is_active' => 1],
                ['last_activity' => 'DESC'],
                $limit,
                $offset
            );

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $sessions,
                'count' => count($sessions)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve active sessions: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get session by ID
     * 
     * @Route POST /api/sessions
     */
    
    public function getSessionById(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $session_id = $data['session_id'] ?? null;

            if (empty($session_id)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'session_id is required in request body'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Handle both numeric ID and GUID
            if (is_numeric($session_id) && (int) $session_id == $session_id) {
                $sessionId = (int) $session_id;
                $session = $this->sessionRepository->find($sessionId);
            } else {
                // Find by session_id field (GUID)
                $session = $this->sessionRepository->findOneBy(['session_id' => $session_id]);
            }

            if (!$session) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Session not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $session
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve session: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}