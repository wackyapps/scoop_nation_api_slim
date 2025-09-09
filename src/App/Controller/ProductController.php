<?php
declare(strict_types=1);
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\ProductRepository;

/**
 * ProductController - Handles HTTP requests for product operations
 * 
 * Controller responsible for managing product-related API endpoints
 * including retrieval, category filtering, and search functionality
 */
class ProductController
{
    /**
     * @var ProductRepository $productRepository Repository for product data access
     */
    private $productRepository;

    /**
     * Constructor - Dependency injection of ProductRepository
     *
     * @param ProductRepository $productRepository The product repository instance
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Magic method handler for Slim framework routing
     *
     * This method allows the controller to be called as a callable
     * and routes to appropriate methods based on route arguments
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param array $args Route parameters
     * @return Response Formatted HTTP response
     * @throws \RuntimeException When the requested method doesn't exist
     */
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

    /**
     * Retrieve all products from the database
     *
     * GET /api/products
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @return Response JSON response containing all products
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Chocolate Con Ice Cream",
     *       "slug": "chocolate-con-ice-cream",
     *       "price": 1000,
     *       ...
     *     }
     *   ]
     * }
     */
    public function getAll(Request $request, Response $response): Response
    {
        // Extract branch_id from request (e.g., header, query param, or session)
        $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

        $products = $this->productRepository->getAllProducts($branchId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieve products by category ID
     *
     * GET /api/products/category/{categoryId}
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param array $args Route parameters containing categoryId
     * @return Response JSON response containing products in the specified category
     * 
     * @param int $args['categoryId'] The ID of the category to filter by
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Chocolate Con Ice Cream",
     *       "categoryId": 1,
     *       ...
     *     }
     *   ]
     * }
     */
    public function getByCategory(Request $request, Response $response, array $args): Response
    {
        $categoryId = (int) $args['categoryId'];
        // Extract branch_id from request
        $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

        $products = $this->productRepository->findByCategory($categoryId, $branchId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Search products by keyword in title or description
     *
     * GET /api/products/search?q={keyword}
     *
     * @param Request $request The PSR-7 request object containing query parameters
     * @param Response $response The PSR-7 response object
     * @return Response JSON response containing products matching the search criteria
     * 
     * @param string $request->getQueryParams()['q'] Search keyword parameter
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Chocolate Con Ice Cream",
     *       "description": "This is a chocolate con icecream",
     *       ...
     *     }
     *   ]
     * }
     * 
     * @example
     * Search for products containing "chocolate"
     * GET /api/products/search?q=chocolate
     */
    public function search(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $keyword = $queryParams['q'] ?? '';
        // Extract branch_id from request
        $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

        $products = $this->productRepository->search($keyword, $branchId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}