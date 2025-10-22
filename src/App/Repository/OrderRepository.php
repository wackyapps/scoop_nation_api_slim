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
        $sql = 'select o.id, o.customer_id,o.branch_id,o.rider_id,o.dateTime,o.status,o.total,o.orderNotice,o.order_number,o.address_id,c.fullname,o.customer_address from `order` o INNER JOIN customer c on o.customer_id = c.id ';
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
        
        $total = DB::queryFirstRow($countSql, ...$params);

        $totalCount = $total['total'] ??0;
        return [
            'orders'=>$orders,
            'total'=>$totalCount,
            'per_page'=>$perPage,
            'total_pages'=>ceil($totalCount / $perPage),
            'page'=>$page
        ];
    }

    public function getOrderByOrderId(int $orderId): ?array{
        $query = "select o.id, o.customer_id,o.branch_id,o.rider_id,o.dateTime,o.status,o.total,o.orderNotice,o.order_number,o.address_id,c.fullname,o.customer_address,u.email,u.phone from `order` o INNER JOIN customer c on o.customer_id = c.id INNER JOIN user u on u.id = c.user_id WHERE o.id = %i";
        $params = [$orderId];
        $order = DB::queryFirstRow($query, ...$params);
        if (!$order) {
            return null;
        }
        return $order;
    }
    
    public function updateOrder(int $orderId, array $data): bool
    {
        $allowedFields = ['status', 'rider_id'];
        $updateData = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateData[$key] = $value;
            }
        }

        if (empty($updateData)) {
            return false;
        }


        DB::update($this->table, $updateData, "id=%i", $orderId);


        return DB::affectedRows() > 0;
    }

    public function findByCustomerId(int $customer_id,int $page,int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT o.*, c.fullname FROM `order` o INNER JOIN customer c ON o.customer_id = c.id WHERE o.customer_id = %i LIMIT %i OFFSET %i";
        $countQuery = "SELECT COUNT(*) as total FROM `order` WHERE customer_id = %i";

        $params = [$customer_id, $perPage, $offset];
        $countParams = [$customer_id];
        $orders = DB::query($query, ...$params);
        $total = DB::queryFirstRow($countQuery, ...$countParams);
        
        $totalCount = $total['total'] ?? 0;
        
        return [
            'orders' => $orders,
            'total' => $totalCount,
            'per_page' => $perPage,
            'total_pages' => ceil($totalCount / $perPage),
            'page' => $page
        ];
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