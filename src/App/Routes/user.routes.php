<?php
// User routes - IMPORTANT: Define specific routes BEFORE parameterized routes

/**
 * @OA\Get(
 *     path="/api/users/guests",
 *     summary="Get guest customers",
 *     description="Retrieves a list of customers who do not have a user account.",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */

$app->get('/api/users/guests', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getGuestCustomers($request, $response);
});

/**
 * @OA\Get(
 *     path="/api/users/email/{email}",
 *     summary="Get user by email",
 *     description="Retrieves a user by email.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         description="Email of the user to retrieve.",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/users/email/{email}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUserByEmail($request, $response, $args);
});

/**
 * @OA\Get(
 *     path="/api/users/role/{role}",
 *     summary="Get users by role",
 *     description="Retrieves users by their role.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="role",
 *         in="path",
 *         required=true,
 *         description="User role.",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/users/role/{role}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUsersByRole($request, $response, $args);
});

/**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get all users",
 *     description="Get all users with profiles (admin only).",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/users', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getAllUsers($request, $response);
});

// PARAMETERIZED ROUTES SHOULD COME AFTER SPECIFIC ROUTES

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Get user by ID",
 *     description="Get user by ID with profile.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/users/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUserById($request, $response, $args);
});

/**
 * @OA\Post(
 *     path="/api/users",
 *     summary="Create a new user",
 *     description="Create a new user (admin only).",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             ref="#/components/schemas/User"
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->createUser($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/{id}/link-customer/{customerId}",
 *     summary="Link customer to user account",
 *     description="Link a customer to a user account.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="customerId",
 *         in="path",
 *         required=true,
 *         description="Customer ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/{id}/link-customer/{customerId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->linkCustomerToUser($request, $response, $args);
});

// New routes for added methods

/**
 * @OA\Post(
 *     path="/api/users/register-customer",
 *     summary="Register new customer user",
 *     description="Register a new customer user.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             ref="#/components/schemas/User"
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created"
 *     )
 * )
 */
$app->post('/api/users/register-customer', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->registerCustomerUser($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/login-customer",
 *     summary="Login customer user",
 *     description="Login a customer user.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             ref="#/components/schemas/User"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
$app->post('/api/users/login-customer', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->loginCustomerUser($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/register-with-role",
 *     summary="Register user with role",
 *     description="Register a new user with a role (admin only).",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             ref="#/components/schemas/User"
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/register-with-role', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->registerUserWithRole($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/forgot-password",
 *     summary="Forgot password",
 *     description="Send a password reset email to the user.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
$app->post('/api/users/forgot-password', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->forgotUserPassword($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/add-address",
 *     summary="Add new address",
 *     description="Add a new address for a user.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 description="User ID"
 *             ),
 *             @OA\Property(
 *                 property="address_type",
 *                 type="string",
 *                 description="Address type (home, work, etc.)"
 *             ),
 *             @OA\Property(
 *                 property="street_address",
 *                 type="string",
 *                 description="Street address"
 *             ),
 *             @OA\Property(
 *                 property="city",
 *                 type="string",
 *                 description="City"
 *             ),
 *             @OA\Property(
 *                 property="state",
 *                 type="string",
 *                 description="State"
 *             ),
 *             @OA\Property(
 *                 property="postal_code",
 *                 type="string",
 *                 description="Postal code"
 *             ),
 *             @OA\Property(
 *                 property="country",
 *                 type="string",
 *                 description="Country"
 *             ),
 *             @OA\Property(
 *                 property="is_default",
 *                 type="boolean",
 *                 description="Is this the default address?"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Address created"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/add-address', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->addNewAddress($request, $response);
});

/**
 * @OA\Delete(
 *     path="/api/users/{userId}/remove-address/{addressId}",
 *     summary="Remove address",
 *     description="Remove an address from a user.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="addressId",
 *         in="path",
 *         required=true,
 *         description="Address ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->delete('/api/users/{userId}/remove-address/{addressId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->removeAddress($request, $response, $args);
});

/**
 * @OA\Post(
 *     path="/api/users/{userId}/favorites/add/{productId}",
 *     summary="Add product to favorite",
 *     description="Add a product to a user's favorites.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         description="Product ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/{userId}/favorites/add/{productId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->addProductToFavorite($request, $response, $args);
});

/**
 * @OA\Delete(
 *     path="/api/users/{userId}/favorites/remove/{productId}",
 *     summary="Remove product from favorite",
 *     description="Remove a product from a user's favorites.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         description="Product ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->delete('/api/users/{userId}/favorites/remove/{productId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->removeProductFromFavorite($request, $response, $args);
});

/**
 * @OA\Put(
 *     path="/api/users/{id}/profile",
 *     summary="Save profile",
 *     description="Save a user's profile.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             ref="#/components/schemas/Profile"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->put('/api/users/{id}/profile', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->saveProfile($request, $response, $args);
});