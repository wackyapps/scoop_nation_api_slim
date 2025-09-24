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
        '/api/sessions/start',
        '/api/sessions/cart',
        '/api/sessions/verify',
        '/api/sessions/cart/add',
        '/api/sessions/cart/remove',
        '/api/sessions/cart/increase',
        '/api/sessions/cart/decrease',
        '/api/sessions',
        '/api/sessions/active',
        '/api/branch/homepage',
        // contact us
        '/api/contact/submit',
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        $path   = $request->getUri()->getPath();

        // === Determine base path ===
        // Preferred: use SCRIPT_NAME (eg "/scoopnation_api/public/index.php") and strip "index.php"
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '');
        $basePath = '';

        if ($scriptName !== '') {
            // remove trailing "index.php" if present
            $basePath = rtrim(str_replace('index.php', '', $scriptName), '/');
        }

        // If basePath equals empty string, nothing to strip
        if ($basePath && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        // Normalize path
        $normalizedPath = rtrim($path, '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        // Debug logging (safe to keep temporarily)
        error_log("JWTMiddleware -> SCRIPT_NAME: [$scriptName] basePath: [$basePath] path: [" . $request->getUri()->getPath() . "] normalized: [$normalizedPath] method: [$method]");

        // Check if the request is for a public API
        if ($this->isPublicApi($normalizedPath, $method)) {
            error_log("Public API detected, skipping authentication for: " . $normalizedPath);
            return $handler->handle($request);
        }

        // Get Bearer token from headers (make regex resilient to spacing/case)
        $authHeader = $request->getHeaderLine('Authorization');
        $token = '';
        if (preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
            $token = trim($m[1]);
        } else {
            // fallback: strip literal 'Bearer' if present
            $token = trim(str_ireplace('Bearer', '', $authHeader));
        }

        // If token is not set or empty, return 401 Unauthorized
        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error'   => 'Token not found',
                'message' => 'Authorization token is required for: ' . $normalizedPath,
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Validate token (your JWT class should exist and implement validate/decodeJWT)
        $jwt = new JWT();

        if (!$jwt->validate($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error'   => 'Invalid Token',
                'message' => 'Authorization token is invalid or expired',
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
        if (in_array($path, $this->publicApis, true)) {
            return true;
        }

        // Regex pattern matches
        $publicPatterns = [
            // Parameterized branch homepage URL: /api/branches/{businessId}/{branchId}/homepage
            '#^/api/branches/[0-9]+/[0-9]+/homepage$#',
        ];

        foreach ($publicPatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
