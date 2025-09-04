<?php
declare(strict_types=1);
namespace App\Repository;

require_once __DIR__ . '/../meekrodb/db.class.php';

use DB; // Import the MeekroDB class

/**
 * BundleRepository - Data access layer for bundle entities
 * 
 * Provides methods for retrieving, managing, and calculating prices for product bundles
 * Extends BaseRepository for common CRUD operations
 */
class BundleRepository extends BaseRepository
{
    /**
     * @var string $table Database table name for bundles
     */
    protected $table = 'bundle';
    
    /**
     * @var string $primaryKey Primary key column name
     */
    protected $primaryKey = 'id';

    /**
     * Find a bundle by its name
     *
     * @param string $name The name of the bundle to search for
     * @return array|null Returns the bundle data as an associative array or null if not found
     * 
     * @example
     * $bundle = $bundleRepository->findByName('Summer Ice Cream Pack');
     */
    public function findByName(string $name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Retrieve a bundle with all its associated products and variants
     *
     * @param int $bundleId The ID of the bundle to retrieve with products
     * @return array Bundle data with associated products and variants information
     * 
     * @example
     * $bundleWithProducts = $bundleRepository->getBundleWithProducts(1);
     * 
     * @return array[] Returns array containing:
     *   - bundle details
     *   - associated product IDs and variant IDs
     *   - product titles, prices, and main images
     */
    public function getBundleWithProducts($bundleId): array
    {
        $query = "
            SELECT b.*, bp.productId, bp.variantId, p.title, p.price, p.mainImage 
            FROM bundle b 
            LEFT JOIN bundle_product bp ON b.id = bp.bundleId 
            LEFT JOIN product p ON bp.productId = p.id 
            WHERE b.id = %i
        ";
        
        return $this->executeQuery($query, [$bundleId]);
    }

    /**
     * Calculate the actual total price of all products in a bundle
     *
     * This method sums up the individual prices of all products included in the bundle
     * to determine the actual retail value before any bundle discount is applied
     *
     * @param int $bundleId The ID of the bundle to calculate price for
     * @return float The total sum of all product prices in the bundle
     * 
     * @example
     * $actualPrice = $bundleRepository->calculateActualPrice(1);
     * // Returns: 2500 (if bundle contains products totaling $25.00)
     * 
     * @throws \RuntimeException If the database query fails
     */
    public function calculateActualPrice($bundleId): float
    {
        $query = "
            SELECT SUM(p.price) as total_price 
            FROM bundle_product bp 
            JOIN product p ON bp.productId = p.id 
            WHERE bp.bundleId = %i
        ";
        
        $result = $this->executeQueryFirstRow($query, [$bundleId]);
        return (float) ($result['total_price'] ?? 0);
    }

    /**
     * Calculate the savings percentage for a bundle
     *
     * Compares the bundle's discounted price against the actual total price
     * of all individual products to determine the savings percentage
     *
     * @param int $bundleId The ID of the bundle to calculate savings for
     * @return float The percentage savings (0-100) or 0 if no savings
     * 
     * @example
     * $savings = $bundleRepository->calculateSavingsPercentage(1);
     * // Returns: 20.0 (20% savings)
     */
    public function calculateSavingsPercentage($bundleId): float
    {
        $bundle = $this->find($bundleId);
        if (!$bundle) {
            return 0.0;
        }

        $actualPrice = $this->calculateActualPrice($bundleId);
        $discountedPrice = (float) $bundle['discountedPrice'];

        if ($actualPrice <= 0 || $discountedPrice >= $actualPrice) {
            return 0.0;
        }

        return (($actualPrice - $discountedPrice) / $actualPrice) * 100;
    }

    /**
     * Get bundles that contain a specific product
     *
     * @param int $productId The ID of the product to search for in bundles
     * @return array Array of bundles that contain the specified product
     * 
     * @example
     * $bundles = $bundleRepository->getBundlesContainingProduct(5);
     */
    public function getBundlesContainingProduct($productId): array
    {
        $query = "
            SELECT b.* 
            FROM bundle b 
            JOIN bundle_product bp ON b.id = bp.bundleId 
            WHERE bp.productId = %i
            GROUP BY b.id
        ";
        
        return $this->executeQuery($query, [$productId]);
    }
}