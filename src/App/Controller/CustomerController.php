<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\CustomerRepository;

class CustomerController
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get all customers
     * 
     * @Route GET /api/customers
     */
    public function getAllCustomers(Request $request, Response $response): Response
    {
        try {
            // Get optional query parameters for pagination and sorting
            $queryParams = $request->getQueryParams();
            $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'] : null;
            $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : null;

            $customers = $this->customerRepository->findAll($orderBy, $limit, $offset);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customers: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get a single customer by ID
     * 
     * @Route GET /api/customers/{id}
     */
    public function getCustomerById(Request $request, Response $response, array $args): Response
    {
        try {
            $customerId = (int)$args['id'];
            
            $customer = $this->customerRepository->findById($customerId);
            
            if (!$customer) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customer
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customer: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get customer by email
     * 
     * @Route GET /api/customers/email/{email}
     */
    public function getCustomerByEmail(Request $request, Response $response, array $args): Response
    {
        try {
            $email = urldecode($args['email']);
            
            $customer = $this->customerRepository->findByEmail($email);
            
            if (!$customer) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customer
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customer: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Create a new customer
     * 
     * @Route POST /api/customers
     */
    public function createCustomer(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate required fields
            $requiredFields = ['firstname', 'lastname', 'email', 'phone', 'address', 'city', 'country', 'postalCode'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => "Field '{$field}' is required"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            
            // Check if customer already exists by email
            $existingCustomer = $this->customerRepository->findByEmail($data['email']);
            if ($existingCustomer) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer with this email already exists'
                ]));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
            }
            
            // Prepare customer data
            $customerData = [
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => $data['company'] ?? '',
                'address' => $data['address'],
                'apartment' => $data['apartment'] ?? '',
                'postalCode' => $data['postalCode'],
                'city' => $data['city'],
                'country' => $data['country'],
                'user_id' => $data['user_id'] ?? null
            ];
            
            $customerId = $this->customerRepository->insert($customerData);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer_id' => $customerId
            ]));
            
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to create customer: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Update a customer
     * 
     * @Route PUT /api/customers/{id}
     */
    public function updateCustomer(Request $request, Response $response, array $args): Response
    {
        try {
            $customerId = (int)$args['id'];
            $data = $request->getParsedBody();
            
            // Check if customer exists
            $existingCustomer = $this->customerRepository->findById($customerId);
            if (!$existingCustomer) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            // Update customer
            $success = $this->customerRepository->update($customerId, $data);
            
            if ($success) {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Customer updated successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to update customer'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to update customer: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Delete a customer
     * 
     * @Route DELETE /api/customers/{id}
     */
    public function deleteCustomer(Request $request, Response $response, array $args): Response
    {
        try {
            $customerId = (int)$args['id'];
            
            // Check if customer exists
            $existingCustomer = $this->customerRepository->findById($customerId);
            if (!$existingCustomer) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            // Delete customer
            $success = $this->customerRepository->delete($customerId);
            
            if ($success) {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Customer deleted successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Failed to delete customer'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to delete customer: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Search customers by name or email
     * 
     * @Route GET /api/customers/search/{query}
     */
    public function searchCustomers(Request $request, Response $response, array $args): Response
    {
        try {
            $query = urldecode($args['query']);
            
            $customers = $this->customerRepository->search($query);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to search customers: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get customers by city
     * 
     * @Route GET /api/customers/city/{city}
     */
    public function getCustomersByCity(Request $request, Response $response, array $args): Response
    {
        try {
            $city = urldecode($args['city']);
            
            $customers = $this->customerRepository->findByCity($city);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customers by city: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}