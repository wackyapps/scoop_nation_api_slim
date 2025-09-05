<?php
// Bundle routes - IMPORTANT: Define specific routes BEFORE parameterized routes
$app->get('/api/bundles', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\BundleController::class);
    return $controller->getAll($request, $response);
});

$app->get('/api/bundles/search', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\BundleController::class);
    return $controller->searchByName($request, $response);
});

$app->get('/api/bundles/product/{productId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\BundleController::class);
    return $controller->getBundlesByProduct($request, $response, $args);
});

// Parameterized routes should come AFTER specific routes
$app->get('/api/bundles/{id}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\BundleController::class);
    return $controller->getBundleWithProducts($request, $response, $args);
});

$app->get('/api/bundles/{id}/products', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\BundleController::class);
    return $controller->getPricingInfo($request, $response, $args);
});