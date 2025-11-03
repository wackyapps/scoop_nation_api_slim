<?php
declare(strict_types=1);

use App\Controller\CategoryController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

// ============= ADMIN ROUTES =============

// Get all categories for admin panel with pagination and search
$app->get('/api/admin/categories', [CategoryController::class, 'getAllCategoriesAdmin']);

// Get category by ID for admin panel
$app->get('/api/admin/categories/getById', [CategoryController::class, 'getCategoryByIdAdmin']);

// Create new category
$app->post('/api/admin/categories/create', [CategoryController::class, 'createCategory']);

// Update category
$app->post('/api/admin/categories/update', [CategoryController::class, 'updateCategory']);

// Delete category
$app->post('/api/admin/categories/delete', [CategoryController::class, 'deleteCategory']);
