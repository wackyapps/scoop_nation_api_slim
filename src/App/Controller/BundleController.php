<?php
declare(strict_types=1);
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\BundleRepository;

/**
 * BundleController - Handles HTTP requests for bundle operations
 * 
 * Controller responsible for managing product bundle-related API endpoints
 * including retrieval, product association, and price calculations
 */
class BundleController
{
    /**
     * @var BundleRepository $bundleRepository Repository for bundle data access
     */
    private $bundleRepository;

    /**
     * Constructor - Dependency injection of BundleRepository
     *
     * @param BundleRepository $bundleRepository The bundle repository instance
     */
    public function __construct(BundleRepository $bundleRepository)
    {
        $this->bundleRepository = $bundleRepository;
    }

    /**
     * Retrieve all bundles from the database
     *
     * GET /api/bundles
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @return Response JSON response containing all bundles
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Summer Ice Cream Pack",
     *       "discountedPrice": 2000,
     *       ...
     *     }
     *   ]
     * }
     */
    public function getAll(Request $request, Response $response): Response
    {
        $bundles = $this->bundleRepository->findAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $bundles
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieve a specific bundle by ID
     *
     * GET /api/bundles/{id}
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param array $args Route parameters containing bundle ID
     * @return Response JSON response containing the bundle data
     * 
     * @param int $args['id'] The ID of the bundle to retrieve
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Summer Ice Cream Pack",
     *     "discountedPrice": 2000,
     *     ...
     *   }
     * }
     */
    public function getById(Request $request, Response $response, array $args): Response
    {
        $bundleId = (int) $args['id'];
        $bundle = $this->bundleRepository->find($bundleId);
        
        if (!$bundle) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Bundle not found'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $bundle
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieve a bundle with all its associated products
     *
     * GET /api/bundles/{id}/products
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param array $args Route parameters containing bundle ID
     * @return Response JSON response containing bundle with products
     * 
     * @param int $args['id'] The ID of the bundle to retrieve with products
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Summer Ice Cream Pack",
     *       "discountedPrice": 2000,
     *       "productId": 1,
     *       "variantId": null,
     *       "title": "Chocolate Con Ice Cream",
     *       "price": 1000,
     *       "mainImage": "Wafer-Ice-Cream-PNG-Picture.png"
     *     }
     *   ]
     * }
     */
    public function getBundleWithProducts(Request $request, Response $response, array $args): Response
    {
        $bundleId = (int) $args['id'];
        $bundleWithProducts = $this->bundleRepository->getBundleWithProducts($bundleId);
        
        if (empty($bundleWithProducts)) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Bundle not found or has no products'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $bundleWithProducts
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Calculate price information for a bundle
     *
     * GET /api/bundles/{id}/pricing
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param array $args Route parameters containing bundle ID
     * @return Response JSON response containing pricing information
     * 
     * @param int $args['id'] The ID of the bundle to calculate pricing for
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "bundleId": 1,
     *     "bundleName": "Summer Ice Cream Pack",
     *     "actualPrice": 2500,
     *     "discountedPrice": 2000,
     *     "savingsAmount": 500,
     *     "savingsPercentage": 20.0,
     *     "savingsFormatted": "20%"
     *   }
     * }
     */
    public function getPricingInfo(Request $request, Response $response, array $args): Response
    {
        $bundleId = (int) $args['id'];
        $bundle = $this->bundleRepository->find($bundleId);
        
        if (!$bundle) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Bundle not found'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $actualPrice = $this->bundleRepository->calculateActualPrice($bundleId);
        $discountedPrice = (float) $bundle['discountedPrice'];
        $savingsAmount = $actualPrice - $discountedPrice;
        $savingsPercentage = $this->bundleRepository->calculateSavingsPercentage($bundleId);
        
        $pricingInfo = [
            'bundleId' => $bundleId,
            'bundleName' => $bundle['name'],
            'actualPrice' => $actualPrice,
            'discountedPrice' => $discountedPrice,
            'savingsAmount' => $savingsAmount,
            'savingsPercentage' => $savingsPercentage,
            'savingsFormatted' => number_format($savingsPercentage, 1) . '%'
        ];
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $pricingInfo
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Find bundles by name
     *
     * GET /api/bundles/search?name={name}
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @return Response JSON response containing matching bundles
     * 
     * @param string $request->getQueryParams()['name'] Bundle name search parameter
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Summer Ice Cream Pack",
     *       "discountedPrice": 2000,
     *       ...
     *     }
     *   ]
     * }
     */
    public function searchByName(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $name = $queryParams['name'] ?? '';
        
        if (empty($name)) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Name parameter is required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $bundle = $this->bundleRepository->findByName($name);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $bundle ? [$bundle] : []
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Get bundles that contain a specific product
     *
     * GET /api/bundles/product/{productId}
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param array $args Route parameters containing product ID
     * @return Response JSON response containing bundles with the product
     * 
     * @param int $args['productId'] The ID of the product to find in bundles
     * 
     * @response {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Summer Ice Cream Pack",
     *       "discountedPrice": 2000,
     *       ...
     *     }
     *   ]
     * }
     */
    public function getBundlesByProduct(Request $request, Response $response, array $args): Response
    {
        $productId = (int) $args['productId'];
        $bundles = $this->bundleRepository->getBundlesContainingProduct($productId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $bundles
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}