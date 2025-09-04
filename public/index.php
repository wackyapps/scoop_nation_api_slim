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

// Your routes here
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => 'ScoopNation API is running',
        'endpoints' => [
            '/api/products' => 'Get all products',
            '/api/products/{id}' => 'Get specific product'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Example route using repository through dependency injection
$app->get('/api/products', function (Request $request, Response $response) use ($container) {
    try {
        $productRepository = $container->get(ProductRepository::class);
        $products = $productRepository->findAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products,
            'count' => count($products)
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Run the application
$app->run();