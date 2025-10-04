<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\AddressRepository;
use App\Repository\SessionRepository;
use App\Services\EmailService;
use App\Services\OtpService;
use App\Services\Authentication\JWT;

class OrderController
{
    private $userRepository;
    private $wishlistRepository;
    private $addressRepository;
    private $sessionRepository;
    private $emailService;
    private $otpService;
    private $orderRepository;
    private $customerRepository;
    private $orderItemRepository;
    private $productRepository;

    public function __construct(
        UserRepository $userRepository,
        WishlistRepository $wishlistRepository,
        AddressRepository $addressRepository,
        SessionRepository $sessionRepository,
        EmailService $emailService,
        OtpService $otpService,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository
    ) {
        $this->userRepository = $userRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->addressRepository = $addressRepository;
        $this->sessionRepository = $sessionRepository;
        $this->emailService = $emailService;
        $this->otpService = $otpService;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
    }



    /**
     * Get user orders
     * 
     * @Route GET /api/users/{id}/orders
     */
    public function getAllOrders(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            // Extract pagination
            $page = max(1, (int) ($queryParams['page'] ?? 1));
            $perPage = min(100, max(1, (int) ($queryParams['per_page'] ?? 10)));
            $filters = [
                'search' => $queryParams['search'] ?? null
            ];

            $data = $this->orderRepository->getAllOrders($filters, $page, $perPage);
            $result = [];
            $orders = $data['orders'];

            foreach ($orders as $order) {
                $orderItems = $this->orderItemRepository->findByOrderId((int) $order['id']);
                $items = [];
                // decoding product and variant from json string
                foreach ($orderItems as $item) {
                    $item2 = [...$item];
                    $item2['product'] = json_decode($item['product'] ?? '{}', true);
                    $item2['variant'] = json_decode($item['variant'] ?? '{}', true);
                    $items[] = $item2;
                }
                $order['items'] = $items;
                // decode customer_address from json string
                $order['customer_address'] = json_decode($order['customer_address'] ?? '{}', true);
                var_dump($order['customer_address']);
                $result[] = $order;
            }
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'total' => $data['total'],
                    'per_page' => $data['per_page'],
                    'page' => $data['page'],
                    'total_pages' => $data['total_pages'],
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to get orders: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getOrderDetails(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $id = (int)($queryParams['id'] ?? 0);
            
            if (!$id) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Customer ID is required. Use ?customer_id=123'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            $order = $this->orderRepository->getOrderByOrderId($id);
            if (!$order) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Order not found.'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            $orderItems = $this->orderItemRepository->findByOrderId((int) $order['id']);
            $items = [];
            // decoding product and variant from json string
            foreach ($orderItems as $item) {
                $item2 = [...$item];
                $item2['product'] = json_decode($item['product'] ?? '{}', true);
                $item2['variant'] = json_decode($item['variant'] ?? '{}', true);
                $items[] = $item2;
            }
            $order['items'] = $items;
            // decode customer_address from json string
            $order['customer_address'] = json_decode($order['customer_address'] ?? '{}', true);
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $order
            ]));
            return $response->withHeader('Content-Type', 'application/json');



        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to get orders: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

    }

    public function updateOrder(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            if (empty($data)) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'No data provided for update.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            if (!isset($data['orderId'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'orderId is required.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $orderId = (int)$data['orderId'];
            $order = $this->orderRepository->getOrderByOrderId($orderId);
            if (!$order) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Order not found.']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            $allowedFields = ['status', 'rider_id'];
            $updateData = [];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }
            if (empty($updateData)) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'No valid fields to update.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $success = $this->orderRepository->updateOrder($orderId, $updateData);

            if ($success) {
                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Order updated successfully.']));
            } else {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to update order or no changes were made.']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Failed to update order: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}