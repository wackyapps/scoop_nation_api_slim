<?php
declare(strict_types=1);
namespace App\Repository;

/**
 * ProductRepository - Data access layer for product entities
 * 
 * Provides methods for retrieving, searching, and managing product data
 * Extends BaseRepository for common CRUD operations
 */
class ProductRepository extends BaseRepository
{
    /**
     * @var string $table Database table name
     */
    protected $table = 'product';
    
    /**
     * @var string $primaryKey Primary key column name
     */
    protected $primaryKey = 'id';

    /**
     * Find a product by its slug
     *
     * @param string $slug The unique slug identifier of the product
     * @param int|null $branchId The ID of the branch to filter, or null for all
     * @return array|null Returns the product data as an associative array or null if not found
     * 
     * @example
     * $product = $productRepository->findBySlug('chocolate-con-ice-cream', 1);
     */
    public function findBySlug(string $slug, ?int $branchId = null)
    {
        $query = "SELECT * FROM {$this->table} WHERE slug = %s" . ($branchId ? " AND EXISTS (SELECT 1 FROM branch_product bp WHERE bp.product_id = {$this->table}.id AND (bp.branch_id = %i OR bp.branch_id IS NULL))" : "");
        $params = [$slug];
        if ($branchId) $params[] = $branchId;
        return $this->executeQueryFirstRow($query, $params) ?: null;
    }

    /**
     * Find products by category ID with optional sorting and pagination
     * Filters by branch_id if provided using branch_product
     *
     * @param int $categoryId The ID of the category to filter products by
     * @param int|null $branchId The ID of the branch to filter, or null for all
     * @param array|null $orderBy Associative array of field => direction for sorting
     * @param int|null $limit Maximum number of records to return
     * @param int|null $offset Number of records to skip for pagination
     * @return array Array of product data matching the category
     * 
     * @example
     * $products = $productRepository->findByCategory(1, 1, ['price' => 'ASC'], 10, 0);
     */
    public function findByCategory(int $categoryId, ?int $branchId = null, array $orderBy = null, $limit = null, $offset = null): array
    {
        $query = "SELECT p.* FROM {$this->table} p WHERE p.categoryId = %i" . ($branchId ? " AND EXISTS (SELECT 1 FROM branch_product bp WHERE bp.product_id = p.id AND (bp.branch_id = %i OR bp.branch_id IS NULL))" : "");
        $params = [$categoryId];
        if ($branchId) $params[] = $branchId;

        if ($orderBy) {
            $query .= " ORDER BY ";
            $orders = [];
            foreach ($orderBy as $field => $direction) {
                $orders[] = "{$field} {$direction}";
            }
            $query .= implode(', ', $orders);
        }

        if ($limit) {
            $query .= " LIMIT %i";
            $params[] = $limit;
        }

        if ($offset) {
            $query .= " OFFSET %i";
            $params[] = $offset;
        }

        return $this->executeQuery($query, $params);
    }

    /**
     * Search products by keyword in title or description with optional sorting and pagination
     * Filters by branch_id if provided using branch_product
     *
     * @param string $keyword Search term to match against product title and description
     * @param int|null $branchId The ID of the branch to filter, or null for all
     * @param array|null $orderBy Associative array of field => direction for sorting
     * @param int|null $limit Maximum number of records to return
     * @param int|null $offset Number of records to skip for pagination
     * @return array Array of products matching the search criteria
     * 
     * @example
     * $products = $productRepository->search('chocolate', 1, ['title' => 'ASC'], 20, 0);
     */
    public function search(string $keyword, ?int $branchId = null, array $orderBy = null, $limit = null, $offset = null): array
    {
        $query = "SELECT p.* FROM {$this->table} p WHERE (title LIKE %ss OR description LIKE %ss)" . ($branchId ? " AND EXISTS (SELECT 1 FROM branch_product bp WHERE bp.product_id = p.id AND (bp.branch_id = %i OR bp.branch_id IS NULL))" : "");
        $params = ["%{$keyword}%", "%{$keyword}%"];
        if ($branchId) $params[] = $branchId;

        if ($orderBy) {
            $query .= " ORDER BY ";
            $orders = [];
            foreach ($orderBy as $field => $direction) {
                $orders[] = "{$field} {$direction}";
            }
            $query .= implode(', ', $orders);
        }

        if ($limit) {
            $query .= " LIMIT %i";
            $params[] = $limit;
        }

        if ($offset) {
            $query .= " OFFSET %i";
            $params[] = $offset;
        }

        return $this->executeQuery($query, $params);
    }

    /**
     * Find a product with its associated images
     *
     * @param int $productId The ID of the product to retrieve with images
     * @param int|null $branchId The ID of the branch to filter, or null for all
     * @return array Product data with associated images or empty array if not found
     * 
     * @example
     * $productWithImages = $productRepository->findWithImages(1, 1);
     */
    public function findWithImages(int $productId, ?int $branchId = null)
    {
        $query = "
            SELECT p.*, i.imageID, i.image 
            FROM product p 
            LEFT JOIN image i ON p.id = i.productID 
            WHERE p.id = %i" . ($branchId ? " AND EXISTS (SELECT 1 FROM branch_product bp WHERE bp.product_id = p.id AND (bp.branch_id = %i OR bp.branch_id IS NULL))" : "");
        $params = [$productId];
        if ($branchId) $params[] = $branchId;
        
        return $this->executeQuery($query, $params);
    }

    /**
     * Get all products from the database
     * Filters by branch_id if provided using branch_product
     *
     * @param int|null $branchId The ID of the branch to filter, or null for all
     * @return array Array of all products in the database
     * 
     * @example
     * $allProducts = $productRepository->getAllProducts(1);
     * 
     * @see BaseRepository::findAll()
     */
    public function getAllProducts(?int $branchId = null)
    {
        $query = "SELECT * FROM {$this->table}" . ($branchId ? " WHERE EXISTS (SELECT 1 FROM branch_product bp WHERE bp.product_id = {$this->table}.id AND (bp.branch_id = %i OR bp.branch_id IS NULL))" : "");
        return $branchId ? $this->executeQuery($query, [$branchId]) : $this->findAll();
    }
}