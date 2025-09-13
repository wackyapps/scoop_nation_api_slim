<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories with banners and products
     */
    public function getAllCategoriesWithBannersAndProducts(Request $request, Response $response): Response
    {
        try {
            // Extract branch_id from request (e.g., header, query param, or session)
            $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

            $categories = $this->categoryRepository->getAllCategoriesWithBannersAndProducts($branchId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get specific category with banner and products
     */
    public function getCategoryWithBannerAndProducts(Request $request, Response $response): Response
    {
        try {
            $id = $request->getAttribute('id');
            $categoryId = (int) $id;
            
            if ($categoryId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
            // Extract branch_id from request
            $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

            $category = $this->categoryRepository->getCategoryWithBannerAndProducts($categoryId, $branchId);
            
            if (!$category) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Category not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve category: ' . $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get all categories (basic info)
     */
    public function getAllCategories(Request $request, Response $response): Response
    {
        try {
            // Extract branch_id from request
            $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

            $categories = $this->categoryRepository->getAllCategories($branchId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get category by ID (basic info)
     */
    public function getCategoryById(Request $request, Response $response): Response
    {
        try {
            $id = $request->getAttribute('id');
            $categoryId = (int) $id;
            
            if ($categoryId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
            // Extract branch_id from request
            $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

            $category = $this->categoryRepository->getCategoryById($categoryId, $branchId);
            
            if (!$category) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Category not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve category: ' . $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
