<?php
// Products routes
$app->get('/api/products', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\ProductController::class);
    return $controller->getAll($request, $response);
});

$app->get('/api/products/category/{categoryId}', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\ProductController::class);
    return $controller->getByCategory($request, $response, $args);
});

$app->get('/api/products/search', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\ProductController::class);
    return $controller->search($request, $response);
});