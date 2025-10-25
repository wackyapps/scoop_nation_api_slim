<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controller\AnalyticsController;

global $app;

// Analytics routes group
$app->group('/api/analytics', function () use ($app) {
    
    // Get comprehensive dashboard analytics
    // Supports: ?preset=today|this_week|this_month|last_30_days|this_year
    // OR: ?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
    // Optional: &branch_id=1
    $app->get('/dashboard', function (Request $request, Response $response) {
        $controller = new AnalyticsController();
        return $controller->getDashboardAnalytics($request, $response);
    });

    // Get sales analytics with daily breakdown
    $app->get('/sales', function (Request $request, Response $response) {
        $controller = new AnalyticsController();
        return $controller->getSalesAnalytics($request, $response);
    });

    // Get customer registration analytics
    $app->get('/customers', function (Request $request, Response $response) {
        $controller = new AnalyticsController();
        return $controller->getCustomerAnalytics($request, $response);
    });

    // Get top/most ordered products
    // Optional: &limit=10
    $app->get('/top-products', function (Request $request, Response $response) {
        $controller = new AnalyticsController();
        return $controller->getTopProducts($request, $response);
    });

    // Get recent orders
    // Optional: &limit=20
    $app->get('/recent-orders', function (Request $request, Response $response) {
        $controller = new AnalyticsController();
        return $controller->getRecentOrders($request, $response);
    });

    // Get sales by category
    $app->get('/sales-by-category', function (Request $request, Response $response) {
        $controller = new AnalyticsController();
        return $controller->getSalesByCategory($request, $response);
    });
});
