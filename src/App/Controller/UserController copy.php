<?php
// declare(strict_types=1);

// namespace App\Controller;

// use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Message\ResponseInterface as Response;
// use App\Repository\UserRepository;

// class UserController
// {
//     private $userRepository;

//     public function __construct(UserRepository $userRepository)
//     {
//         $this->userRepository = $userRepository;
//     }

//     /**
//      * Get all users with their customer profiles
//      * 
//      * @Route GET /api/users
//      */
//     public function getAllUsers(Request $request, Response $response): Response
//     {
//         try {
//             // Get optional query parameters for pagination and sorting
//             $queryParams = $request->getQueryParams();
//             $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'] : null;
//             $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
//             $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : null;

//             $users = $this->userRepository->findAllWithProfiles($orderBy, $limit, $offset);
            
//             $response->getBody()->write(json_encode([
//                 'success' => true,
//                 'data' => $users,
//                 'count' => count($users)
//             ]));
            
//             return $response->withHeader('Content-Type', 'application/json');
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to retrieve users: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }

//     /**
//      * Get a single user by ID with customer profile
//      * 
//      * @Route GET /api/users/{id}
//      */
//     public function getUserById(Request $request, Response $response, array $args): Response
//     {
//         try {
//             $userId = (int)$args['id'];
            
//             $user = $this->userRepository->findUserWithCustomerProfile($userId);
            
//             if (!$user) {
//                 $response->getBody()->write(json_encode([
//                     'success' => false,
//                     'error' => 'User not found'
//                 ]));
//                 return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
//             }
            
//             $response->getBody()->write(json_encode([
//                 'success' => true,
//                 'data' => $user
//             ]));
            
//             return $response->withHeader('Content-Type', 'application/json');
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to retrieve user: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }

//     /**
//      * Get user by email with customer profile
//      * 
//      * @Route GET /api/users/email/{email}
//      */
//     public function getUserByEmail(Request $request, Response $response, array $args): Response
//     {
//         try {
//             $email = urldecode($args['email']);
            
//             $user = $this->userRepository->findByEmailWithProfile($email);
            
//             if (!$user) {
//                 $response->getBody()->write(json_encode([
//                     'success' => false,
//                     'error' => 'User not found'
//                 ]));
//                 return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
//             }
            
//             $response->getBody()->write(json_encode([
//                 'success' => true,
//                 'data' => $user
//             ]));
            
//             return $response->withHeader('Content-Type', 'application/json');
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to retrieve user: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }

//     /**
//      * Get users by role
//      * 
//      * @Route GET /api/users/role/{role}
//      */
//     public function getUsersByRole(Request $request, Response $response, array $args): Response
//     {
//         try {
//             $role = $args['role'];
            
//             // Get optional query parameters
//             $queryParams = $request->getQueryParams();
//             $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'] : null;
//             $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
//             $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : null;

//             $users = $this->userRepository->findByRole($role, $orderBy, $limit, $offset);
            
//             $response->getBody()->write(json_encode([
//                 'success' => true,
//                 'data' => $users,
//                 'count' => count($users)
//             ]));
            
//             return $response->withHeader('Content-Type', 'application/json');
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to retrieve users by role: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }

//     /**
//      * Get guest customers (customers without user accounts)
//      * 
//      * @Route GET /api/users/guests
//      */
//     public function getGuestCustomers(Request $request, Response $response): Response
//     {
//         try {
//             $guestCustomers = $this->userRepository->findGuestCustomers();
            
//             $response->getBody()->write(json_encode([
//                 'success' => true,
//                 'data' => $guestCustomers,
//                 'count' => count($guestCustomers)
//             ]));
            
//             return $response->withHeader('Content-Type', 'application/json');
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to retrieve guest customers: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }

//     /**
//      * Create a new user (for admin purposes)
//      * 
//      * @Route POST /api/users
//      */
//     public function createUser(Request $request, Response $response): Response
//     {
//         try {
//             $data = $request->getParsedBody();
            
//             // Validate required fields
//             if (!isset($data['email']) || !isset($data['password'])) {
//                 $response->getBody()->write(json_encode([
//                     'success' => false,
//                     'error' => 'Email and password are required'
//                 ]));
//                 return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//             }
            
//             // Check if user already exists
//             $existingUser = $this->userRepository->findByEmail($data['email']);
//             if ($existingUser) {
//                 $response->getBody()->write(json_encode([
//                     'success' => false,
//                     'error' => 'User with this email already exists'
//                 ]));
//                 return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
//             }
            
//             // Prepare user data
//             $userData = [
//                 'email' => $data['email'],
//                 'password' => password_hash($data['password'], PASSWORD_DEFAULT),
//                 'role' => $data['role'] ?? 'user'
//             ];
            
//             // Optional: link to existing customer
//             $customerId = isset($data['customer_id']) ? (int)$data['customer_id'] : null;
            
//             $userId = $this->userRepository->createUserWithCustomerLink($userData, $customerId);
            
//             $response->getBody()->write(json_encode([
//                 'success' => true,
//                 'message' => 'User created successfully',
//                 'user_id' => $userId
//             ]));
            
//             return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to create user: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }

//     /**
//      * Link a customer to a user account
//      * 
//      * @Route POST /api/users/{id}/link-customer/{customerId}
//      */
//     public function linkCustomerToUser(Request $request, Response $response, array $args): Response
//     {
//         try {
//             $userId = (int)$args['id'];
//             $customerId = (int)$args['customerId'];
            
//             $success = $this->userRepository->linkCustomerToUser($customerId, $userId);
            
//             if ($success) {
//                 $response->getBody()->write(json_encode([
//                     'success' => true,
//                     'message' => 'Customer linked to user successfully'
//                 ]));
//                 return $response->withHeader('Content-Type', 'application/json');
//             } else {
//                 $response->getBody()->write(json_encode([
//                     'success' => false,
//                     'error' => 'Failed to link customer to user'
//                 ]));
//                 return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//             }
            
//         } catch (\Exception $e) {
//             $response->getBody()->write(json_encode([
//                 'success' => false,
//                 'error' => 'Failed to link customer: ' . $e->getMessage()
//             ]));
//             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//         }
//     }
// }