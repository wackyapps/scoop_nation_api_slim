<?php
// Admin Profile routes

/**
 * @OA\Get(
 *     path="/api/admin/profile",
 *     summary="Get admin profile",
 *     description="Retrieves an admin profile by user ID.",
 *     tags={"Admin Profile"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="User ID of the admin.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Admin profile not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/admin/profile', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\AdminProfileController::class);
    return $controller->getAdminProfile($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/admin/profile/update",
 *     summary="Update admin profile",
 *     description="Updates an admin profile.",
 *     tags={"Admin Profile"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="fullname", type="string", example="John Doe"),
 *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
 *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="avatar", type="string", example="/media/avatars/avatar_1_123456.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/admin/profile/update', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\AdminProfileController::class);
    return $controller->updateAdminProfile($request, $response);
});

/**
 * @OA\Post(
 *     path="/api/admin/profile/upload-avatar",
 *     summary="Upload admin avatar",
 *     description="Uploads an avatar image for an admin profile.",
 *     tags={"Admin Profile"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"user_id", "avatar"},
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="avatar", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Avatar uploaded successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->post('/api/admin/profile/upload-avatar', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\AdminProfileController::class);
    return $controller->uploadAvatar($request, $response);
});

/**
 * @OA\Get(
 *     path="/api/admin/profiles",
 *     summary="Get all admin profiles",
 *     description="Retrieves all admin profiles with pagination support.",
 *     tags={"Admin Profile"},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *         description="Number of records to return.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         required=false,
 *         description="Number of records to skip.",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
$app->get('/api/admin/profiles', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\AdminProfileController::class);
    return $controller->getAllAdminProfiles($request, $response);
});
