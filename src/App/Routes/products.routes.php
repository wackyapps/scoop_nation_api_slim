<?php
// Updated products.routes.php
// Added routes for create, update, delete for admin panel operations

// $app->get('/api/admin/products', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->getAll($request, $response);
// });

// // get product by id
// $app->get('/api/admin/products/getById', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->getProductById($request, $response);
// });

// // Accepts an optional X-Branch-Id header to filter branch-specific products
// $app->get('/api/admin/products/category/{categoryId}', function ($request, $response, $args) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->getByCategory($request, $response, $args);
// });

// // Create new product (for admin)
// $app->post('/api/admin/products', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->create($request, $response);
// });

// // Update product (for admin)
// $app->post('/api/admin/products/update', function ($request, $response, ) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->update($request, $response);
// });

// // Delete product (for admin)
// $app->post('/api/admin/products/delete', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->delete($request, $response, );
// });

// Accepts an optional X-Branch-Id header to filter branch-specific products
// $app->get('/api/products/search', function ($request, $response) use ($app) {
//     $controller = $app->getContainer()->get(App\Controller\ProductController::class);
//     return $controller->search($request, $response);
// });
?>