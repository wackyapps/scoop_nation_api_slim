<?php
declare(strict_types=1);
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\ProductRepository;

class ProductController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    // Add this __invoke method that Slim expects
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        // This is a fallback method that can handle generic requests
        // or you can use it to route to specific methods based on the request
        $route = $request->getAttribute('route');
        $method = $route->getArgument('method', 'index');
        
        if (method_exists($this, $method)) {
            return $this->$method($request, $response, $args);
        }
        
        throw new \RuntimeException("Method {$method} not found");
    }

    public function getAll(Request $request, Response $response): Response
    {
        $products = $this->productRepository->findAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getByCategory(Request $request, Response $response, array $args): Response
    {
        $categoryId = (int) $args['categoryId'];
        $products = $this->productRepository->findByCategory($categoryId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function search(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $keyword = $queryParams['q'] ?? '';
        
        $products = $this->productRepository->search($keyword);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}