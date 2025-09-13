<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Scoop Nation API", version="1.0")
 * @OA\Server(url="http://localhost:8080")
 */

// User routes - IMPORTANT: Define specific routes BEFORE parameterized routes

/**
 * @OA\Get(
 *     path="/api/users/guests",
 *     summary="Get guest customers",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
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
