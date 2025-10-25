<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\AnalyticsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AnalyticsController
{
    private $analyticsRepository;

    public function __construct()
    {
        $this->analyticsRepository = new AnalyticsRepository();
    }

    /**
     * Helper to calculate date ranges based on preset
     */
    private function calculateDateRange($preset)
    {
        $now = new \DateTime();
        $startDate = clone $now;
        $endDate = clone $now;

        switch ($preset) {
            case 'today':
                $startDate->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;

            case 'this_week':
                $startDate->modify('monday this week')->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;

            case 'this_month':
                $startDate->modify('first day of this month')->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;

            case 'last_30_days':
                $startDate->modify('-30 days')->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;

            case 'this_year':
                $startDate->setDate((int)$now->format('Y'), 1, 1)->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
                break;

            default:
                // Default to last 30 days
                $startDate->modify('-30 days')->setTime(0, 0, 0);
                $endDate->setTime(23, 59, 59);
        }

        return [
            'start' => $startDate->format('Y-m-d H:i:s'),
            'end' => $endDate->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get comprehensive dashboard analytics
     */
    public function getDashboardAnalytics(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        
        // Handle date range
        $dateRange = null;
        if (isset($params['preset'])) {
            $dateRange = $this->calculateDateRange($params['preset']);
        } elseif (isset($params['start_date']) && isset($params['end_date'])) {
            $dateRange = [
                'start' => $params['start_date'],
                'end' => $params['end_date']
            ];
        } else {
            // Default to last 30 days
            $dateRange = $this->calculateDateRange('last_30_days');
        }

        $branchId = $params['branch_id'] ?? null;
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        try {
            // Get all analytics data
            $salesSummary = $this->analyticsRepository->getTotalSales($startDate, $endDate, $branchId);
            $customerSummary = $this->analyticsRepository->getTotalCustomers($startDate, $endDate, $branchId);
            $orderStats = $this->analyticsRepository->getOrderStatistics($startDate, $endDate, $branchId);
            $topProducts = $this->analyticsRepository->getMostOrderedProducts($startDate, $endDate, $branchId, 10);
            $recentOrders = $this->analyticsRepository->getRecentOrders($startDate, $endDate, $branchId, 10);
            $revenueComparison = $this->analyticsRepository->getRevenueComparison($startDate, $endDate, $branchId);

            $data = [
                'success' => true,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                    'preset' => $params['preset'] ?? 'custom'
                ],
                'sales' => [
                    'total_sales' => (float)($salesSummary['total_sales'] ?? 0),
                    'total_orders' => (int)($salesSummary['total_orders'] ?? 0),
                    'average_order_value' => (float)($salesSummary['average_order_value'] ?? 0),
                    'min_order_value' => (float)($salesSummary['min_order_value'] ?? 0),
                    'max_order_value' => (float)($salesSummary['max_order_value'] ?? 0),
                ],
                'customers' => [
                    'total_customers' => (int)($customerSummary['total_customers'] ?? 0),
                    'total_users' => (int)($customerSummary['total_users'] ?? 0),
                ],
                'orders' => [
                    'by_status' => $orderStats
                ],
                'comparison' => $revenueComparison,
                'top_products' => $topProducts,
                'recent_orders' => $recentOrders
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Failed to fetch dashboard analytics',
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get sales analytics with daily breakdown
     */
    public function getSalesAnalytics(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        
        $dateRange = null;
        if (isset($params['preset'])) {
            $dateRange = $this->calculateDateRange($params['preset']);
        } elseif (isset($params['start_date']) && isset($params['end_date'])) {
            $dateRange = [
                'start' => $params['start_date'],
                'end' => $params['end_date']
            ];
        } else {
            $dateRange = $this->calculateDateRange('last_30_days');
        }

        $branchId = $params['branch_id'] ?? null;

        try {
            $salesData = $this->analyticsRepository->getSalesAnalytics(
                $dateRange['start'],
                $dateRange['end'],
                $branchId
            );

            $data = [
                'success' => true,
                'date_range' => [
                    'start' => $dateRange['start'],
                    'end' => $dateRange['end']
                ],
                'sales_by_date' => $salesData
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Failed to fetch sales analytics',
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get customer registration analytics
     */
    public function getCustomerAnalytics(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        
        $dateRange = null;
        if (isset($params['preset'])) {
            $dateRange = $this->calculateDateRange($params['preset']);
        } elseif (isset($params['start_date']) && isset($params['end_date'])) {
            $dateRange = [
                'start' => $params['start_date'],
                'end' => $params['end_date']
            ];
        } else {
            $dateRange = $this->calculateDateRange('last_30_days');
        }

        $branchId = $params['branch_id'] ?? null;

        try {
            $customerData = $this->analyticsRepository->getCustomerRegistrations(
                $dateRange['start'],
                $dateRange['end'],
                $branchId
            );

            $customerSummary = $this->analyticsRepository->getTotalCustomers(
                $dateRange['start'],
                $dateRange['end'],
                $branchId
            );

            $data = [
                'success' => true,
                'date_range' => [
                    'start' => $dateRange['start'],
                    'end' => $dateRange['end']
                ],
                'summary' => $customerSummary,
                'registrations_by_date' => $customerData
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Failed to fetch customer analytics',
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get top products
     */
    public function getTopProducts(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        
        $dateRange = null;
        if (isset($params['preset'])) {
            $dateRange = $this->calculateDateRange($params['preset']);
        } elseif (isset($params['start_date']) && isset($params['end_date'])) {
            $dateRange = [
                'start' => $params['start_date'],
                'end' => $params['end_date']
            ];
        } else {
            $dateRange = $this->calculateDateRange('last_30_days');
        }

        $branchId = $params['branch_id'] ?? null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 10;

        try {
            $topProducts = $this->analyticsRepository->getMostOrderedProducts(
                $dateRange['start'],
                $dateRange['end'],
                $branchId,
                $limit
            );

            $data = [
                'success' => true,
                'date_range' => [
                    'start' => $dateRange['start'],
                    'end' => $dateRange['end']
                ],
                'top_products' => $topProducts
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Failed to fetch top products',
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        
        $dateRange = null;
        if (isset($params['preset'])) {
            $dateRange = $this->calculateDateRange($params['preset']);
        } elseif (isset($params['start_date']) && isset($params['end_date'])) {
            $dateRange = [
                'start' => $params['start_date'],
                'end' => $params['end_date']
            ];
        } else {
            $dateRange = $this->calculateDateRange('last_30_days');
        }

        $branchId = $params['branch_id'] ?? null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 20;

        try {
            $recentOrders = $this->analyticsRepository->getRecentOrders(
                $dateRange['start'],
                $dateRange['end'],
                $branchId,
                $limit
            );

            $data = [
                'success' => true,
                'date_range' => [
                    'start' => $dateRange['start'],
                    'end' => $dateRange['end']
                ],
                'orders' => $recentOrders
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Failed to fetch recent orders',
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get sales by category
     */
    public function getSalesByCategory(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        
        $dateRange = null;
        if (isset($params['preset'])) {
            $dateRange = $this->calculateDateRange($params['preset']);
        } elseif (isset($params['start_date']) && isset($params['end_date'])) {
            $dateRange = [
                'start' => $params['start_date'],
                'end' => $params['end_date']
            ];
        } else {
            $dateRange = $this->calculateDateRange('last_30_days');
        }

        $branchId = $params['branch_id'] ?? null;

        try {
            $categoryData = $this->analyticsRepository->getSalesByCategory(
                $dateRange['start'],
                $dateRange['end'],
                $branchId
            );

            $data = [
                'success' => true,
                'date_range' => [
                    'start' => $dateRange['start'],
                    'end' => $dateRange['end']
                ],
                'categories' => $categoryData
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Failed to fetch category analytics',
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
