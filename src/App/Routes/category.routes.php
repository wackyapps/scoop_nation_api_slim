<?php
declare(strict_types=1);

use App\Controller\CategoryController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// No need for RouteCollectorProxy or group() here since you're not nesting routes.

// Get all categories (basic info)
// Accepts an optional X-Branch-Id header to filter branch-specific categories
$app->get('/api/categories', [CategoryController::class, 'getAllCategories']);

// Get all categories with banners and products
// Accepts an optional X-Branch-Id header to filter branch-specific categories
$app->get('/api/categories/with-products', [CategoryController::class, 'getAllCategoriesWithBannersAndProducts']);

// Get category by ID (basic info)
// Accepts an optional X-Branch-Id header to filter branch-specific categories
$app->get('/api/categories/{id}', [CategoryController::class, 'getCategoryById']);

// Get specific category with banner and products
// Accepts an optional X-Branch-Id header to filter branch-specific categories
$app->get('/api/categories/{id}/with-products', [CategoryController::class, 'getCategoryWithBannerAndProducts']);