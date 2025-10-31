<?php
/**
 * Customer Management Routes
 * 
 * This file contains all API routes for customer management using query string parameters
 * instead of dynamic routes to prevent routing conflicts and maintain consistency.
 * 
 * Base URL: /api
 * Admin routes require authentication and admin privileges
 */

// =============================================================================
// ADMIN DASHBOARD ROUTES - Customer Data Table (Next.js Frontend)
// These routes are specifically designed for the admin dashboard customer table
// =============================================================================

/**
 * @api {get} /api/admin/customers Get Customers for Data Table
 * @apiName GetCustomersDataTable
 * @apiGroup AdminCustomers
 * @apiDescription Get paginated, searchable, and filterable customers data for admin dashboard table
 * 
 * @apiQuery {String} [search] Global search term (searches fullname, email, phone)
 * @apiQuery {String} [fullname] Filter by customer full name
 * @apiQuery {String} [email] Filter by email address
 * @apiQuery {String} [phone] Filter by phone number
 * @apiQuery {Number} [min_orders] Minimum number of orders
 * @apiQuery {Number} [max_orders] Maximum number of orders
 * @apiQuery {Number} [min_revenue] Minimum total revenue (in base currency)
 * @apiQuery {Number} [max_revenue] Maximum total revenue (in base currency)
 * @apiQuery {String} [date_from] Filter customers created after this date (YYYY-MM-DD)
 * @apiQuery {String} [date_to] Filter customers created before this date (YYYY-MM-DD)
 * @apiQuery {String} [customer_type] Filter by customer type: 'registered' or 'guest'
 * @apiQuery {String} [sort_field] Field to sort by: fullname, email, total_orders, total_revenue, first_ordered_at, last_ordered_at, customer_since
 * @apiQuery {String} [sort_direction] Sort direction: 'ASC' or 'DESC' (default: 'ASC')
 * @apiQuery {Number} [page=1] Page number for pagination
 * @apiQuery {Number} [per_page=10] Number of items per page (max: 100)
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object[]} data Array of customer objects
 * @apiSuccess {Object} pagination Pagination information
 * 
 * @apiExample {js} Next.js Example:
 * // Get first page of customers with search
 * const response = await fetch('/api/admin/customers?search=john&page=1&per_page=10', {
 *   headers: { 'Authorization': 'Bearer your-token' }
 * });
 * const data = await response.json();
 */
// $app->get('/api/admin/customers', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getCustomersWithSearchAndPaginated($request, $response);
// });

/**
 * @api {get} /api/admin/customer-details Get Customer Detailed Information
 * @apiName GetCustomerDetails
 * @apiGroup AdminCustomers
 * @apiDescription Get comprehensive customer details including order history and statistics
 * 
 * @apiQuery {Number} customer_id Customer unique ID (required)
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object} data Customer details object
 * 
 * @apiExample {js} Next.js Example:
 * // Get detailed customer information
 * const response = await fetch('/api/admin/customer-details?customer_id=123', {
 *   headers: { 'Authorization': 'Bearer your-token' }
 * });
 * const data = await response.json();
 */
// $app->get('/api/admin/customer-details', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getCustomerDetails($request, $response);
// });

// =============================================================================
// CUSTOMER SEARCH & FILTER ROUTES
// =============================================================================

/**
 * @api {get} /api/customers/search Search Customers
 * @apiName SearchCustomers
 * @apiGroup Customers
 * @apiDescription Search customers by name, email, or phone number
 * 
 * @apiQuery {String} q Search term (required)
 * @apiQuery {Number} [limit=50] Maximum number of results to return
 * @apiQuery {Number} [offset=0] Number of results to skip
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object[]} data Array of matching customers
 * @apiSuccess {Number} count Number of results
 * 
 * @apiExample {js} Next.js Example:
 * // Search for customers
 * const response = await fetch('/api/customers/search?q=john&limit=20');
 * const data = await response.json();
 */
// $app->get('/api/customers/search', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->searchCustomers($request, $response);
// });

/**
 * @api {get} /api/customers/by-city Get Customers by City
 * @apiName GetCustomersByCity
 * @apiGroup Customers
 * @apiDescription Get all customers from a specific city
 * 
 * @apiQuery {String} city City name (required)
 * @apiQuery {Number} [limit=50] Maximum number of results to return
 * @apiQuery {Number} [offset=0] Number of results to skip
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object[]} data Array of customers in the city
 * @apiSuccess {Number} count Number of results
 * 
 * @apiExample {js} Next.js Example:
 * // Get customers from Karachi
 * const response = await fetch('/api/customers/by-city?city=Karachi&limit=50');
 * const data = await response.json();
 */
// $app->get('/api/customers/by-city', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getCustomersByCity($request, $response);
// });

/**
 * @api {get} /api/customers/by-email Get Customer by Email
 * @apiName GetCustomerByEmail
 * @apiGroup Customers
 * @apiDescription Get customer details by email address
 * 
 * @apiQuery {String} email Email address (required)
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object} data Customer object
 * 
 * @apiExample {js} Next.js Example:
 * // Get customer by email
 * const response = await fetch('/api/customers/by-email?email=customer@example.com');
 * const data = await response.json();
 */
// $app->get('/api/customers/by-email', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getCustomerByEmail($request, $response);
// });

// =============================================================================
// BASIC CUSTOMER CRUD ROUTES
// =============================================================================

/**
 * @api {get} /api/customers Get All Customers
 * @apiName GetAllCustomers
 * @apiGroup Customers
 * @apiDescription Get all customers with basic pagination and sorting
 * 
 * @apiQuery {String} [sort] Field to sort by
 * @apiQuery {String} [order=ASC] Sort order: ASC or DESC
 * @apiQuery {Number} [limit=50] Number of records to return
 * @apiQuery {Number} [offset=0] Number of records to skip
 * @apiQuery {String} [customer_type] Filter by customer type: 'registered' or 'guest'
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object[]} data Array of customer objects
 * @apiSuccess {Number} count Number of customers returned
 * 
 * @apiExample {js} Next.js Example:
 * // Get all customers with pagination
 * const response = await fetch('/api/customers?limit=20&offset=0&sort=createdAt&order=DESC');
 * const data = await response.json();
 */
// $app->get('/api/customers', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getAllCustomers($request, $response);
// });

/**
 * @api {get} /api/customers/by-id Get Customer by ID
 * @apiName GetCustomerById
 * @apiGroup Customers
 * @apiDescription Get customer details by customer ID
 * 
 * @apiQuery {Number} id Customer unique ID (required)
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object} data Customer object
 * 
 * @apiExample {js} Next.js Example:
 * // Get customer by ID
 * const response = await fetch('/api/customers/by-id?id=123');
 * const data = await response.json();
 */
// $app->get('/api/customers/by-id', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getCustomerById($request, $response);
// });

/**
 * @api {post} /api/customers Create New Customer
 * @apiName CreateCustomer
 * @apiGroup Customers
 * @apiDescription Create a new customer record
 * 
 * @apiBody {String} firstname Customer first name (required)
 * @apiBody {String} lastname Customer last name (required)
 * @apiBody {String} email Customer email address (required)
 * @apiBody {String} phone Customer phone number (required)
 * @apiBody {String} address Customer street address (required)
 * @apiBody {String} city Customer city (required)
 * @apiBody {String} country Customer country (required)
 * @apiBody {String} postalCode Customer postal code (required)
 * @apiBody {String} [company] Customer company name
 * @apiBody {String} [apartment] Apartment/suite number
 * @apiBody {Number} [user_id] Associated user ID if available
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {String} message Success message
 * @apiSuccess {Number} customer_id ID of the created customer
 * 
 * @apiExample {js} Next.js Example:
 * // Create a new customer
 * const response = await fetch('/api/customers', {
 *   method: 'POST',
 *   headers: {
 *     'Content-Type': 'application/json',
 *   },
 *   body: JSON.stringify({
 *     firstname: 'John',
 *     lastname: 'Doe',
 *     email: 'john.doe@example.com',
 *     phone: '+1234567890',
 *     address: '123 Main St',
 *     city: 'Karachi',
 *     country: 'Pakistan',
 *     postalCode: '75500'
 *   })
 * });
 * const data = await response.json();
 */
// $app->post('/api/customers', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->createCustomer($request, $response);
// });

/**
 * @api {put} /api/customers/update Update Customer
 * @apiName UpdateCustomer
 * @apiGroup Customers
 * @apiDescription Update an existing customer record
 * 
 * @apiQuery {Number} id Customer unique ID (required)
 * 
 * @apiBody {String} [firstname] Customer first name
 * @apiBody {String} [lastname] Customer last name
 * @apiBody {String} [email] Customer email address
 * @apiBody {String} [phone] Customer phone number
 * @apiBody {String} [address] Customer street address
 * @apiBody {String} [city] Customer city
 * @apiBody {String} [country] Customer country
 * @apiBody {String} [postalCode] Customer postal code
 * @apiBody {String} [company] Customer company name
 * @apiBody {String} [apartment] Apartment/suite number
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {String} message Success message
 * 
 * @apiExample {js} Next.js Example:
 * // Update customer information
 * const response = await fetch('/api/customers/update?id=123', {
 *   method: 'PUT',
 *   headers: {
 *     'Content-Type': 'application/json',
 *   },
 *   body: JSON.stringify({
 *     firstname: 'John',
 *     lastname: 'Smith',
 *     email: 'john.smith@example.com',
 *     phone: '+1234567890'
 *   })
 * });
 * const data = await response.json();
 */
// $app->put('/api/customers/update', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->updateCustomer($request, $response);
// });

/**
 * @api {delete} /api/customers/delete Delete Customer
 * @apiName DeleteCustomer
 * @apiGroup Customers
 * @apiDescription Delete a customer record
 * 
 * @apiQuery {Number} id Customer unique ID (required)
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {String} message Success message
 * 
 * @apiExample {js} Next.js Example:
 * // Delete a customer
 * const response = await fetch('/api/customers/delete?id=123', {
 *   method: 'DELETE'
 * });
 * const data = await response.json();
 */
// $app->delete('/api/customers/delete', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->deleteCustomer($request, $response);
// });

/**
 * @api {get} /api/customers/statistics Get Customer Statistics
 * @apiName GetCustomerStatistics
 * @apiGroup Customers
 * @apiDescription Get overall customer statistics and metrics
 * 
 * @apiSuccess {Boolean} success Request status
 * @apiSuccess {Object} data Statistics object
 * @apiSuccess {Number} data.total_customers Total number of customers
 * @apiSuccess {Number} data.registered_customers Number of registered customers
 * @apiSuccess {Number} data.guest_customers Number of guest customers
 * @apiSuccess {Number} data.countries_count Number of countries represented
 * @apiSuccess {Number} data.cities_count Number of cities represented
 * 
 * @apiExample {js} Next.js Example:
 * // Get customer statistics
 * const response = await fetch('/api/customers/statistics');
 * const data = await response.json();
 */
// $app->get('/api/customers/statistics', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
//     return $controller->getCustomerStatistics($request, $response);
// });