<?php


$app->get('/api/admin/orders', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\OrderController::class);
    return $controller->getAllOrders($request, $response);
});

$app->get('/api/admin/orders-details', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\OrderController::class);
    return $controller->getOrderDetails($request, $response);
});