<?php

/**
 * Email Subscription Routes
 * 
 * These routes handle email subscription operations including
 * subscribing to the mailing list and managing subscriptions.
 */

/**
 * @OA\Post(
 *     path="/api/email-subscription/subscribe",
 *     summary="Subscribe to email list",
 *     description="Allows users to subscribe their email address to the mailing list. This endpoint is publicly accessible and does not require authentication.",
 *     tags={"Email Subscription"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Email subscription data",
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Email address to subscribe")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Successfully subscribed",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Successfully subscribed to our mailing list"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="email", type="string", example="user@example.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request - Email already exists or invalid",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Email already exists")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Failed to subscribe")
 *         )
 *     )
 * )
 */
$app->post('/api/email-subscription/subscribe', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\EmailSubscriptionController::class);
    return $controller->subscribe($request, $response);
});

/**
 * @OA\Get(
 *     path="/api/email-subscription/subscriptions",
 *     summary="Get all email subscriptions (Admin Only)",
 *     description="Retrieve all email subscriptions with pagination. Requires administrator authentication.",
 *     tags={"Email Subscription"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of records to return",
 *         @OA\Schema(type="integer", example=20)
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         description="Number of records to skip",
 *         @OA\Schema(type="integer", example=0)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="email", type="string", example="user@example.com")
 *                 )
 *             ),
 *             @OA\Property(property="count", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Admin authentication required",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Token not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Failed to retrieve subscriptions")
 *         )
 *     )
 * )
 */
// $app->get('/api/email-subscription/subscriptions', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\EmailSubscriptionController::class);
//     return $controller->getAllSubscriptions($request, $response);
// });
