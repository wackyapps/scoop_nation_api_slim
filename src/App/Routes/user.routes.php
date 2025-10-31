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

// $app->get('/api/users/guests', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->getGuestCustomers($request, $response);
// });

/**
 * @OA\Get(
 *     path="/api/users/email",
 *     summary="Get user by email",
 *     description="Retrieves a user by email.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
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
// $app->get('/api/users/email', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->getUserByEmail($request, $response);
// });

/**
 * @OA\Get(
 *     path="/api/users/role",
 *     summary="Get users by role",
 *     description="Retrieves users by their role.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="role",
 *         in="query",
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
// $app->get('/api/users/role', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->getUsersByRole($request, $response);
// });

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
// $app->get('/api/users', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->getAllUsers($request, $response);
// });



/**
 * @OA\Get(
 *     path="/api/users/by-id",
 *     summary="Get user by ID",
 *     description="Get user by ID with profile.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
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

// $app->get('/api/users/by-id', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->getUserById($request, $response);
// });

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

// $app->post('/api/users', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->createUser($request, $response);
// });

/**
 * @OA\Post(
 *     path="/api/users/link-customer",
 *     summary="Link customer to user account",
 *     description="Link a customer to a user account.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="User ID.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="customerId",
 *         in="query",
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

// $app->post('/api/users/link-customer', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->linkCustomerToUser($request, $response);
// });


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
 *     path="/api/users/google-auth",
 *     summary="Login or register customer with Google OAuth",
 *     description="Authenticate a customer using Google OAuth. If the user exists, logs them in. If not, creates a new account.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"idToken", "email", "fullname"},
 *             @OA\Property(
 *                 property="idToken",
 *                 type="string",
 *                 description="Firebase ID token from Google sign-in"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="User's email from Google"
 *             ),
 *             @OA\Property(
 *                 property="fullname",
 *                 type="string",
 *                 description="User's full name from Google"
 *             ),
 *             @OA\Property(
 *                 property="photoURL",
 *                 type="string",
 *                 description="User's profile picture URL from Google (optional)"
 *             ),
 *             @OA\Property(
 *                 property="phone",
 *                 type="string",
 *                 description="User's phone number (optional)"
 *             ),
 *             @OA\Property(
 *                 property="session_id",
 *                 type="string",
 *                 description="Session ID for cart linking (optional)"
 *             ),
 *             @OA\Property(
 *                 property="cookie_token",
 *                 type="string",
 *                 description="Cookie token for cart linking (optional)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
 *             @OA\Property(property="user", type="object"),
 *             @OA\Property(property="message", type="string", example="Login successful")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Account created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="user", type="object"),
 *             @OA\Property(property="message", type="string", example="Account created successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="ID token, email, and full name are required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid Google token",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Invalid Google authentication token")
 *         )
 *     )
 * )
 */
$app->post('/api/users/google-auth', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->googleAuth($request, $response);
});

/**
 * Login Admin User
 */

// $app->post('/api/users/login-admin', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->loginAdminUser($request, $response);
// });

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
// $app->post('/api/users/register-with-role', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\UserController::class);
//     return $controller->registerUserWithRole($request, $response);
// });

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
 * @OA\Get(
 *     path="/api/users/verify-email",
 *     summary="Verify email address",
 *     description="Verify a user's email address using the verification token sent via email.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="token",
 *         in="query",
 *         required=true,
 *         description="Email verification token",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email verified successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Email verified successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or expired token",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Invalid or expired verification token"),
 *             @OA\Property(property="code", type="string", example="TOKEN_INVALID")
 *         )
 *     )
 * )
 */
$app->get('/api/users/verify-email', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->verifyEmail($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/resend-verification",
 *     summary="Resend verification email",
 *     description="Resend the email verification link to a user who hasn't verified their email yet.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email"},
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="User's email address",
 *                 example="user@example.com"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verification email sent successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Verification email sent successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Email already verified or user not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Email is already verified"),
 *             @OA\Property(property="code", type="string", example="ALREADY_VERIFIED")
 *         )
 *     )
 * )
 */
$app->post('/api/users/resend-verification', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->resendVerificationEmail($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/reset-password",
 *     summary="Reset password with token",
 *     description="Reset a user's password using the reset token sent via email.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"token", "password"},
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 description="Password reset token from email",
 *                 example="a1b2c3d4e5f6..."
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 format="password",
 *                 description="New password (minimum 8 characters)",
 *                 example="newSecurePassword123"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password reset successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid token or weak password",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Invalid or expired reset token"),
 *             @OA\Property(property="code", type="string", example="TOKEN_INVALID")
 *         )
 *     )
 * )
 */
$app->post('/api/users/reset-password', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->resetPassword($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/resend-password-reset",
 *     summary="Resend password reset email",
 *     description="Resend the password reset link to a user who requested a password reset but didn't receive the email or the email expired.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email"},
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="User's email address",
 *                 example="user@example.com"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset email sent successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="If an account exists with this email, you will receive password reset instructions.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="s

/**
 * @OA\Get(
 *     path="/api/users/validate-reset-token",
 *     summary="Validate password reset token",
 *     description="Check if a password reset token is valid and not expired. Used by frontend to determine if the reset form should be displayed.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="token",
 *         in="query",
 *         required=true,
 *         description="Password reset token to validate",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Token validation result",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="valid", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Token is valid")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or expired token",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="valid", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Invalid or expired token")
 *         )
 *     )
 * )
 */
$app->get('/api/users/validate-reset-token', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->validateResetToken($request, $response);
});

$app->get('/api/users/addresses/get', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getAddressOfUser($request, $response);
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
 * @OA\Post(
 *     path="/api/users/favorites",
 *     summary="Get favorites",
 *     description="Get all favorites for a user.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 description="User ID"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */


/**
 * @OA\Post(
 *     path="/api/users/remove-address",
 *     summary="Remove address",
 *     description="Remove an address from a user.",
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
 *                 property="id",
 *                 type="integer",
 *                 description="Address ID"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/remove-address', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->removeAddress($request, $response);
});


$app->get('/api/users/favorites/get', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getFavorites($request, $response);
});
/**
 * @OA\Post(
 *     path="/api/users/favorites/add",
 *     summary="Add product to favorite",
 *     description="Add a product to a user's favorites.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"userId", "productId"},
 *             @OA\Property(property="userId", type="integer", description="User ID"),
 *             @OA\Property(property="productId", type="integer", description="Product ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/favorites/add', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->addProductToFavorite($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/users/favorites/remove",
 *     summary="Remove product from favorite",
 *     description="Remove a product from a user's favorites.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"userId", "productId"},
 *             @OA\Property(property="userId", type="integer", description="User ID"),
 *             @OA\Property(property="productId", type="integer", description="Product ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/users/favorites/remove', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->removeProductFromFavorite($request, $response);
});

/**
 * @OA\Put(
 *     path="/api/users/profile",
 *     summary="Save profile",
 *     description="Save a user's profile.",
 *     tags={"Users"},
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
$app->put('/api/users/profile', function ($request, $response) use ($app) {
    $userId = (int) $request->getAttribute('user')['id'];
    if (!$userId) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode([
            'success' => false,
            'error' => 'User ID is required'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->saveProfile($request, $response, $userId);
});


$app->get('/api/users/orders/get', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\UserController::class);
    return $controller->getUserOrders($request, $response);
});