<?php
declare(strict_types=1);
namespace App\Repository;

class WishlistRepository extends BaseRepository
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';

    public function findByUser($userId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['userId' => $userId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function userHasProduct($userId, $productId): bool
    {
        $query = "
            SELECT COUNT(*) as count 
            FROM wishlist 
            WHERE userId = %i AND productId = %i
        ";
        
        $result = DB::queryFirstRow($query, $userId, $productId);
        return (int) $result['count'] > 0;
    }

    public function getUserWishlistWithProducts($userId): array
    {
        $query = "
            SELECT w.*, p.title, p.price, p.mainImage, p.slug 
            FROM wishlist w 
            JOIN product p ON w.productId = p.id 
            WHERE w.userId = %i
        ";
        
        return DB::query($query, $userId);
    }
}