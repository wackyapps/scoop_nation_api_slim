<?php
// User routes - IMPORTANT: Define specific routes BEFORE parameterized routes

// Get guest customers (customers without user accounts) - SPECIFIC ROUTE FIRST
$app->get('/api/users/guests', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getGuestCustomers($request, $response);
});

// Get user by email with profile - SPECIFIC ROUTE
$app->get('/api/users/email/{email}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUserByEmail($request, $response, $args);
});

// Get users by role - SPECIFIC ROUTE
$app->get('/api/users/role/{role}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUsersByRole($request, $response, $args);
});

// Get all users with profiles (admin only) - STATIC ROUTE
$app->get('/api/users', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getAllUsers($request, $response);
});

// PARAMETERIZED ROUTES SHOULD COME AFTER SPECIFIC ROUTES

// Get user by ID with profile - PARAMETERIZED ROUTE
$app->get('/api/users/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUserById($request, $response, $args);
});

// Create a new user (admin only) - STATIC ROUTE (POST)
$app->post('/api/users', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->createUser($request, $response);
});

// Link customer to user account - PARAMETERIZED ROUTE (POST)
$app->post('/api/users/{id}/link-customer/{customerId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->linkCustomerToUser($request, $response, $args);
});

// New routes for added methods

// Register new customer user
$app->post('/api/users/register-customer', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->registerCustomerUser($request, $response);
});

// Login customer user
$app->post('/api/users/login-customer', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->loginCustomerUser($request, $response);
});

// Register user with role (admin only)
$app->post('/api/users/register-with-role', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->registerUserWithRole($request, $response);
});

// Forgot password
$app->post('/api/users/forgot-password', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->forgotUserPassword($request, $response);
});

// Add new address
$app->post('/api/users/{userId}/add-address', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->addNewAddress($request, $response, $args);
});

// Remove address
$app->delete('/api/users/{userId}/remove-address/{addressId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->removeAddress($request, $response, $args);
});

// Add product to favorite
$app->post('/api/users/{userId}/favorites/add/{productId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->addProductToFavorite($request, $response, $args);
});

// Remove product from favorite
$app->delete('/api/users/{userId}/favorites/remove/{productId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->removeProductFromFavorite($request, $response, $args);
});

// Save profile
$app->put('/api/users/{id}/profile', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->saveProfile($request, $response, $args);
});