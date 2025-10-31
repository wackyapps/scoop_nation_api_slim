<?php
// Branch routes - IMPORTANT: Define specific routes BEFORE parameterized routes

/**
 * @OA\Get(
 *     path="/api/branches/{businessId}/{branchId}/homepage",
 *     summary="Get branch homepage data",
 *     description="Retrieves complete branch homepage data including branch info, business info, timings, banners, products, and bundles.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="businessId",
 *         in="path",
 *         required=true,
 *         description="Business ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */

// $app->get('/api/branches/{businessId}/{branchId}/homepage', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getBranchHomePage($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/branches/{branchId}/summary",
 *     summary="Get branch summary",
 *     description="Retrieves minimal branch data for quick lookups.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/branches/{branchId}/summary', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getBranchSummary($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/branches/{branchId}/timings/{dayOfWeek}",
 *     summary="Get branch timing by day",
 *     description="Retrieves branch operating hours for a specific day of the week.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="dayOfWeek",
 *         in="path",
 *         required=true,
 *         description="Day of week (1=Monday, 7=Sunday)",
 *         @OA\Schema(type="integer", minimum=1, maximum=7)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/branches/{branchId}/timings/{dayOfWeek}', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getBranchTimingByDay($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/businesses/{businessId}/branches",
 *     summary="Get active branches by business",
 *     description="Retrieves all active branches for a business.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="businessId",
 *         in="path",
 *         required=true,
 *         description="Business ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/businesses/{businessId}/branches', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getActiveBranchesByBusiness($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/branches/{branchId}/is-open",
 *     summary="Check if branch is open",
 *     description="Checks if a branch is currently open based on its timings.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/branches/{branchId}/is-open', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->isBranchOpen($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/branches/{branchId}/timings",
 *     summary="Get all branch timings",
 *     description="Retrieves all weekly timings for a branch.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/branches/{branchId}/timings', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getBranchTimings($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/branches/{branchId}/banners",
 *     summary="Get active banner campaigns for branch",
 *     description="Retrieves active banner campaigns with their media for a branch.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/branches/{branchId}/banners', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getActiveBannerCampaignsForBranch($request, $response, $args);
// });

/**
 * @OA\Get(
 *     path="/api/branches/{branchId}/products",
 *     summary="Get available products for branch",
 *     description="Retrieves all available products with branch-specific pricing and availability.",
 *     tags={"Branches"},
 *     @OA\Parameter(
 *         name="branchId",
 *         in="path",
 *         required=true,
 *         description="Branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
// $app->get('/api/branches/{branchId}/products', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\BranchController::class);
//     return $controller->getAvailableProductsForBranch($request, $response, $args);
// });

// Branch parameterized routes 

/**
 * '/api/branch/homepage?businessId=[0-9]+&branchId=[0-9]+' // Added to match parameterized URLs
 */

/**
 * @OA\Get(
 *     path="/api/branch/homepage",
 *     summary="Get branch homepage data",
 *     description="Retrieves complete branch homepage data including branch info, business info, timings, banners, products, and bundles.",
 *     tags={"Branches"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
$app->get('/api/branch/homepage', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\BranchController::class);
    return $controller->getBranchHomepageData($request, $response);
});