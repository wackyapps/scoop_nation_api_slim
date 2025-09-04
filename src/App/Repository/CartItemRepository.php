<?php
declare(strict_types=1);
namespace App\Repository;

class CartItemRepository extends BaseRepository
{
    protected $table = 'cart_item';
    protected $primaryKey = 'id';

    public function findByUser($userId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['userId' => $userId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function getUserCartWithProducts($userId): array
    {
        $query = "
            SELECT ci.*, p.title, p.price, p.mainImage, p.slug 
            FROM cart_item ci 
            JOIN product p ON ci.productId = p.id 
            WHERE ci.userId = %i
        ";
        
        return DB::query($query, $userId);
    }

    public function getCartTotal($userId): float
    {
        $query = "
            SELECT SUM(p.price * ci.quantity) as cart_total 
            FROM cart_item ci 
            JOIN product p ON ci.productId = p.id 
            WHERE ci.userId = %i
        ";
        
        $result = DB::queryFirstRow($query, $userId);
        return (float) ($result['cart_total'] ?? 0);
    }

    public function updateQuantity($userId, $productId, $variantId, $quantity)
    {
        DB::update(
            $this->table, 
            ['quantity' => $quantity], 
            'userId = %i AND productId = %i AND variantId = %i', 
            $userId, $productId, $variantId
        );
        
        return DB::affectedRows();
    }
}