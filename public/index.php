<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
use App\DatabaseMeekro;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/App/Constants.php';
require dirname(__DIR__) . '/src/App/meekrodb/db.class.php';
require dirname(__DIR__) . '/src/App/DatabaseMeekro.php';

$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Initialize database connection
DatabaseMeekro::initialize();

// Root route
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode([
        'message' => 'ScoopNation API is running',
        'endpoints' => [
            '/api/products' => 'Get all products',
            '/api/products/{id}' => 'Get specific product'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// API products route - USING STATIC CALLS
$app->get('/api/products', function (Request $request, Response $response, $args) {
    try {
        // Use MeekroDB static calls directly
        $data = \DB::query('SELECT * FROM product');
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $data,
            'count' => count($data)
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

// API product by ID route - USING STATIC CALLS
$app->get('/api/products/{id}', function (Request $request, Response $response, $args) {
    try {
        $id = $args['id'];
        $product = \DB::queryFirstRow('SELECT * FROM product WHERE id = %s', $id);
        
        if ($product) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $product
            ]));
        } else {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Product not found'
            ]));
            return $response->withStatus(404);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Catch-all route for 404 errors
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'success' => false,
        'error' => 'Route not found'
    ]));
    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
});

// Run the application
$app->run();