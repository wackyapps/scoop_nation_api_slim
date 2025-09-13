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
        '/users/login',
        '/users/verify-otp',
        '/usersforgotpassword',
        '/userspasswordreset',
        // database backup related APIs
        '/db/backup/create',
        '/db/backup/test-connection',
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        // Debug: Log the path (remove in production)
        error_log("Request Path: " . $path);

        // If the request is for a public API, skip authentication
        if (in_array($path, $this->publicApis)) {
            error_log("Public API detected, skipping authentication for: " . $path);
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
}