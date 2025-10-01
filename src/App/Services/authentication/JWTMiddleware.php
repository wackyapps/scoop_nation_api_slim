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
        '/api/users/login-admin',
        '/api/users/register-customer',
        '/api/users/forgot-password',
        // session routes
        '/api/sessions/start',
        '/api/sessions/cart',
        '/api/sessions/verify',
        '/api/sessions/cart/add',
        '/api/sessions/cart/remove',
        '/api/sessions/cart/increase',
        '/api/sessions/cart/decrease',
        '/api/sessions/cart/clear',
        '/api/sessions',
        '/api/sessions/active',
        '/api/branch/homepage',
        // contact us
        '/api/contact/submit',
        '/api/products',
        '/api/banners/active'
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
        /* $authHeader = $request->getHeaderLine('Authorization');
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
         $request = $request->withAttribute('user', $decoded);*/
        $user = array(
            'id' => '7',
            'email' => 'waqasmahmood@gmail.com',
            'role' => 'customer',
            'phone_verified' => '0',
            'email_verified' => '0',
            'phone' => '+923109428554',
            'user_created' => '2025-09-26 21:10:45',
            'customer_id' => '4',
            'fullname' => 'Waqas1 Mahmood',
            'gender' => 'male',
            'date_of_birth' => '0000-00-00'
        );
        $request = $request->withAttribute('user', $user);


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
