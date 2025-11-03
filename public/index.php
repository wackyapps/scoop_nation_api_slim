<?php
declare(strict_types=1);

use App\Services\CORS\CORSMiddleware;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Middleware\BodyParsingMiddleware;
use App\Services\Authentication\JWTMiddleware;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/App/Constants.php';
require __DIR__ . '/../src/App/Repository/SQL_Table_Names.php';
require __DIR__ . '/../src/App/meekrodb/db.class.php';

// require for JWT and CORS Middleware classes (if not autoloaded)
require_once __DIR__ . '/../src/App/Services/Authentication/JWTMiddleware.php';
require_once __DIR__ . '/../src/App/Services/CORS/CORSMiddleware.php';

// --------------------------------------------------
// Configure MeekroDB
// --------------------------------------------------
DB::$host = DB_HOST;
DB::$user = DB_USER;
DB::$password = DB_PASS;
DB::$dbName = DB_NAME;
DB::$port = DB_PORT;
DB::$encoding = DB_CHARSET;

// --------------------------------------------------
// Build PHP-DI container instance
// --------------------------------------------------
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$container = $containerBuilder->build();

// --------------------------------------------------
// Create Slim app instance with PHP-DI bridge
// --------------------------------------------------
$app = Bridge::create($container);

// --------------------------------------------------
// Auto-detect base path so Slim routes match production sub-folder on Hostinger
// --------------------------------------------------
// This avoids hardcoding hostnames or folder names.
// Example: if SCRIPT_NAME = /api/public/index.php, dirname -> /api/public
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir  = str_replace('\\', '/', dirname($scriptName));
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
if ($basePath) {
    $app->setBasePath($basePath);
}

// --------------------------------------------------
// Optional debug dump (uncomment to write request server vars to tmp/srv.txt)
// --------------------------------------------------
// Make sure the tmp folder exists and is writable if you enable this.
//file_put_contents(__DIR__ . '/../tmp/srv.txt', print_r([
//    'REQUEST_URI'  => $_SERVER['REQUEST_URI'] ?? null,
//    'SCRIPT_NAME'  => $_SERVER['SCRIPT_NAME'] ?? null,
//    'PATH_INFO'    => $_SERVER['PATH_INFO'] ?? null,
//    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? null,
//], true));

// --------------------------------------------------
// Middlewares
// ORDER MATTERS:
// - Add CORS first so it can respond to preflight OPTIONS before auth middleware rejects it.
// - Add BodyParsing (Slim built-in).
// - Add JWT auth middleware (applies to protected routes).
// --------------------------------------------------

// Add CORS middleware (should run early to handle OPTIONS)
$app->add(new CORSMiddleware());

// Add body parsing middleware (Slim built-in)
$app->addBodyParsingMiddleware();

// Add JWT Middleware (authentication)
$app->add(new JWTMiddleware());

// Optional: Add Slim error middleware (set displayErrorDetails = true for debugging; false in prod)
$app->addErrorMiddleware(true, true, true);

// Disable cache for all responses (you can remove/modify as needed)
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withHeader('Pragma', 'no-cache')
        ->withHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
});

// --------------------------------------------------
// Preflight OPTIONS route (catch-all) — returns quickly with appropriate headers
// --------------------------------------------------
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    // CORS middleware should already add necessary headers; ensure 200 returned for preflight
    return $response->withStatus(200);
});

// --------------------------------------------------
// Root route
// --------------------------------------------------
$app->get('/', function (Request $request, Response $response) {
    $payload = [
        'message' => 'ScoopNation API is running',
        'endpoints' => [
            '/api/categories' => 'Get all categories (basic info)',
            '/api/categories/with-products' => 'Get all categories with banners and products',
            '/api/categories/{id}' => 'Get category by ID (basic info)',
            '/api/categories/{id}/with-products' => 'Get category with banner and products',
            '/api/products' => 'Get all products',
            '/api/products/category/{categoryId}' => 'Get products by category',
            '/api/products/search' => 'Search products',
            '/api/bundles' => 'Get all bundles',
            '/api/bundles/search' => 'Search bundles by name',
            '/api/bundles/{id}' => 'Get bundle with products',
            '/api/bundles/{id}/products' => 'Get bundle pricing info',
            '/api/bundles/product/{productId}' => 'Get bundles by product',
            '/api/users' => 'User management endpoints',
            '/api/customers' => 'Customer management endpoints',
            '/api/banners/active' => 'Get all active banner campaigns for today with banners and meta'
        ]
    ];

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json');
});

// --------------------------------------------------
// Make $app available to route files (if your routes reference $app as global)
global $app;

// --------------------------------------------------
// Load route files
// --------------------------------------------------
require __DIR__ . '/../src/App/Routes/category.routes.php';
require __DIR__ . '/../src/App/Routes/products.routes.php';
require __DIR__ . '/../src/App/Routes/bundles.routes.php';
require __DIR__ . '/../src/App/Routes/user.routes.php';
require __DIR__ . '/../src/App/Routes/customer.routes.php';
require __DIR__ . '/../src/App/Routes/banner.routes.php';
require __DIR__ . '/../src/App/Routes/session.routes.php';
require __DIR__ . '/../src/App/Routes/branch.routes.php';
require __DIR__ . '/../src/App/Routes/contact.routes.php';
require __DIR__ . '/../src/App/Routes/order.routes.php';
require __DIR__ . '/../src/App/Routes/company.routes.php';
require __DIR__ . '/../src/App/Routes/email_subscription.routes.php';
require __DIR__ . '/../src/App/Routes/email_templates.routes.php';
require __DIR__ . '/../src/App/Routes/analytics.routes.php';
require __DIR__ . '/../src/App/Routes/admin_profile.routes.php';


// --------------------------------------------------
// Run the application
// --------------------------------------------------
$app->run();
