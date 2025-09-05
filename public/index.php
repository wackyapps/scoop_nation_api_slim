<?php
declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Middleware\BodyParsingMiddleware;

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

// Add CORS middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Handle preflight OPTIONS requests
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

// Add body parsing middleware
$app->addBodyParsingMiddleware();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Disable cache middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    
    return $response
        ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withHeader('Pragma', 'no-cache')
        ->withHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
});

// Root route
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'message' => 'ScoopNation API is running',
        'endpoints' => [
            '/api/products' => 'Get all products',
            '/api/products/category/{categoryId}' => 'Get products by category',
            '/api/products/search' => 'Search products',
            '/api/bundles' => 'Get all bundles',
            '/api/bundles/search' => 'Search bundles by name',
            '/api/bundles/{id}' => 'Get bundle with products',
            '/api/bundles/{id}/products' => 'Get bundle pricing info',
            '/api/bundles/product/{productId}' => 'Get bundles by product'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Make $app available to route files
global $app;

// Load route files and pass the container
require __DIR__ . '/../src/App/Routes/products.routes.php';
require __DIR__ . '/../src/App/Routes/bundles.routes.php';

// Run the application
$app->run();