<?php
// Session routes

/**
 * @OA\Post(
 *     path="/api/sessions/start",
 *     summary="Start new anonymous session",
 *     description="Starts a new anonymous session for a visitor.",
 *     tags={"Sessions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="cookie_token",
 *                 type="string",
 *                 description="Client-side random token from Next.js frontend"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Session started successfully"
 *     )
 * )
 */
$app->post('/api/sessions/start', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->startNewSession($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/sessions/verify",
 *     summary="Verify session",
 *     description="Verifies a session using cookie token and session ID.",
 *     tags={"Sessions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="cookie_token",
 *                 type="string",
 *                 description="Client-side cookie token"
 *             ),
 *             @OA\Property(
 *                 property="session_id",
 *                 type="string",
 *                 description="Server-generated session ID (GUID)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Session verified successfully"
 *     )
 * )
 */
$app->post('/api/sessions/verify', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->verifySession($request, $response);
});

/**
 * @OA\Get(
 *     path="/api/sessions/{sessionId}/cart",
 *     summary="Get cart items for session",
 *     description="Retrieves all cart items for a specific session.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
$app->get('/api/sessions/{sessionId}/cart', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->getCartItems($request, $response, $args);
});

/**
 * @OA\Post(
 *     path="/api/sessions/{sessionId}/cart/add",
 *     summary="Add product to cart",
 *     description="Adds a product to the cart for a specific session.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 description="Product ID"
 *             ),
 *             @OA\Property(
 *                 property="variant_id",
 *                 type="integer",
 *                 description="Variant ID"
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 description="Quantity to add (default: 1)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product added to cart successfully"
 *     )
 * )
 */
$app->post('/api/sessions/{sessionId}/cart/add', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->addProductToCart($request, $response, $args);
});

/**
 * @OA\Post(
 *     path="/api/sessions/{sessionId}/cart/increase",
 *     summary="Increase product quantity",
 *     description="Increases the quantity of a product in the cart.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 description="Product ID"
 *             ),
 *             @OA\Property(
 *                 property="variant_id",
 *                 type="integer",
 *                 description="Variant ID"
 *             ),
 *             @OA\Property(
 *                 property="increment",
 *                 type="integer",
 *                 description="Amount to increment by (default: 1)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product quantity increased successfully"
 *     )
 * )
 */
$app->post('/api/sessions/{sessionId}/cart/increase', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->increaseProductQuantity($request, $response, $args);
});

/**
 * @OA\Post(
 *     path="/api/sessions/{sessionId}/cart/decrease",
 *     summary="Decrease product quantity",
 *     description="Decreases the quantity of a product in the cart.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 description="Product ID"
 *             ),
 *             @OA\Property(
 *                 property="variant_id",
 *                 type="integer",
 *                 description="Variant ID"
 *             ),
 *             @OA\Property(
 *                 property="decrement",
 *                 type="integer",
 *                 description="Amount to decrement by (default: 1)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product quantity decreased successfully"
 *     )
 * )
 */
$app->post('/api/sessions/{sessionId}/cart/decrease', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->decreaseProductQuantity($request, $response, $args);
});

/**
 * @OA\Delete(
 *     path="/api/sessions/{sessionId}/cart/remove",
 *     summary="Remove product from cart",
 *     description="Removes a product from the cart.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 description="Product ID"
 *             ),
 *             @OA\Property(
 *                 property="variant_id",
 *                 type="integer",
 *                 description="Variant ID"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product removed from cart successfully"
 *     )
 * )
 */
$app->delete('/api/sessions/{sessionId}/cart/remove', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->removeProductFromCart($request, $response, $args);
});

/**
 * @OA\Post(
 *     path="/api/sessions/{sessionId}/checkout",
 *     summary="Checkout session cart",
 *     description="Converts session cart to order with customer information.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="sessionId",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="fullname",
 *                 type="string",
 *                 description="Customer's full name"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="Customer's email"
 *             ),
 *             @OA\Property(
 *                 property="phone",
 *                 type="string",
 *                 description="Customer's phone"
 *             ),
 *             @OA\Property(
 *                 property="gender",
 *                 type="string",
 *                 enum={"male", "female", "other"},
 *                 description="Customer's gender (optional)"
 *             ),
 *             @OA\Property(
 *                 property="date_of_birth",
 *                 type="string",
 *                 format="date",
 *                 description="Customer's date of birth YYYY-MM-DD (optional)"
 *             ),
 *             @OA\Property(
 *                 property="branch_id",
 *                 type="integer",
 *                 description="Branch ID for the order (optional)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order created successfully"
 *     )
 * )
 */
$app->post('/api/sessions/{sessionId}/checkout', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->checkoutSessionCart($request, $response, $args);
});

/**
 * @OA\Get(
 *     path="/api/sessions/active",
 *     summary="Get active sessions",
 *     description="Retrieves all active sessions (admin only).",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *         description="Limit the number of results",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         required=false,
 *         description="Offset for pagination",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/sessions/active', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->getActiveSessions($request, $response);
});

/**
 * @OA\Get(
 *     path="/api/sessions/{id}",
 *     summary="Get session by ID",
 *     description="Retrieves a session by its ID.",
 *     tags={"Sessions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Session ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/sessions/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\SessionController::class);
    return $controller->getSessionById($request, $response, $args);
});