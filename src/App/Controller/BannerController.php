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



    public function createBannerCampaign(Request $request, Response $response): Response{
         try {
             
            $data = json_decode($request->getBody()->getContents(), true);
            $user = $request->getAttribute('user');

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            $user_id =(int) $user['id'];

            // Validate required fields
            $requiredFields = ['name', 'description', 'start_date', 'end_date', 'is_active'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            // Create the campaign via repository
            $campaign = $this->bannerRepository->save([
                'name'        => $data['name'],
                'description' => $data['description'],
                'start_date'   => $data['start_date'],
                'end_date'     => $data['end_date'],
                'is_active'    => (bool)$data['is_active'],
                'created_by'=> $user_id,
                'updated_by'=> $user_id,
                'branch_id'=>1,
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s')
            ]);

            if(!$campaign){
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to create banner campaign'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data'    => $campaign,
                'message' => 'Banner campaign created successfully'
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