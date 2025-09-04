<?php
declare(strict_types=1);
namespace App\Repository;

class BundleRepository extends BaseRepository
{
    protected $table = 'bundle';
    protected $primaryKey = 'id';

    public function findByName(string $name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function getBundleWithProducts($bundleId): array
    {
        $query = "
            SELECT b.*, bp.productId, bp.variantId, p.title, p.price, p.mainImage 
            FROM bundle b 
            LEFT JOIN bundle_product bp ON b.id = bp.bundleId 
            LEFT JOIN product p ON bp.productId = p.id 
            WHERE b.id = %i
        ";
        
        return DB::query($query, $bundleId);
    }

    public function calculateActualPrice($bundleId): float
    {
        $query = "
            SELECT SUM(p.price) as total_price 
            FROM bundle_product bp 
            JOIN product p ON bp.productId = p.id 
            WHERE bp.bundleId = %i
        ";
        
        $result = DB::queryFirstRow($query, $bundleId);
        return (float) ($result['total_price'] ?? 0);
    }
}