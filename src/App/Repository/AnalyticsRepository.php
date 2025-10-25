<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class AnalyticsRepository extends BaseRepository
{
    protected $table = 'order';
    protected $primaryKey = 'id';

    /**
     * Get sales analytics for a date range
     */
    public function getSalesAnalytics($startDate, $endDate, $branchId = null)
    {
        $whereClause = "dateTime BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND branch_id = %i";
            $params[] = $branchId;
        }

        $query = "
            SELECT 
                COUNT(id) as total_orders,
                SUM(total) as total_sales,
                AVG(total) as average_order_value,
                DATE(dateTime) as order_date
            FROM `order`
            WHERE {$whereClause}
            GROUP BY DATE(dateTime)
            ORDER BY order_date ASC
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get total sales summary
     */
    public function getTotalSales($startDate, $endDate, $branchId = null)
    {
        $whereClause = "dateTime BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND branch_id = %i";
            $params[] = $branchId;
        }

        $query = "
            SELECT 
                COUNT(id) as total_orders,
                SUM(total) as total_sales,
                AVG(total) as average_order_value,
                MIN(total) as min_order_value,
                MAX(total) as max_order_value
            FROM `order`
            WHERE {$whereClause}
        ";

        return DB::queryFirstRow($query, ...$params);
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics($startDate, $endDate, $branchId = null)
    {
        $whereClause = "dateTime BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND branch_id = %i";
            $params[] = $branchId;
        }

        $query = "
            SELECT 
                status,
                COUNT(*) as count,
                SUM(total) as total_amount
            FROM `order`
            WHERE {$whereClause}
            GROUP BY status
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get customer registration statistics
     */
    public function getCustomerRegistrations($startDate, $endDate, $branchId = null)
    {
        $whereClause = "u.createdAt BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND u.branch_id = %i";
            $params[] = $branchId;
        }

        $query = "
            SELECT 
                DATE(u.createdAt) as registration_date,
                COUNT(DISTINCT c.id) as new_customers,
                COUNT(DISTINCT u.id) as new_users
            FROM user u
            LEFT JOIN customer c ON u.id = c.user_id
            WHERE {$whereClause}
            GROUP BY DATE(u.createdAt)
            ORDER BY registration_date ASC
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get total customer count
     */
    public function getTotalCustomers($startDate, $endDate, $branchId = null)
    {
        $whereClause = "u.createdAt BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND u.branch_id = %i";
            $params[] = $branchId;
        }

        $query = "
            SELECT 
                COUNT(DISTINCT c.id) as total_customers,
                COUNT(DISTINCT u.id) as total_users
            FROM user u
            LEFT JOIN customer c ON u.id = c.user_id
            WHERE {$whereClause}
        ";

        return DB::queryFirstRow($query, ...$params);
    }

    /**
     * Get most ordered products
     */
    public function getMostOrderedProducts($startDate, $endDate, $branchId = null, $limit = 10)
    {
        $whereClause = "o.dateTime BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND o.branch_id = %i";
            $params[] = $branchId;
        }

        $params[] = $limit;

        $query = "
            SELECT 
                p.id,
                p.title,
                p.mainImage,
                p.price,
                p.slug,
                c.name as category_name,
                SUM(oi.quantity) as total_quantity,
                COUNT(DISTINCT o.id) as order_count,
                SUM(oi.quantity * p.price) as total_revenue
            FROM order_item oi
            INNER JOIN `order` o ON oi.customerOrderId = o.id
            INNER JOIN product p ON oi.productId = p.id
            LEFT JOIN category c ON p.categoryId = c.id
            WHERE {$whereClause}
            GROUP BY p.id
            ORDER BY total_quantity DESC
            LIMIT %i
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get most ordered product variants
     */
    public function getMostOrderedVariants($startDate, $endDate, $branchId = null, $limit = 10)
    {
        $whereClause = "o.dateTime BETWEEN %s AND %s AND oi.variantId IS NOT NULL";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND o.branch_id = %i";
            $params[] = $branchId;
        }

        $params[] = $limit;

        $query = "
            SELECT 
                v.id,
                v.name as variant_name,
                v.value as variant_value,
                p.id as product_id,
                p.title as product_title,
                p.mainImage,
                v.price,
                SUM(oi.quantity) as total_quantity,
                COUNT(DISTINCT o.id) as order_count,
                SUM(oi.quantity * v.price) as total_revenue
            FROM order_item oi
            INNER JOIN `order` o ON oi.customerOrderId = o.id
            INNER JOIN variant v ON oi.variantId = v.id
            INNER JOIN product p ON v.productId = p.id
            WHERE {$whereClause}
            GROUP BY v.id
            ORDER BY total_quantity DESC
            LIMIT %i
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders($startDate, $endDate, $branchId = null, $limit = 20)
    {
        $whereClause = "o.dateTime BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND o.branch_id = %i";
            $params[] = $branchId;
        }

        $params[] = $limit;

        $query = "
            SELECT 
                o.id,
                o.order_number,
                o.dateTime,
                o.status,
                o.total,
                o.orderNotice,
                c.fullname as customer_name,
                u.email as customer_email,
                u.phone as customer_phone,
                b.name as branch_name,
                COUNT(oi.id) as item_count
            FROM `order` o
            LEFT JOIN customer c ON o.customer_id = c.id
            LEFT JOIN user u ON c.user_id = u.id
            LEFT JOIN branch b ON o.branch_id = b.id
            LEFT JOIN order_item oi ON o.id = oi.customerOrderId
            WHERE {$whereClause}
            GROUP BY o.id
            ORDER BY o.dateTime DESC
            LIMIT %i
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get sales by category
     */
    public function getSalesByCategory($startDate, $endDate, $branchId = null)
    {
        $whereClause = "o.dateTime BETWEEN %s AND %s";
        $params = [$startDate, $endDate];

        if ($branchId) {
            $whereClause .= " AND o.branch_id = %i";
            $params[] = $branchId;
        }

        $query = "
            SELECT 
                c.id as category_id,
                c.name as category_name,
                c.mainImage as category_image,
                COUNT(DISTINCT oi.id) as items_sold,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * p.price) as total_revenue
            FROM order_item oi
            INNER JOIN `order` o ON oi.customerOrderId = o.id
            INNER JOIN product p ON oi.productId = p.id
            INNER JOIN category c ON p.categoryId = c.id
            WHERE {$whereClause}
            GROUP BY c.id
            ORDER BY total_revenue DESC
        ";

        return DB::query($query, ...$params);
    }

    /**
     * Get revenue comparison with previous period
     */
    public function getRevenueComparison($startDate, $endDate, $branchId = null)
    {
        // Calculate previous period
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $diff = $start->diff($end);
        $days = $diff->days;

        $prevEnd = clone $start;
        $prevEnd->modify('-1 day');
        $prevStart = clone $prevEnd;
        $prevStart->modify("-{$days} days");

        $currentData = $this->getTotalSales($startDate, $endDate, $branchId);
        $previousData = $this->getTotalSales(
            $prevStart->format('Y-m-d H:i:s'),
            $prevEnd->format('Y-m-d H:i:s'),
            $branchId
        );

        $currentSales = $currentData['total_sales'] ?? 0;
        $previousSales = $previousData['total_sales'] ?? 0;

        $percentageChange = 0;
        if ($previousSales > 0) {
            $percentageChange = (($currentSales - $previousSales) / $previousSales) * 100;
        }

        return [
            'current' => $currentData,
            'previous' => $previousData,
            'percentage_change' => round($percentageChange, 2),
            'period_days' => $days
        ];
    }
}
