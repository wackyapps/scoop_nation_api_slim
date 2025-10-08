<?php
// Updated ProductController.php
// Added create, update, delete methods
// Enhanced getAll and getProductById to include variants and media
// Injected VariantRepository and MediaRepository
// Uses BaseRepository methods: save (for insert), update, delete
// For deletions of multiples (variants, media), uses custom deleteByProductId added to repos
// For file uploads, handles multipart/form-data
// For media, assumes only one media per product for simplicity (as per "one media"), but code handles multiple if present
// Media upload validates image/video MIME types
// Slug generation with basic uniqueness check
// Assumes media table has 'productID' and 'type' columns for linking

declare(strict_types=1);
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\ProductRepository;
use App\Repository\VariantRepository;
use App\Repository\MediaRepository;

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
     * @var VariantRepository $variantRepository Repository for variant data access
     */
    private $variantRepository;

    /**
     * @var MediaRepository $mediaRepository Repository for media data access
     */
    private $mediaRepository;

    /**
     * Constructor - Dependency injection of repositories
     *
     * @param ProductRepository $productRepository The product repository instance
     * @param VariantRepository $variantRepository The variant repository instance
     * @param MediaRepository $mediaRepository The media repository instance
     */
    public function __construct(ProductRepository $productRepository, VariantRepository $variantRepository, MediaRepository $mediaRepository)
    {
        $this->productRepository = $productRepository;
        $this->variantRepository = $variantRepository;
        $this->mediaRepository = $mediaRepository;
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
     * Supports pagination, sorting via query params for datatable use
     * Includes variants and media in response
     *
     * GET /api/products?limit=10&offset=0&orderBy={"price":"ASC"}
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
     *       "variants": [...],
     *       "media": [...],
     *       ...
     *     }
     *   ],
     *   "count": 10
     * }
     */

    /**
     * Get all products
     * 
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             ),
     *             @OA\Property(property="count", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getAll(Request $request, Response $response): Response
    {
        // Extract branch_id from request (e.g., header, query param, or session)
        $branchId = $request->getHeaderLine('X-Branch-Id') ? (int) $request->getHeaderLine('X-Branch-Id') : null;

        // Get query params for pagination/sorting
        $queryParams = $request->getQueryParams();
        $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : 10;
        $page = isset($queryParams['page']) ? (int) $queryParams['page'] : 1;
        $orderBy = isset($queryParams['orderBy']) ? json_decode($queryParams['orderBy'], true) : null;
        $search = isset($queryParams['search']) ? $queryParams['search'] : null;

        $products = $this->productRepository->getAllProducts($branchId, $search, $orderBy, $limit, $page);
        $total = $this->productRepository->getAllProductsCount($branchId, $search, $orderBy);
        $result = [];
        foreach ($products as $product) {
            $product['variants'] = $this->variantRepository->findByProduct((int) $product['id']);
            $product['media'] = $this->mediaRepository->findMediaByProductId((int) $product['id']);
            $result[] = $product;
        }




        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $result,
            'pagination' => [
                'limit' => $limit,
                'page' => $page,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Get product by ID
     * Includes variants and media
     * 
     * @OA\Get(
     *     path="/api/products/{productId}",
     *     summary="Get product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getProductById(Request $request, Response $response): Response
    {

        $productId = (int) $request->getQueryParams()['productId'] ?? null;

        if (!$productId) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'productId is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        // Extract branch_id from request

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Product not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $product['variants'] = $this->variantRepository->findByProduct((int) $product['id']);
        $product['media'] = $this->mediaRepository->findMediaByProductId((int) $product['id']);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $product
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

    /**
     * Get products by category
     * 
     * @OA\Get(
     *     path="/api/products/category/{categoryId}",
     *     summary="Get products by category",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getByCategory(Request $request, Response $response, array $args): Response
    {
        $categoryId = (int) $args['categoryId'];
        // Extract branch_id from request
        $branchId = $request->getHeaderLine('X-Branch-Id') ? (int) $request->getHeaderLine('X-Branch-Id') : null;

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

    /**
     * Search products
     * 
     * @OA\Get(
     *     path="/api/products/search",
     *     summary="Search products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */

    public function search(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $keyword = $queryParams['q'] ?? '';
        // Extract branch_id from request
        $branchId = $request->getHeaderLine('X-Branch-Id') ? (int) $request->getHeaderLine('X-Branch-Id') : null;

        $products = $this->productRepository->search($keyword, $branchId);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $products
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Create a new product with variants and media
     * Requires at least one variant and one media file
     * Media file uploaded to public/media/products
     * Expects multipart/form-data with fields: title, description, price, categoryId, variants (JSON string array), media (file)
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @return Response JSON response with created product
     */
    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        // Parse variants if sent as JSON string
        $variants = !empty($data['variants']) ? json_decode($data['variants'], true) : [];

        // Validate required fields
        $errors = [];
        if (empty($data['title'])) {
            $errors[] = 'Title is required.';
        }
        if (empty($data['description'])) {
            $errors[] = 'Description is required.';
        }
        if (empty($data['price'])) {
            $errors[] = 'Price is required.';
        }
        if (empty($data['categoryId'])) {
            $errors[] = 'Category ID is required.';
        }
        if (!is_array($variants) || count($variants) < 1) {
            $errors[] = 'At least one variant is required and must be a valid JSON array.';
        }
        if (empty($files['file']) || $files['file']->getError() !== UPLOAD_ERR_OK) {
            $errors[] = 'Media file is required and must be a valid upload.';
        }
        if (!empty($errors)) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Missing or invalid data', 'details' => $errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validate media type (image or video)
        $mime = $files['file']->getClientMediaType();
        if (!str_starts_with($mime, 'image/') && !str_starts_with($mime, 'video/')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid file type. Must be image or video']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Generate unique slug
        $slug = $this->generateUniqueSlug($data['title']);
        $mediaFile = $files['file'];
        $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', uniqid(), $extension);
        $directory = __DIR__ . '/../../../public/media/products/';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $mediaFile->moveTo($directory . $filename);
        $path = 'media/products/' . $filename;
        // Insert product
        $productId = $this->productRepository->save([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'],
            'price' => (int) $data['price'],
            'categoryId' => (int) $data['categoryId'],
            'mainImage' => $path
        ]);

        // Insert variants
        foreach ($variants as $variant) {
            $this->variantRepository->save([
                'productId' => $productId,
                'name' => $variant['name'],
                'value' => $variant['value'],
                'price' => (int) ($variant['price'] ?? 0),
            ]);
        }

        // Handle media upload


        // Insert media
        $this->mediaRepository->save([
            'image' => $path,
            'productID' => $productId,
            'type' => 'product',
            'mime_type' => $mime,
        ]);

        // Return created product
        $product = $this->productRepository->findById((int) $productId);
        $product['variants'] = $this->variantRepository->findByProduct($productId);
        $product['media'] = $this->mediaRepository->findMediaByProductId($productId);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $product, 'message' => 'Product created successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /**
     * Update an existing product
     * Allows updating fields, replacing variants, and optionally replacing media
     * If new media provided, deletes old media and file
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param int $productId Product ID to update
     * @return Response JSON response with updated product
     */
    public function update(Request $request, Response $response, ): Response
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['productId'])) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'productId is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $productId = (int) $queryParams['productId'];


        // Check if product exists
        if (!$this->productRepository->findById($productId)) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Product not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Parse variants if provided
        $variants = !empty($data['variants']) ? json_decode($data['variants'], true) : null;
        $media = !empty($data['media']) ? json_decode($data['media'], true) : [];

        // Update product fields if provided
        $updateData = [];
        if (!empty($data['title']))
            $updateData['title'] = $data['title'];
        if (!empty($data['description']))
            $updateData['description'] = $data['description'];
        if (!empty($data['price']))
            $updateData['price'] = (int) $data['price'];
        if (!empty($data['categoryId']))
            $updateData['categoryId'] = (int) $data['categoryId'];

        if (!empty($updateData)) {
            if (isset($updateData['title'])) {
                $updateData['slug'] = $this->generateUniqueSlug($updateData['title'], $productId);
            }
            $this->productRepository->update($productId, $updateData);
        }

        // Update variants if provided (replace all)
        if (is_array($variants)) {
            $this->variantRepository->deleteByProductId($productId);
            foreach ($variants as $variant) {
                $this->variantRepository->save([
                    'productId' => $productId,
                    'name' => $variant['name'] ?? '',
                    'value' => $variant['value'] ?? '',
                    'price' => (int) ($variant['price'] ?? 0),
                ]);
            }
        }

        // --- Media sync logic ---
        // 1. Get current media records
            $currentMedias = $this->mediaRepository->findMediaByProductId($productId); // array of db rows
            $mediaToKeep = [];
            if (!empty($data['media']) && is_array($media)) {
                $mediaToKeep = $media;// array of image paths or IDs to keep
            }


            // 2. Delete media not in data['media']
            foreach ($currentMedias as $media) {
                // Use image path for comparison (adjust if you use IDs)
                $imageIDsToKeep = array_column($mediaToKeep, 'imageID');

                if (!in_array($media['imageID'], $imageIDsToKeep)) {
                    $filePath = __DIR__ . '/../../../public/' . $media['image'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // Delete from DB
                    $this->mediaRepository->delete($media['imageID']);
                }
            }

        // 3. Add new uploaded files (support multiple)
        $mediaFiles = [];
        if (!empty($files['file'])) {
            if (is_array($files['file'])) {
                foreach ($files['file'] as $file) {
                    if ($file && $file->getError() === UPLOAD_ERR_OK) {
                        $mediaFiles[] = $file;
                    }
                }
            } else {
                if ($files['file']->getError() === UPLOAD_ERR_OK) {
                    $mediaFiles[] = $files['file'];
                }
            }
        }

        $directory = __DIR__ . '/../../../public/media/products/';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        foreach ($mediaFiles as $mediaFile) {
            $mime = $mediaFile->getClientMediaType();
            if (!str_starts_with($mime, 'image/') && !str_starts_with($mime, 'video/')) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid media type. Must be image or video']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('%s.%s', uniqid(), $extension);
            $mediaFile->moveTo($directory . $filename);
            $path = 'media/products/' . $filename;
            $this->mediaRepository->save([
                'image' => $path,
                'productID' => $productId,
                'type' => 'product',
                'mime_type' => $mime,
            ]);
        }

        // Return updated product
        $product = $this->productRepository->findById($productId);
        $product['variants'] = $this->variantRepository->findByProduct($productId);
        $product['media'] = $this->mediaRepository->findMediaByProductId($productId);

        $response->getBody()->write(json_encode(['success' => true, 'data' => $product]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Delete a product and associated variants/media
     * Also deletes physical media files
     *
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @param int $productId Product ID to delete
     * @return Response JSON response confirming deletion
     */
    public function delete(Request $request, Response $response): Response
    {
        $productId = (int) $request->getQueryParams()['productId'] ?? null;

        if (!$productId) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'productId is required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        //

        // Check if product exists
        if (!$this->productRepository->findById($productId)) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Product not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Delete media and files
        $medias = $this->mediaRepository->findMediaByProductId($productId);
        foreach ($medias as $media) {
            $filePath = __DIR__ . '/../../../public/' . $media['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $this->mediaRepository->deleteByProductId($productId);

        // Delete variants
        $this->variantRepository->deleteByProductId($productId);

        // Delete product
        $this->productRepository->delete($productId);

        $response->getBody()->write(json_encode(['success' => true, "message" => "Product deleted successfully"]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Generate a unique slug based on title
     * Appends number if duplicate
     *
     * @param string $title Product title
     * @param int|null $excludeId ID to exclude for uniqueness check
     * @return string Unique slug
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($title)));
        $slug = $baseSlug;
        $count = 1;

        while (true) {
            $existing = $this->productRepository->findBy(['slug' => $slug]);
            if (empty($existing) || ($excludeId && $existing[0]['id'] == $excludeId)) {
                break;
            }
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }
}
?>