<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\EmailSubscriptionRepository;
use Exception;

/**
 * EmailSubscriptionController - Handles email subscription operations
 * 
 * Controller responsible for managing email subscription API endpoints
 */
class EmailSubscriptionController
{
    /**
     * @var EmailSubscriptionRepository $subscriptionRepository Repository for email subscription data access
     */
    private $subscriptionRepository;

    /**
     * Constructor - Dependency injection of repository
     *
     * @param EmailSubscriptionRepository $subscriptionRepository The email subscription repository instance
     */
    public function __construct(EmailSubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Subscribe an email address
     * 
     * POST /api/email-subscription/subscribe
     * 
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @return Response JSON response with subscription status
     * 
     * @OA\Post(
     *     path="/api/email-subscription/subscribe",
     *     summary="Subscribe to email list",
     *     tags={"Email Subscription"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Email subscription data",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Email address to subscribe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully subscribed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully subscribed to our mailing list"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Email already exists or invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Email already exists")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Failed to subscribe")
     *         )
     *     )
     * )
     */
    public function subscribe(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            // Validate required field
            if (!isset($data['email']) || empty(trim($data['email']))) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $email = trim($data['email']);

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid email format'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Check if email already exists
            if ($this->subscriptionRepository->emailExists($email)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email already exists'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Subscribe the email
            $subscriptionId = $this->subscriptionRepository->subscribe($email);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Successfully subscribed to our mailing list',
                'data' => [
                    'id' => $subscriptionId,
                    'email' => $email
                ]
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to subscribe: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get all email subscriptions (admin only)
     * 
     * GET /api/email-subscription/subscriptions
     * 
     * @param Request $request The PSR-7 request object
     * @param Response $response The PSR-7 response object
     * @return Response JSON response with list of subscriptions
     * 
     * @OA\Get(
     *     path="/api/email-subscription/subscriptions",
     *     summary="Get all email subscriptions (Admin Only)",
     *     tags={"Email Subscription"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of records to return",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of records to skip",
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="email", type="string", example="user@example.com")
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Failed to retrieve subscriptions")
     *         )
     *     )
     * )
     */
    public function getAllSubscriptions(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int) $queryParams['offset'] : null;

            $subscriptions = $this->subscriptionRepository->getAllSubscriptions(['id' => 'DESC'], $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $subscriptions,
                'count' => count($subscriptions)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve subscriptions: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
