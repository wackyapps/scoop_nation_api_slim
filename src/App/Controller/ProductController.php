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