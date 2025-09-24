<?php

namespace App\Services\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class JWTMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /**
     * Public APIs that do not require authentication
     */
    private $publicApis = [
        '/',
        '/api/users/login-customer',
        '/api/users/register-customer',
        // session routes
        '/api/sessions/start', // start new session http_method: POST
        '/api/sessions/cart', // get session cart items http_method: POST
        '/api/sessions/verify', // verify session http_method: POST
        '/api/sessions/cart/add', // add product to cart http_method: POST
        '/api/sessions/cart/remove', // remove product from cart http_method: POST
        '/api/sessions/cart/increase', // increase product quantity http_method: POST
        '/api/sessions/cart/decrease', // decrease product quantity http_method: POST
        '/api/sessions', // get session by id http_method: POST
        '/api/sessions/active', // todo: later make it authorized only for admin
        '/api/branch/homepage', // Added the exact path for query parameter version
        // contact us
        '/api/contact/submit', // Add this line
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        // Normalize path by removing trailing slash if present
        $normalizedPath = rtrim($path, '/');

        // Debug: Log the path (remove in production)
        error_log("Request Path: " . $normalizedPath . " Method: " . $method);

        // Check if the request is for a public API
        if ($this->isPublicApi($normalizedPath, $method)) {
            error_log("Public API detected, skipping authentication for: " . $normalizedPath);
            return $handler->handle($request);
        }

        // Get Bearer token from headers
        $authHeader = $request->getHeaderLine('Authorization');
        $token = trim(str_replace('Bearer', '', $authHeader));

        // If token is not set or empty, return 401 Unauthorized
        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Token not found',
                'message' => 'Authorization token is required'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $jwt = new JWT();

        // Validate token
        if (!$jwt->validate($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Invalid Token',
                'message' => 'Authorization token is invalid or expired'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Decode token and add to request attributes
        $decoded = $jwt->decodeJWT($token);
        $request = $request->withAttribute('user', $decoded);

        // Proceed to next middleware or route handler
        return $handler->handle($request);
    }

    /**
     * Check if the current request is for a public API
     */
    private function isPublicApi(string $path, string $method): bool
    {
        // Exact path matches
        if (in_array($path, $this->publicApis)) {
            return true;
        }

        // Regex pattern matches
        $publicPatterns = [
            // Parameterized branch homepage URL: /api/branches/{businessId}/{branchId}/homepage
            '#^/api/branches/[0-9]+/[0-9]+/homepage$#',
            
            // Query parameter branch homepage URL: /api/branch/homepage (handled by exact match above)
            // Additional patterns can be added here if needed
        ];

        foreach ($publicPatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}