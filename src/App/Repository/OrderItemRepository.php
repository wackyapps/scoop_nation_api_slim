<?php
declare(strict_types=1);
namespace App\Repository;

class OrderItemRepository extends BaseRepository
{
    protected $table = 'order_item';
    protected $primaryKey = 'id';

    public function findByOrder($orderId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['customerOrderId' => $orderId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findByProduct($productId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['productId' => $productId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function getOrderTotal($orderId): float
    {
        $query = "
            SELECT SUM(p.price * oi.quantity) as order_total 
            FROM order_item oi 
            JOIN product p ON oi.productId = p.id 
            WHERE oi.customerOrderId = %i
        ";
        
        $result = DB::queryFirstRow($query, $orderId);
        return (float) ($result['order_total'] ?? 0);
    }
}