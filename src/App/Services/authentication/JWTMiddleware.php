<?php

class JWTMiddleware extends \Slim\Middleware
{
    /**
     * public apis that do not require authentication
     */
    public $public_apis = [
        '/users/login',
        '/users/verify-otp',
        '/usersforgotpassword',
        '/userspasswordreset',
        // database back up related apis
        '/db/backup/create',
        '/db/backup/test-connection',
    ];

    public function call()
    {
        $app = $this->app;
        $req = $app->request();
        $res = $app->response();

        // Debug the incoming path
        $pathInfo = $req->getPathInfo();
        $pathInfo = $req->getResourceUri();
        // var_dump("Request Path: " . $pathInfo); // Log the path for debugging

        // if the request is for a public API, skip authentication
        if (in_array($pathInfo, $this->public_apis)) {
            error_log("Public API detected, skipping authentication for: " . $pathInfo);
            $this->next->call();
            return;
        }

        // getting Bearer token from headers
        $authHeader = $req->headers('Authorization');
        // set token
        $token = trim(str_replace('Bearer', '', $authHeader ?? ''));
        // if token is not set or empty then return 401 Unauthorized
        if (!$authHeader || empty($token)) {
            $app->response()->status(401);
            $app->response()->header('Content-Type', 'application/json');
            $app->response()->write(json_encode([
                'success' => false,
                "error" => "Token not found",
                "message" => "Authorization token is required"
            ]));
            return;
        }
        $jwt = new JWT();
        // validate token
        if (!$jwt->validate($token)) {
            $app->response()->status(401);
            $app->response()->header('Content-Type', 'application/json');
            $app->response()->write(json_encode([
                'success' => false,
                "error" => "Invalid Token",
                "message" => "Authorization token is invalid or expired"
            ]));
            return;
        }
        $decoded = $jwt->decodeJWT($token);
        // set user id in request
        $req->headers->set(key: 'user', value: $decoded);

        $this->next->call();
    }
}