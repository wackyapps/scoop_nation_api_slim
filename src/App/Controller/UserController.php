<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\AddressRepository;
use App\Repository\SessionRepository;
use App\Services\EmailService;
use App\Services\OtpService;
use App\Services\Authentication\JWT;
use DB;

class UserController
{
    private $userRepository;
    private $wishlistRepository;
    private $addressRepository;
    private $sessionRepository;
    private $emailService;
    private $otpService;
    private $orderRepository;
    private $customerRepository;
    private $orderItemRepository;
    private $productRepository;

    public function __construct(
        UserRepository $userRepository,
        WishlistRepository $wishlistRepository,
        AddressRepository $addressRepository,
        SessionRepository $sessionRepository,
        EmailService $emailService,
        OtpService $otpService,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository
    ) {
        $this->userRepository = $userRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->addressRepository = $addressRepository;
        $this->sessionRepository = $sessionRepository;
        $this->emailService = $emailService;
        $this->otpService = $otpService;    
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get all users with their customer profiles
     * 
     * @Route GET /api/users
     */
    public function getAllUsers(Request $request, Response $response): Response
    {
        try {
            // Get optional query parameters for pagination and sorting
            $queryParams = $request->getQueryParams();
            $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'] : null;
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int) $queryParams['offset'] : null;

            $users = $this->userRepository->findAllWithProfiles($orderBy, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $users,
                'count' => count($users)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve users: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get a single user by ID with customer profile
     * 
     * @Route GET /api/users/by-id?id={id}
     */
    public function getUserById(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (!isset($queryParams['id']) || empty($queryParams['id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $userId = (int) $queryParams['id'];

            $user = $this->userRepository->findUserWithCustomerProfile($userId);

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve user: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get user by email with customer profile
     * 
     * @Route GET /api/users/email?email={email}
     */
    public function getUserByEmail(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (!isset($queryParams['email']) || empty($queryParams['email'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $email = urldecode($queryParams['email']);

            $user = $this->userRepository->findByEmailWithProfile($email);

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve user: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get users by role
     * 
     * @Route GET /api/users/role?role={role}
     */
    public function getUsersByRole(Request $request, Response $response): Response
    {
        try {
            // Get query parameters
            $queryParams = $request->getQueryParams();
            
            if (!isset($queryParams['role']) || empty($queryParams['role'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Role is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $role = $queryParams['role'];
            $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'] : null;
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int) $queryParams['offset'] : null;

            $users = $this->userRepository->findByRole($role, $orderBy, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $users,
                'count' => count($users)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve users by role: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get guest customers (customers without user accounts)
     * 
     * @Route GET /api/users/guests
     */
    public function getGuestCustomers(Request $request, Response $response): Response
    {
        try {
            $guestCustomers = $this->userRepository->findGuestCustomers();

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $guestCustomers,
                'count' => count($guestCustomers)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve guest customers: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Link customer to user account
     * 
     * @Route POST /api/users/link-customer?id={id}&customerId={customerId}
     */
    public function linkCustomerToUser(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            // Validate required parameters
            if (!isset($queryParams['id']) || empty($queryParams['id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            if (!isset($queryParams['customerId']) || empty($queryParams['customerId'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer ID is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $userId = (int) $queryParams['id'];
            $customerId = (int) $queryParams['customerId'];

            // Link customer to user
            $success = $this->userRepository->linkCustomerToUser($userId, $customerId);

            if (!$success) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to link customer to user'
                ]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Customer linked to user successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to link customer: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Register a new customer user
     * 
     * @Route POST /api/users/register-customer
     */
    public function registerCustomerUser(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            // Validate required fields for user and customer
            $requiredUser = ['email', 'password'];
            $requiredCustomer = ['fullname', 'phone'];
            foreach (array_merge($requiredUser, $requiredCustomer) as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'error' => "Field {$field} is required"]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            // Check if user already exists
            if ($this->userRepository->findByEmail($data['email'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'User with this email already exists']));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json');

            }

            // Prepare user data
            $userData = [
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'phone' => $data['phone'] ?? null,
                'role' => 'customer' // Automatically set to customer
            ];

            // Prepare customer data
            $customerData = [
                'fullname' => $data['fullname'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
            ];

            $userId = $this->userRepository->registerCustomerUser($userData, $customerData);

            // Generate and send verification email
            $token = $this->userRepository->generateEmailVerificationToken((int)$userId);
            $this->emailService->sendCustomerVerificationEmail(
                $data['email'],
                $token,
                $customerData['fullname']
            );
            $this->emailService->sendAdminNewCustomerNotification((int)$userId, );

            $response->getBody()->write(json_encode([
                'success' => true, 
                'message' => 'Registration successful. Please check your email to verify your account.',
                'user_id' => $userId
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to register customer user: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');

        }
    }

    /**
     * Login admin user
     * 
     * @Route POST /api/users/login-admin
     */
    public function loginAdminUser(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            if (!isset($data['email']) || !isset($data['password'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Email and password are required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $user = $this->userRepository->loginAdminUser($data['email'], $data['password']);


            if (!$user) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid email or password']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            if (isset($user["success"]) && $user["success"] == false) {
                $response->getBody()->write(json_encode($user));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            /**
             * Check if email is verified
             */
            if (!$user['email_verified']) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Please verify your email address before logging in',
                    'code' => 'EMAIL_NOT_VERIFIED'
                ]));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }

            // Generate token (assuming you have a method for this)
            $jwt = new JWT();
            $token = $jwt->generate($user);

            $response->getBody()->write(json_encode(['success' => true, 'token' => $token, 'user' => $user]));
            return $response->withHeader('Content-Type', 'application/json');


        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to login: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Login customer user
     * 
     * @Route POST /api/users/login-customer
     */
    public function loginCustomerUser(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            /**
             * Validate email and password
             */
            if (!isset($data['email']) || !isset($data['password'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Email and password are required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            /**
             * Authenticate user as customer role type
             */

            $user = $this->userRepository->loginCustomerUser($data['email'], $data['password']);

            /**
             * Authentication Failed - Invalid email or password
             */

            if (!$user) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid email or password']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            /**
             * Authentication Failed - User found is not customer role user
             */

            if (isset($user["success"]) && $user["success"] == false) {
                $response->getBody()->write(json_encode(['success' => $user["success"], 'error' => $user["error"]]));
                // $response->getBody()->write(json_encode($user));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            /**
             * Check if email is verified
             */
            if (!$user['email_verified']) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Please verify your email address before logging in',
                    'code' => 'EMAIL_NOT_VERIFIED'
                ]));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }

            /**
             * If user is found and in request there is session_id, cookie_token are found then link session cart to logged in user
             * using sessionRepository
             */

            $sessionId = $data['session_id'] ?? null;
            $cookieToken = $data['cookie_token'] ?? null;
            $businessId = isset($data['business_id']) ? (int) $data['business_id'] : null;
            $branchId = isset($data['branch_id']) ? (int) $data['branch_id'] : null;

            if ($sessionId && $cookieToken && $user['id']) {
                $this->sessionRepository->linkSessionCartToUser($sessionId, $cookieToken, $user['id'], $businessId, $branchId);
            }


            // Generate token (assuming you have a method for this)
            $jwt = new JWT();
            $token = $jwt->generate($user);

            $response->getBody()->write(json_encode(['success' => true, 'token' => $token, 'user' => $user]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to login: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    



    /**
     * Register user with specific role (admin/rider)
     * 
     * @Route POST /api/users/register-with-role
     */
    public function registerUserWithRole(Request $request, Response $response): Response
    {
        try {
            // Verify if the current user is admin (assuming auth middleware sets 'user')
            $currentUser = $request->getAttribute('user');
            var_dump($currentUser);
            if ($currentUser['role'] !== 'admin') {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }

            $data = $request->getParsedBody();

            // Validate required fields
            $required = ['email', 'password', 'role'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'error' => "Field '{$field}' is required"]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            if (!in_array($data['role'], ['admin', 'rider'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid role']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Check if user already exists
            if ($this->userRepository->findByEmail($data['email'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'User with this email already exists']));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
            }

            // Prepare user data
            $userData = [
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'phone' => $data['phone'] ?? null,
                'role' => $data['role']
            ];

            $userId = $this->userRepository->registerUserWithRole($userData, $data['role']);

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'User registered successfully', 'user_id' => $userId]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to register user: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Handle forgot password
     * 
     * @Route POST /api/users/forgot-password
     */
    public function forgotUserPassword(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            if (!isset($data['email']) || empty($data['email'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Email is required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Generate password reset token
            $result = $this->userRepository->generatePasswordResetToken($data['email']);

            // Handle rate limiting errors
            if (!$result['success'] && isset($result['error']) && $result['error'] === 'RATE_LIMIT_EXCEEDED') {
                $response->getBody()->write(json_encode([
                    'success' => false, 
                    'error' => 'Too many password reset requests. Please try again later.',
                    'code' => 'RATE_LIMIT_EXCEEDED'
                ]));
                return $response->withStatus(429)->withHeader('Content-Type', 'application/json');
            }

            // If token was generated successfully, send the email
            if ($result['success'] && $result['token']) {
                // Get user details for email
                $user = $this->userRepository->findByEmail($data['email']);
                
                if ($user) {
                    // Get user name from customer profile or use email
                    $userName = $user['email'];
                    if (isset($user['customer_fullname'])) {
                        $userName = $user['customer_fullname'];
                    }
                    
                    // Send password reset email notification to customer
                    $this->emailService->sendPasswordResetRequestEmail(
                        $data['email'],
                        $result['token'],
                        $userName
                    );
                }
            }

            // Always return generic success message to prevent email enumeration
            $response->getBody()->write(json_encode([
                'success' => true, 
                'message' => 'If an account exists with this email, you will receive password reset instructions.'
            ]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error in forgotUserPassword: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'success' => false, 
                'error' => 'Failed to process request'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }


    public function getAddressOfUser(Request $request, Response $response): Response
    {
        try {
            $userId = (int) $request->getAttribute('user')['id'];
            $addressResitory = new AddressRepository();
            $addresses = $addressResitory->listAllAddressesByUserId($userId);



            $response->getBody()->write(json_encode(['success' => true, 'data' => $addresses]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to get addresses: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    /**
     * Add new address for user
     * 
     * @Route POST /api/users/add-address
     */
    public function addNewAddress(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $userId = (int) $request->getAttribute('user')['id'];
            // Get user_id from request body
            if (!$userId) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'user_id is required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            // Validate required fields
            $required = ['street_address', 'city', 'state', 'country', 'longitude', 'latitude'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'error' => "Field {$field} is required"]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            // Get customer ID for the user
            $user = $this->userRepository->findUserWithCustomerProfile($userId);
            if (!$user || !$user["id"]) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Customer profile not found for user']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $addressData = [
                'user_id' => (int) $user["id"],
                'address_type' => $data['address_type'] ?? 'home',
                'street_address' => $data['street_address'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'country' => $data['country'],
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude'],
                'is_default' => $data['is_default'] ?? false
            ];
            if (!empty($data['postal_code'])) {
                $addressData['postal_code'] = $data['postal_code'];
            }

            // var_dump($addressData);

            $addressRepository = new AddressRepository();
            $addressId = $addressRepository->save($addressData);

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Address added successfully', 'address_id' => $addressId]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to add address: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Remove address for user
     * 
     * @Route POST /api/users/remove-address
     */
    public function removeAddress(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $userId = (int) $request->getAttribute('user')['id'];
            // Validate required fields
            if (!$userId) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'user_id is required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (!isset($data['id']) || empty($data['id'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'id (address ID) is required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $addressId = (int) $data['id'];

            // Get customer ID for the user
            $user = $this->userRepository->findUserWithCustomerProfile($userId);


            // Check if user exists and has customer_id (as array, not object)
            if (!$user || !isset($user['id']) || empty($user['id'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Customer profile not found for user']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $addressRepository = new AddressRepository();

            // Check the actual column name in your address table
            // Common alternatives: 'customer_id', 'customerId', 'user_id', 'userId'
            $customerId = $user['id'];

            // Try to find the address with the correct column name
            // Replace 'customer_id' with the actual column name from your table
            $address = $addressRepository->findOneBy([
                'id' => $addressId,
                'user_id' => $customerId  // Change this to match your actual column name
            ]);

            if (!$address) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Address not found or does not belong to user']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $success = $addressRepository->delete($addressId);

            $response->getBody()->write(json_encode(['success' => $success, 'message' => $success ? 'Address removed successfully' : 'Failed to remove address']));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to remove address: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get favorites for user
     * 
     * @Route POST /api/users/favorites
     */
    public function getFavorites(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $page = max(1, (int)($queryParams['page'] ?? 1));
            $perPage = min(100, max(1, (int)($queryParams['limit'] ?? 10)));
            $userId = (int) $request->getAttribute('user')['id'];
            $wishlistRepository = new WishlistRepository();
            $favoritesResponse  = $wishlistRepository->getAllFavorites($userId,$page,$perPage);
            $favorites = $favoritesResponse['data'];
            $total = $favoritesResponse['total'];
            $totalPages = $favoritesResponse['total_pages'];
            $productRepository = new ProductRepository();
            
            $result = [];

            foreach ($favorites as $favorite) {
                $productId = (int) $favorite['productId'];
                $product = $productRepository->findOneBy(['id' => $productId]);
                $product['variants'] = $productRepository->getProductVariantByProductId($productId);
                $result[] = [
                    'id' => $favorite['id'],
                    'user_id' => $favorite['userId'],
                    'productId' => $favorite['productId'],
                    'product' => $product
                ];
            }

            $response->getBody()->write(json_encode(['success' => true, 'data' => [
                "favorites"=>$result,
                'pagination'=>[
                    'total'=> $total,
                    'page'=> $page,
                    'limit'=> $perPage,
                    'total_pages'=> $totalPages
                ]
            ]]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to get favorites: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Add product to favorite
     * 
     * @Route POST /api/users/{userId}/favorites/add/{productId}
     */
    public function addProductToFavorite(Request $request, Response $response): Response
    {
        try {

            $data = $request->getParsedBody();

            $userId = (int) $request->getAttribute('user')['id'];
            $productId = (int) $data['product_id'];

            $wishlistRepository = new WishlistRepository();
            $favoriteId = $wishlistRepository->addProductToFavorite($userId, $productId);

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Product added to favorites', 'favorite_id' => $favoriteId]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);


        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to add favorite: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Remove product from favorite
     * 
     * @Route POST /api/users/favorites/remove
     */
    public function removeProductFromFavorite(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $userId = (int) $request->getAttribute('user')['id'];

            // Validate required fields
            if (!$userId || !isset($data['product_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'user_id and product_id are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $productId = (int) $data['product_id'];

            $wishlistRepository = new WishlistRepository();
            $success = $wishlistRepository->removeProductFromFavorite($userId, $productId);

            $response->getBody()->write(json_encode([
                'success' => $success,
                'message' => $success ? 'Product removed from favorites' : 'Failed to remove product from favorites'
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to remove favorite: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Save profile updates
     * 
     * @Route PUT /api/users/{id}/profile
     */
    public function saveProfile(Request $request, Response $response, int $userId): Response
    {
        try {
            $userEmail = $request->getAttribute('user')['email'];

            $data = $request->getParsedBody();
            // Prepare user data
            $userData = array_intersect_key($data, array_flip(['email', 'phone']));

            // Prepare customer data
            $customerData = array_intersect_key($data, array_flip(['fullname', 'gender', 'date_of_birth']));

            $success = $this->userRepository->saveProfile($userId, $userData, $customerData);
            $user = $this->userRepository->findByEmailWithProfile($userEmail);

            // remove password field from $user
            if (isset($user['password'])) {
                unset($user['password']);
            }

            $response->getBody()->write(json_encode(['success' => $success, 'message' => $success ? 'Profile updated successfully' : 'No changes made', 'user' => $user]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to update profile: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get user orders
     * 
     * @Route GET /api/users/{id}/orders
     */
    public function getUserOrders(Request $request, Response $response): Response
    {
        try {
            $userId = (int) $request->getAttribute('user')['id'];
            $queryParams = $request->getQueryParams();
            $page = max(1, (int)($queryParams['page'] ?? 1));
            $perPage = min(100, max(1, (int)($queryParams['limit'] ?? 10)));
            $customer = $this->customerRepository->findCustomerByUserId($userId);
            if (!$customer) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Customer not found']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            $customerId = (int) $customer['id'];
            $orderResponse = $this->orderRepository->findByCustomerId($customerId, $page, $perPage);
            $orders = $orderResponse['orders'];
            $total = $orderResponse['total'];
            $totalPages = $orderResponse['total_pages'];
            $result = [];

            foreach ($orders as $order) {
                $orderItems = $this->orderItemRepository->findByOrderId( (int) $order['id']);
                $items = [];
                // decoding product and variant from json string
                foreach ($orderItems as $item) {
                    $item2 = [...$item];
                    $item2['product'] = json_decode($item['product'] ?? '{}', true);
                    $item2['variant'] = json_decode($item['variant'] ?? '{}', true);
                    $items[] = $item2;
                }
                $order['items'] = $items;
                 // decode customer_address from json string
                $order['customer_address'] = json_decode($order['customer_address'] ?? '{}', true);
                $result[] = $order;
            }
            $response->getBody()->write(json_encode(['success' => true, 'data' => [
                "orders"=>$result,
                'pagination'=>[
                    'total'=> $total,
                    'page'=> $page,
                    'limit'=> $perPage,
                    'total_pages'=> $totalPages
                ]
            ]]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to get orders: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Verify email address
     * 
     * @Route GET /api/users/verify-email
     */
    public function verifyEmail(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $token = $queryParams['token'] ?? null;

            if (!$token) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Verification token is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->userRepository->verifyEmailWithToken($token);

            if (!$result) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid or expired verification token',
                    'code' => 'TOKEN_INVALID'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // TODO:Hamza need to send a welcome email after verification
            // $this->emailService->sendEmail(
            // '',
            // '',
            // '',
            // []
            // );
            // TO DO:Hamza need to send a email to admin notifying new user registration
            // $this->emailService->sendEmail(
            // '',
            // '',
            // '',
            // []
            // );
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Email verified successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error in verifyEmail: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Resend verification email
     * 
     * @Route POST /api/users/resend-verification
     */
    public function resendVerificationEmail(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? null;

            if (!$email) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Check if user exists first
            $user = $this->userRepository->findByEmail($email);
            
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User not found',
                    'code' => 'USER_NOT_FOUND'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Check if already verified
            if ($user['email_verified'] == 1) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email address is already verified',
                    'code' => 'ALREADY_VERIFIED'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->userRepository->resendVerificationEmail($email);

            if (!$result) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to send verification email'
                ]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Verification email sent successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error in resendVerificationEmail: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Reset password with token
     * 
     * @Route POST /api/users/reset-password
     */
    public function resetPassword(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $token = $data['token'] ?? null;
            $newPassword = $data['password'] ?? null;

            if (!$token || !$newPassword) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Token and password are required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Validate password length before attempting reset
            if (strlen($newPassword) < 8) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Password must be at least 8 characters',
                    'code' => 'WEAK_PASSWORD'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Validate token and get user data before resetting
            $user = $this->userRepository->validatePasswordResetToken($token);

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid or expired reset token',
                    'code' => 'TOKEN_INVALID'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Reset the password
            $result = $this->userRepository->resetPasswordWithToken($token, $newPassword);

            if (!$result) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to reset password'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Get user name for confirmation email
            $userName = $user['email'];
            $sql = "SELECT c.fullname FROM " . TABLE_CUSTOMER . " c WHERE c.user_id = %i";
            $customer = DB::queryFirstRow($sql, $user['id']);
            if ($customer && !empty($customer['fullname'])) {
                $userName = $customer['fullname'];
            }

            // Send confirmation email
            $this->emailService->sendPasswordResetConfirmationEmail(
                $user['email'],
                $userName
            );

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Password reset successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error in resetPassword: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Validate reset token
     * 
     * @Route GET /api/users/validate-reset-token
     */
    public function validateResetToken(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $token = $queryParams['token'] ?? null;

            if (!$token) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Token is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $user = $this->userRepository->validatePasswordResetToken($token);

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Invalid or expired token'
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'valid' => true,
                'message' => 'Token is valid'
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error in validateResetToken: " . $e->getMessage());
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request'
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}