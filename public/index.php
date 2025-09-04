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

// Load dependencies from the dependencies.php file
$containerBuilder->addDefinitions(__DIR__ . '/../src/App/dependencies.php');

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
            '/api/products/search' => 'Search products'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Routes using ProductController
$app->get('/api/products', [ProductController::class, 'getAll']);
$app->get('/api/products/category/{categoryId}', [ProductController::class, 'getByCategory']);
$app->get('/api/products/search', [ProductController::class, 'search']);

// Run the application
$app->run();