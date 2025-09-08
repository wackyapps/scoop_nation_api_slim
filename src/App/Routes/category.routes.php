<?php

use App\Controller\CategoryController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

// Category routes
$app->group('/api/categories', function (RouteCollectorProxy $group) {
    
    // Get all categories with banners and products
    $group->get('/with-products', CategoryController::class . ':getAllCategoriesWithBannersAndProducts');
    
    // Get specific category with banner and products
    $group->get('/{id}/with-products', CategoryController::class . ':getCategoryWithBannerAndProducts');
    
    // Get all categories (basic info)
    $group->get('', CategoryController::class . ':getAllCategories');
    
    // Get category by ID (basic info)
    $group->get('/{id}', CategoryController::class . ':getCategoryById');

})->add(function ($request, $handler) {
    // Optional: Add middleware for authentication/authorization if needed
    return $handler->handle($request);
});