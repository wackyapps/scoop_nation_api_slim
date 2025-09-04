<?php
declare(strict_types=1);
namespace App\Repository;

class VariantRepository extends BaseRepository
{
    protected $table = 'variant';
    protected $primaryKey = 'id';

    public function findByProduct($productId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['productId' => $productId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findByNameAndValue($productId, $name, $value)
    {
        $query = "
            SELECT * FROM variant 
            WHERE productId = %i AND name = %s AND value = %s
        ";
        
        return DB::queryFirstRow($query, $productId, $name, $value);
    }
}