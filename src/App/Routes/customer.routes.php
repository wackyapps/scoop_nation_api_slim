<?php
// Customer routes - IMPORTANT: Define specific routes BEFORE parameterized routes

// Search customers by name or email - SPECIFIC ROUTE FIRST
$app->get('/api/customers/search/{query}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->searchCustomers($request, $response, $args);
});

// Get customers by city - SPECIFIC ROUTE
$app->get('/api/customers/city/{city}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->getCustomersByCity($request, $response, $args);
});

// Get customer by email - SPECIFIC ROUTE
$app->get('/api/customers/email/{email}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->getCustomerByEmail($request, $response, $args);
});

// Get all customers - STATIC ROUTE
$app->get('/api/customers', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->getAllCustomers($request, $response);
});

// PARAMETERIZED ROUTES SHOULD COME AFTER SPECIFIC ROUTES

// Get customer by ID - PARAMETERIZED ROUTE
$app->get('/api/customers/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->getCustomerById($request, $response, $args);
});

// Create a new customer - STATIC ROUTE (POST)
$app->post('/api/customers', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->createCustomer($request, $response);
});

// Update a customer - PARAMETERIZED ROUTE (PUT)
$app->put('/api/customers/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->updateCustomer($request, $response, $args);
});

// Delete a customer - PARAMETERIZED ROUTE (DELETE)
$app->delete('/api/customers/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CustomerController::class);
    return $controller->deleteCustomer($request, $response, $args);
});