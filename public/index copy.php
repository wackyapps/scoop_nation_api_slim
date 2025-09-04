<?php

// declare(strict_types=1);

// use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Message\ResponseInterface as Response;

// use Slim\Factory\AppFactory;

// require dirname(__DIR__) . '/vendor/autoload.php';
// require dirname(__DIR__) . '/src/App/DatabaseMeekro.php';

// $app = AppFactory::create();

// // root get path
// $app->get('/api/products', function (Request $request, Response $response, $args) {

//     $database = new App\DatabaseMeekro;
//     // Get the database connection
//     $db = $database::getConnection();

//     // Query the database for all products
//     $data = $db->query('SELECT * FROM product');
//     // $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


//     $body = json_encode($data);
//     $response->getBody()->write($body);

//     $response = $response->withHeader('Content-Type', 'application/json');

//     return $response;
// });

// // run the application
// $app->run();