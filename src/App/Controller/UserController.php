<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\AddressRepository;
use App\Services\EmailService;
use App\Services\OtpService;
use App\Services\Authentication\JWT;

class UserController
{
    private $userRepository;
    private $wishlistRepository;
    private $addressRepository;
    private $emailService;
    private $otpService;

    public function __construct(
        UserRepository $userRepository,
        WishlistRepository $wishlistRepository, 
        AddressRepository $addressRepository,
        EmailService $emailService,
        OtpService $otpService
    ) {
        $this->userRepository = $userRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->addressRepository = $addressRepository;
        $this->emailService = $emailService;
        $this->otpService = $otpService;
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
            $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : null;

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
     * @Route GET /api/users/{id}
     */
    public function getUserById(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['id'];
            
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
     * @Route GET /api/users/email/{email}
     */
    public function getUserByEmail(Request $request, Response $response, array $args): Response
    {
        try {
            $email = urldecode($args['email']);
            
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
     * @Route GET /api/users/role/{role}
     */
    public function getUsersByRole(Request $request, Response $response, array $args): Response
    {
        try {
            $role = $args['role'];
            
            // Get optional query parameters
            $queryParams = $request->getQueryParams();
            $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'] : null;
            $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : null;

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
                'phone' => $data['phone'],
                'company' => $data['company'] ?? null,
                'address' => $data['address'] ?? null,
                'apartment' => $data['apartment'] ?? null,
                'postalCode' => $data['postalCode'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null
            ];
            
            $userId = $this->userRepository->registerCustomerUser($userData, $customerData);
            
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Customer user registered successfully', 'user_id' => $userId]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to register customer user: ' . $e->getMessage()]));
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
            
            if (!isset($data['email']) || !isset($data['password'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Email and password are required']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                
            }
            
            $user = $this->userRepository->loginCustomerUser($data['email'], $data['password']);
            
            if (!$user) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid email or password']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
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
            
            $success = $this->userRepository->forgotUserPassword($data['email']);
            
            if (!$success) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'User not found']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                
            }

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'OTP sent to your email']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to process request: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Add new address for user
     * 
     * @Route POST /api/users/{userId}/add-address
     */
    public function addNewAddress(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['userId'];
            $data = $request->getParsedBody();
            
            // Validate required fields
            $required = ['address_type', 'street_address', 'city', 'state', 'country', 'postal_code'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode(['success' => false, 'error' => "Field {$field} is required"]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            
            // Get customer ID for the user
            $user = $this->userRepository->findUserWithCustomerProfile($userId);
            if (!$user || !$user->customer_id) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Customer profile not found for user']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $addressData = [
                'customer_id' => $user->customer_id,
                'address_type' => $data['address_type'],
                'street_address' => $data['street_address'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
                'is_default' => $data['is_default'] ?? false
            ];
            
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
     * @Route DELETE /api/users/{userId}/remove-address/{addressId}
     */
    public function removeAddress(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['userId'];
            $addressId = (int)$args['addressId'];
            
            // Get customer ID for the user
            $user = $this->userRepository->findUserWithCustomerProfile($userId);
            if (!$user || !$user->customer_id) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Customer profile not found for user']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $addressRepository = new AddressRepository();
            $address = $addressRepository->findOneBy(['id' => $addressId, 'customer_id' => $user->customer_id]);
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
     * Add product to favorite
     * 
     * @Route POST /api/users/{userId}/favorites/add/{productId}
     */
    public function addProductToFavorite(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['userId'];
            $productId = (int)$args['productId'];
            
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
     * @Route DELETE /api/users/{userId}/favorites/remove/{productId}
     */
    public function removeProductFromFavorite(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['userId'];
            $productId = (int)$args['productId'];
            
            $wishlistRepository = new WishlistRepository();
            $success = $wishlistRepository->removeProductFromFavorite($userId, $productId);
            
            $response->getBody()->write(json_encode(['success' => $success, 'message' => $success ? 'Product removed from favorites' : 'Failed to remove product from favorites']));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to remove favorite: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Save profile updates
     * 
     * @Route PUT /api/users/{id}/profile
     */
    public function saveProfile(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int)$args['id'];
            $data = $request->getParsedBody();
            
            // Prepare user data
            $userData = array_intersect_key($data, array_flip(['email', 'phone']));
            
            // Prepare customer data
            $customerData = array_intersect_key($data, array_flip(['fullname', 'gender', 'date_of_birth']));
            
            $success = $this->userRepository->saveProfile($userId, $userData, $customerData);
            
            $response->getBody()->write(json_encode(['success' => $success, 'message' => $success ? 'Profile updated successfully' : 'No changes made']));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to update profile: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}