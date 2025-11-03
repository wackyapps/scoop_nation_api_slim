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
        '/api/users/login-admin',
        '/api/users/forgot-password',
        '/api/users/reset-password'
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        // === Determine base path ===
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '');
        $basePath = '';

        if ($scriptName !== '') {
            $basePath = rtrim(str_replace('index.php', '', $scriptName), '/');
        }

        if ($basePath && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        $normalizedPath = rtrim($path, '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        error_log("JWTMiddleware -> SCRIPT_NAME: [$scriptName] basePath: [$basePath] path: [" . $request->getUri()->getPath() . "] normalized: [$normalizedPath] method: [$method]");

        // Check if the request is for a public API
        if ($this->isPublicApi($normalizedPath, $method)) {
            error_log("Public API detected, skipping authentication for: " . $normalizedPath);
            return $handler->handle($request);
        }


        // === TOKEN CHECKING DISABLED TEMPORARILY ===
        // Get Bearer token from headers
       /*$authHeader = $request->getHeaderLine('Authorization');
         $token = '';
         if (preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
             $token = trim($m[1]);
         } else {
             $token = trim(str_ireplace('Bearer', '', $authHeader));
         }

         if (empty($token)) {
             $response = new Response();
             $response->getBody()->write(json_encode([
                 'success' => false,
                 'error'   => 'Token not found',
                 'message' => 'Authorization token is required for: ' . $normalizedPath,
             ]));
             return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
         }

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
         
         $decoded = $jwt->decodeJWT($token);
         if (!$decoded ||  $decoded['role'] !== 'administrator' ) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error'   => 'Unauthorized',
                'message' => 'User does not have the required role',
            ]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
         }

         $request = $request->withAttribute('user', $decoded);*/

        // Proceed to next middleware or route handler
        return $handler->handle($request);
    }

    /**
     * Check if the current request is for a public API
     */
    private function isPublicApi(string $path, string $method): bool
    {
        if (in_array($path, $this->publicApis, true)) {
            return true;
        }

        $publicPatterns = [
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
