<?php
declare(strict_types=1);
namespace App\Repository;

use DB;

class OrderRepository extends BaseRepository
{
    protected $table = 'order';
    protected $primaryKey = 'id';

    public function findByEmail(string $email, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['email' => $email];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }
    public function getAllOrders(array $filters = [], $page = 1, $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = 'select o.id, o.customer_id,o.branch_id,o.rider_id,o.dateTime,o.status,o.total,o.orderNotice,o.order_number,o.address_id,c.fullname from `order` o INNER JOIN customer c on o.customer_id = c.id ';
        $countSql = 'select  count(DISTINCT o.id) as total from `order` o INNER JOIN customer c on o.customer_id = c.id ';        

        $params = [];
        if ($filters['search']) {
            $sql .= 'WHERE o.order_number LIKE %s OR c.fullname LIKE %s';
            $countSql .= 'WHERE o.order_number LIKE %s OR c.fullname LIKE %s';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        $sql .='limit  %i offset %i';
        $params2 = [...$params,$perPage, $offset];
        $orders = DB::query($sql, ...$params2);
        
        $total = DB::query($countSql, ...$params);
        return [
            'orders'=>$orders,
            'total'=>$total[0]['total'],
            'per_page'=>$perPage,
            'page'=>$page
        ];
    }
    
    public function findByCustomerId(int $customer_id): array
    {
        $criteria = ['customer_id' => $customer_id];
        $query = "SELECT * FROM `order` WHERE customer_id = %i";
        $params = [$customer_id];
        return $this->executeQuery($query, $params);
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