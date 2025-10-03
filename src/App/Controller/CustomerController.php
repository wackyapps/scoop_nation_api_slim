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
     * Get all customers with search, pagination, and comprehensive data
     * For admin dashboard customer data table
     * 
     * @Route GET /api/admin/customers
     */
    public function getCustomersWithSearchAndPaginated(Request $request, Response $response): Response
    {
        try {
            // Only allow admin access
            // $this->ensureAdminAccess($request);

            $queryParams = $request->getQueryParams();
            
            // Extract filters
            $filters = [
                'search' => $queryParams['search'] ?? null,
                'fullname' => $queryParams['fullname'] ?? null,
                'email' => $queryParams['email'] ?? null,
                'phone' => $queryParams['phone'] ?? null,
                'min_orders' => $queryParams['min_orders'] ?? null,
                'max_orders' => $queryParams['max_orders'] ?? null,
                'min_revenue' => $queryParams['min_revenue'] ?? null,
                'max_revenue' => $queryParams['max_revenue'] ?? null,
                'date_from' => $queryParams['date_from'] ?? null,
                'date_to' => $queryParams['date_to'] ?? null,
                'customer_type' => $queryParams['customer_type'] ?? null,
            ];

            // Extract sorting
            $sort = [
                'field' => $queryParams['sort_field'] ?? null,
                'direction' => $queryParams['sort_direction'] ?? 'ASC'
            ];

            // Extract pagination
            $page = max(1, (int)($queryParams['page'] ?? 1));
            $perPage = min(100, max(1, (int)($queryParams['per_page'] ?? 10)));

            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });

            $result = $this->customerRepository->getCustomersWithSearchAndPaginated(
                $filters, 
                $sort, 
                $page, 
                $perPage
            );

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result['customers'],
                'pagination' => $result['pagination'],
                'filters' => $filters
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
     * Get comprehensive customer details by query parameter
     * 
     * @Route GET /api/admin/customer-details
     */
    public function getCustomerDetails(Request $request, Response $response): Response
    {
        try {
            // Only allow admin access
            // $this->ensureAdminAccess($request);

            $queryParams = $request->getQueryParams();
            $customerId = (int)($queryParams['customer_id'] ?? 0);
            
            if (!$customerId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer ID is required. Use ?customer_id=123'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $customer = $this->customerRepository->getCustomerDetails($customerId);
            
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
                'error' => 'Failed to retrieve customer details: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get all customers (legacy method - kept for backward compatibility)
     * 
     * @Route GET /api/customers
     */
    public function getAllCustomers(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            // Build criteria from query parameters
            $criteria = [];
            if (!empty($queryParams['customer_type'])) {
                if ($queryParams['customer_type'] === 'registered') {
                    $criteria['user_id'] = ['NOT NULL' => ''];
                } elseif ($queryParams['customer_type'] === 'guest') {
                    $criteria['user_id'] = null;
                }
            }

            $orderBy = null;
            if (isset($queryParams['sort'])) {
                $orderBy = [$queryParams['sort'] => $queryParams['order'] ?? 'ASC'];
            }

            $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int)$queryParams['offset'] : null;

            $customers = $this->customerRepository->findBy($criteria, $orderBy, $limit, $offset);
            
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
     * Get a single customer by ID via query parameter
     * 
     * @Route GET /api/customers/by-id
     */
    public function getCustomerById(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $customerId = (int)($queryParams['id'] ?? 0);
            
            if (!$customerId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer ID is required. Use ?id=123'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $customer = $this->customerRepository->find($customerId);
            
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
     * Get customer by email via query parameter
     * 
     * @Route GET /api/customers/by-email
     */
    public function getCustomerByEmail(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $email = $queryParams['email'] ?? '';
            
            if (empty($email)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email address is required. Use ?email=user@example.com'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
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
     * Search customers by name, email, or phone via query parameter
     * 
     * @Route GET /api/customers/search
     */
    public function searchCustomers(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $searchQuery = $queryParams['q'] ?? '';
            
            if (empty($searchQuery)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Search query is required. Use ?q=search_term'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $customers = $this->customerRepository->search($searchQuery);
            
            // Apply limit if provided
            $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
            if ($limit && count($customers) > $limit) {
                $customers = array_slice($customers, 0, $limit);
            }
            
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
     * Get customers by city via query parameter
     * 
     * @Route GET /api/customers/by-city
     */
    public function getCustomersByCity(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $city = $queryParams['city'] ?? '';
            
            if (empty($city)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'City name is required. Use ?city=CityName'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $customers = $this->customerRepository->findByCity($city);
            
            // Apply limit if provided
            $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : null;
            if ($limit && count($customers) > $limit) {
                $customers = array_slice($customers, 0, $limit);
            }
            
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

    /**
     * Get customers by country via query parameter
     * 
     * @Route GET /api/customers/by-country
     */
    public function getCustomersByCountry(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $country = $queryParams['country'] ?? '';
            
            if (empty($country)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Country name is required. Use ?country=CountryName'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $customers = $this->customerRepository->findByCountry($country);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customers by country: ' . $e->getMessage()
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
            
            $customerId = $this->customerRepository->save($customerData);
            
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
     * Update a customer via query parameter
     * 
     * @Route PUT /api/customers/update
     */
    public function updateCustomer(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $customerId = (int)($queryParams['id'] ?? 0);
            $data = $request->getParsedBody();
            
            if (!$customerId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer ID is required. Use ?id=123'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // Check if customer exists
            $existingCustomer = $this->customerRepository->find($customerId);
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
     * Delete a customer via query parameter
     * 
     * @Route DELETE /api/customers/delete
     */
    public function deleteCustomer(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $customerId = (int)($queryParams['id'] ?? 0);
            
            if (!$customerId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer ID is required. Use ?id=123'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // Check if customer exists
            $existingCustomer = $this->customerRepository->find($customerId);
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
     * Get customer statistics
     * 
     * @Route GET /api/customers/statistics
     */
    public function getCustomerStatistics(Request $request, Response $response): Response
    {
        try {
            $statistics = $this->customerRepository->getStatistics();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $statistics
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customer statistics: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get customers with their order count
     * 
     * @Route GET /api/customers/with-order-count
     */
    public function getCustomersWithOrderCount(Request $request, Response $response): Response
    {
        try {
            $customers = $this->customerRepository->findCustomersWithOrderCount();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve customers with order count: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Find guest customers (without user accounts)
     * 
     * @Route GET /api/customers/guest
     */
    public function getGuestCustomers(Request $request, Response $response): Response
    {
        try {
            $customers = $this->customerRepository->findGuestCustomers();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
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
     * Find registered customers (with user accounts)
     * 
     * @Route GET /api/customers/registered
     */
    public function getRegisteredCustomers(Request $request, Response $response): Response
    {
        try {
            $customers = $this->customerRepository->findRegisteredCustomers();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve registered customers: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Ensure admin access (placeholder for authorization)
     */
    private function ensureAdminAccess(Request $request): void
    {
        // Implement your admin authorization logic here
        // This could check JWT tokens, session data, etc.
        // Example:
        // $token = $request->getHeader('Authorization')[0] ?? '';
        // if (!isValidAdminToken($token)) {
        //     throw new \Exception('Admin access required');
        // }
    }
}