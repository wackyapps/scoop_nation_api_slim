<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\BannerRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BannerController
{
    private BannerRepository $bannerRepository;

    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Get all active banner campaigns for today's date and time
     * Returns campaigns with associated banners and meta data, filtered by branch if provided
     */
    public function getActiveBannerCampaignsForDateAndTime(Request $request, Response $response): Response
    {
        try {
            // Extract branch_id from request (e.g., header, query param, or session)
            $branchId = $request->getHeaderLine('X-Branch-Id') ? (int)$request->getHeaderLine('X-Branch-Id') : null;

            $campaigns = $this->bannerRepository->getActiveCampaignsWithBannersAndMeta($branchId);

            if (empty($campaigns)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No active banner campaigns found for today'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $campaigns,
                'message' => 'Active banner campaigns retrieved successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve active banner campaigns: ' . $e->getMessage()
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}