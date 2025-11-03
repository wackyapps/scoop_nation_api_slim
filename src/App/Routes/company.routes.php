<?php
// Updated products.routes.php
// Added routes for create, update, delete for admin panel operations

$app->post('/api/admin/update-company', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CompanyController::class);
    return $controller->updateCompany($request, $response);
});

$app->get('/api/admin/get-company', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CompanyController::class);
    return $controller->getCompany($request, $response);
});

// Layout Design Routes
$app->get('/api/admin/get-layout-design', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CompanyController::class);
    return $controller->getLayoutDesign($request, $response);
});

$app->post('/api/admin/update-layout-design', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\CompanyController::class);
    return $controller->updateLayoutDesign($request, $response);
});