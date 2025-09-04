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
     * @return array|null Returns the product data as an associative array or null if not found
     * 
     * @example
     * $product = $productRepository->findBySlug('chocolate-con-ice-cream');
     */
    public function findBySlug(string $slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Find products by category ID with optional sorting and pagination
     *
     * @param int $categoryId The ID of the category to filter products by
     * @param array|null $orderBy Associative array of field => direction for sorting
     *                            Example: ['name' => 'ASC', 'price' => 'DESC']
     * @param int|null $limit Maximum number of records to return
     * @param int|null $offset Number of records to skip for pagination
     * @return array Array of product data matching the category
     * 
     * @example
     * $products = $productRepository->findByCategory(1, ['price' => 'ASC'], 10, 0);
     */
    public function findByCategory($categoryId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['categoryId' => $categoryId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Search products by keyword in title or description with optional sorting and pagination
     *
     * @param string $keyword Search term to match against product title and description
     * @param array|null $orderBy Associative array of field => direction for sorting
     * @param int|null $limit Maximum number of records to return
     * @param int|null $offset Number of records to skip for pagination
     * @return array Array of products matching the search criteria
     * 
     * @example
     * $products = $productRepository->search('chocolate', ['title' => 'ASC'], 20, 0);
     */
    public function search(string $keyword, array $orderBy = null, $limit = null, $offset = null): array
    {
        $query = "SELECT * FROM {$this->table} WHERE title LIKE %ss OR description LIKE %ss";
        $params = ["%{$keyword}%", "%{$keyword}%"];

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
     * @return array Product data with associated images or empty array if not found
     * 
     * @example
     * $productWithImages = $productRepository->findWithImages(1);
     */
    public function findWithImages($productId)
    {
        $query = "
            SELECT p.*, i.imageID, i.image 
            FROM product p 
            LEFT JOIN image i ON p.id = i.productID 
            WHERE p.id = %i
        ";
        
        return $this->executeQuery($query, [$productId]);
    }

    /**
     * Get all products from the database
     *
     * Alias for findAll() method from BaseRepository
     * 
     * @return array Array of all products in the database
     * 
     * @example
     * $allProducts = $productRepository->getAllProducts();
     * 
     * @see BaseRepository::findAll()
     */
    public function getAllProducts() {
        return $this->findAll();
    }
}