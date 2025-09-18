<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\BranchRepository;

class BranchController
{
    private $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    /**
     * Get branch homepage data
     * 
     * @Route GET /api/branches/{businessId}/{branchId}/homepage
     */
    public function getBranchHomePage(Request $request, Response $response, array $args): Response
    {
        try {
            $businessId = (int) $args['businessId'];
            $branchId = (int) $args['branchId'];

            if ($businessId <= 0 || $branchId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid businessId or branchId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $branchData = $this->branchRepository->getBranchHomePage($businessId, $branchId);

            if (empty($branchData)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Branch not found or inactive'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $branchData
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve branch homepage data: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get branch summary
     * 
     * @Route GET /api/branches/{branchId}/summary
     */
    public function getBranchSummary(Request $request, Response $response, array $args): Response
    {
        try {
            $branchId = (int) $args['branchId'];

            if ($branchId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid branchId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $branchSummary = $this->branchRepository->getBranchSummary($branchId);

            if (!$branchSummary) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Branch not found or inactive'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $branchSummary
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve branch summary: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get branch timings for a specific day
     * 
     * @Route GET /api/branches/{branchId}/timings/{dayOfWeek}
     */
    public function getBranchTimingByDay(Request $request, Response $response, array $args): Response
    {
        try {
            $branchId = (int) $args['branchId'];
            $dayOfWeek = (int) $args['dayOfWeek'];

            if ($branchId <= 0 || $dayOfWeek < 1 || $dayOfWeek > 7) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid branchId or dayOfWeek (1-7)'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $timing = $this->branchRepository->getBranchTimingByDay($branchId, $dayOfWeek);

            if (!$timing) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Timing not found for specified day'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $timing
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve branch timing: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get all active branches for a business
     * 
     * @Route GET /api/businesses/{businessId}/branches
     */
    public function getActiveBranchesByBusiness(Request $request, Response $response, array $args): Response
    {
        try {
            $businessId = (int) $args['businessId'];

            if ($businessId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid businessId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $branches = $this->branchRepository->getActiveBranchesByBusiness($businessId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $branches,
                'count' => count($branches)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve branches: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Check if branch is open
     * 
     * @Route GET /api/branches/{branchId}/is-open
     */
    public function isBranchOpen(Request $request, Response $response, array $args): Response
    {
        try {
            $branchId = (int) $args['branchId'];

            if ($branchId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid branchId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $isOpen = $this->branchRepository->isBranchOpen($branchId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => [
                    'branch_id' => $branchId,
                    'is_open' => $isOpen,
                    'current_time' => date('Y-m-d H:i:s'),
                    'current_day' => date('l') // Full day name
                ]
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to check branch status: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get all branch timings
     * 
     * @Route GET /api/branches/{branchId}/timings
     */
    public function getBranchTimings(Request $request, Response $response, array $args): Response
    {
        try {
            $branchId = (int) $args['branchId'];

            if ($branchId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid branchId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $timings = $this->branchRepository->getBranchTimings($branchId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $timings,
                'count' => count($timings)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve branch timings: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get active banner campaigns for a branch
     * 
     * @Route GET /api/branches/{branchId}/banners
     */
    public function getActiveBannerCampaignsForBranch(Request $request, Response $response, array $args): Response
    {
        try {
            $branchId = (int) $args['branchId'];

            if ($branchId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid branchId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $campaigns = $this->branchRepository->getActiveBannerCampaignsForBranch($branchId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $campaigns,
                'count' => count($campaigns)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve banner campaigns: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get available products for a branch
     * 
     * @Route GET /api/branches/{branchId}/products
     */
    public function getAvailableProductsForBranch(Request $request, Response $response, array $args): Response
    {
        try {
            $branchId = (int) $args['branchId'];

            if ($branchId <= 0) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid branchId'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $products = $this->branchRepository->getAvailableProductsForBranch($branchId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $products,
                'count' => count($products)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve products: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}