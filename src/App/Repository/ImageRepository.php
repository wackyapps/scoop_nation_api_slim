<?php
declare(strict_types=1);
namespace App\Repository;

class ImageRepository extends BaseRepository
{
    protected $table = 'image';
    protected $primaryKey = 'imageID';

    public function findByProduct($productId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['productID' => $productId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function getMainImage($productId)
    {
        $query = "
            SELECT i.* 
            FROM image i 
            JOIN product p ON i.productID = p.id 
            WHERE p.id = %i 
            LIMIT 1
        ";
        
        return DB::queryFirstRow($query, $productId);
    }
}