<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

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

    /**
     * Get all categories for admin panel with pagination and search
     */
    public function getAllCategoriesAdmin(Request $request, Response $response): Response
    {
        try {
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);
            $search = ($request->getQueryParams()['search'] ?? null);

            $categories = $this->categoryRepository->getAllCategoriesAdmin(1, $search, $limit, $page);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categories['data'],
                'pagination' => [
                    'limit' => $limit,
                    'page' => $page,
                    'total_pages' => ceil((int) $categories['total'] / $limit),
                    'total' => $categories['total'],
                ],
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
     * Get category by ID for admin panel
     */
    public function getCategoryByIdAdmin(Request $request, Response $response): Response
    {
        try {
            $categoryId = (int) $request->getQueryParams()['categoryId'] ?? null;
            if (!$categoryId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Missing categoryId in query parameters'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $category = $this->categoryRepository->findByPkId($categoryId);

            if (empty($category)) {
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
     * Create a new category
     */
    public function createCategory(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $files = $request->getUploadedFiles();

            // Validate required fields
            $requiredFields = ['name'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            if (!isset($files['file'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'File upload is required']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Validate file upload
            $mime = $files['file']->getClientMediaType();
            if (!str_starts_with($mime, 'image/')) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid file type. Must be image']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Upload image
            $mediaFile = $files['file'];
            $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('%s.%s', uniqid(), $extension);
            $directory = __DIR__ . '/../../../public/media/categories/';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $mediaFile->moveTo($directory . $filename);
            $path = 'media/categories/' . $filename;


            $optionalFields = [];
            if (isset($data['priority'])) {
                $optionalFields['priority'] = $data['priority'];
            }
            // Create the category
            $categoryId = $this->categoryRepository->save([
                'name' => $data['name'],
                'mainImage' => $path,
                'branch_id' => 1, // Not required for now as per your instruction
                ...$optionalFields
            ]);

            if (!$categoryId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to create category'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categoryId,
                'message' => 'Category created successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Update an existing category
     */
    public function updateCategory(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $files = $request->getUploadedFiles();

            // Validate required fields
            $requiredFields = ['id'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            // Check if category exists
            $category = $this->categoryRepository->findByPkId((int) $data['id']);
            if (!$category) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Category not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Build update data
            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['priority'])) {
                $updateData['priority'] = $data['priority'];
            }

            // Handle image update
            if (!empty($files['file']) && $files['file']->getError() === UPLOAD_ERR_OK) {
                $mime = $files['file']->getClientMediaType();
                if (!str_starts_with($mime, 'image/')) {
                    $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid file type. Must be image']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }

                // Delete old image file
                if (!empty($category['mainImage'])) {
                    $oldFilePath = __DIR__ . '/../../../public/' . $category['mainImage'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Upload new image
                $mediaFile = $files['file'];
                $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
                $filename = sprintf('%s.%s', uniqid(), $extension);
                $directory = __DIR__ . '/../../../public/media/categories/';
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }
                $mediaFile->moveTo($directory . $filename);
                $path = 'media/categories/' . $filename;
                $updateData['mainImage'] = $path;
            }

            if (!empty($updateData)) {
                $updateCategory = $this->categoryRepository->update((int) $data['id'], $updateData);
                if (!$updateCategory) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Failed to update category'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Category updated successfully',
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Delete a category
     */
    public function deleteCategory(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);

            // Validate required fields
            $requiredFields = ['id'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            // Check if category exists
            $category = $this->categoryRepository->findByPkId((int) $data['id']);
            if (!$category) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Category not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Delete image file
            if (!empty($category['mainImage'])) {
                $filePath = __DIR__ . '/../../../public/' . $category['mainImage'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete category
            $deletedCategory = $this->categoryRepository->delete((int) $data['id']);
            if (!$deletedCategory) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to delete category'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
