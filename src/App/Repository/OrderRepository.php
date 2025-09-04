<?php
declare(strict_types=1);
namespace App\Repository;

class OrderRepository extends BaseRepository
{
    protected $table = 'order';
    protected $primaryKey = 'id';

    public function findByEmail(string $email, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['email' => $email];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findByStatus(string $status, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['status' => $status];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findWithItems($orderId)
    {
        $query = "
            SELECT o.*, oi.id as item_id, oi.productId, oi.quantity, p.title, p.price 
            FROM `order` o 
            LEFT JOIN order_item oi ON o.id = oi.customerOrderId 
            LEFT JOIN product p ON oi.productId = p.id 
            WHERE o.id = %i
        ";
        
        return DB::query($query, $orderId);
    }

    public function getTotalSales($startDate = null, $endDate = null): float
    {
        $query = "SELECT SUM(total) as total_sales FROM `order` WHERE status != 'cancelled'";
        $params = [];

        if ($startDate) {
            $query .= " AND dateTime >= %s";
            $params[] = $startDate;
        }

        if ($endDate) {
            $query .= " AND dateTime <= %s";
            $params[] = $endDate;
        }

        $result = DB::queryFirstRow($query, ...$params);
        return (float) ($result['total_sales'] ?? 0);
    }
}