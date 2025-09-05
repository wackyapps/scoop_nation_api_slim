<?php
// Swagger documentation routes
$app->get('/swagger.json', function ($request, $response) {
    $openapi = \OpenApi\Generator::scan([__DIR__ . '/../../App']);
    
    $response->getBody()->write($openapi->toJson());
    return $response->withHeader('Content-Type', 'application/json')
                   ->withHeader('Access-Control-Allow-Origin', '*');
});

$app->get('/api-docs', function ($request, $response) {
    $html = file_get_contents(__DIR__ . '/../../../public/swagger-ui.html');
    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});