<?php
declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/App/Constants.php';
require __DIR__ . '/../src/App/meekrodb/db.class.php';

// Configure MeekroDB
DB::$host = DB_HOST;
DB::$user = DB_USER;
DB::$password = DB_PASS;
DB::$dbName = DB_NAME;
DB::$port = DB_PORT;
DB::$encoding = DB_CHARSET;

// Build PHP-DI container instance
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$container = $containerBuilder->build();

// Create Slim app instance with PHP-DI bridge
$app = Bridge::create($container);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Root route
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => 'ScoopNation API is running',
        'endpoints' => [
            '/api/products' => 'Get all products',
            '/api/products/category/{categoryId}' => 'Get products by category',
            '/api/products/search' => 'Search products',
            '/api/bundles' => 'Get all bundles',
            '/api/bundles/{id}' => 'Get bundle with products',
            '/api/bundles/{id}/products' => 'Get bundle pricing info',
            '/api/bundles/search' => 'Search bundles by name',
            '/api/bundles/product/{productId}' => 'Get bundles by product'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Routes using ProductController with closures
$app->get('/api/products', function (Request $request, Response $response) use ($container) {
    $controller = $container->get(App\Controller\ProductController::class);
    return $controller->getAll($request, $response);
});

$app->get('/api/products/category/{categoryId}', function (Request $request, Response $response, array $args) use ($container) {
    $controller = $container->get(App\Controller\ProductController::class);
    return $controller->getByCategory($request, $response, $args);
});

$app->get('/api/products/search', function (Request $request, Response $response) use ($container) {
    $controller = $container->get(App\Controller\ProductController::class);
    return $controller->search($request, $response);
});

// Routes using BundleController with closures
$app->get('/api/bundles', function (Request $request, Response $response) use ($container) {
    $controller = $container->get(App\Controller\BundleController::class);
    return $controller->getAll($request, $response);
});

$app->get('/api/bundles/{id}', function (Request $request, Response $response, array $args) use ($container) {
    $controller = $container->get(App\Controller\BundleController::class);
    return $controller->getBundleWithProducts($request, $response, $args);
});

$app->get('/api/bundles/{id}/products', function (Request $request, Response $response, array $args) use ($container) {
    $controller = $container->get(App\Controller\BundleController::class);
    return $controller->getPricingInfo($request, $response, $args);
});

$app->get('/api/bundles/search', function (Request $request, Response $response) use ($container) {
    $controller = $container->get(App\Controller\BundleController::class);
    return $controller->searchByName($request, $response);
});

$app->get('/api/bundles/product/{productId}', function (Request $request, Response $response, array $args) use ($container) {
    $controller = $container->get(App\Controller\BundleController::class);
    return $controller->getBundlesByProduct($request, $response, $args);
});

// Run the application
$app->run();