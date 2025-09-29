<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\BannerRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

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



    public function getAllBanners(Request $request, Response $response): Response
    {
        try {
            // Extract branch_id from request (e.g., header, query param, or session)

            $campaigns = $this->bannerRepository->getActiveCampaignsWithBannersAndMeta(1);

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


    public function getBannerCampaignById(Request $request, Response $response): Response
    {
        try {
            // Extract campaign ID from query parameters
            $campaignId =(int) $request->getQueryParams()['campaignId'] ?? null;
            if (!$campaignId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Missing campaignId in query parameters'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }


            // Extract branch_id from request (e.g., header, query param, or session)

            $campaign = $this->bannerRepository->findOneBy(['id'=> $campaignId]);

            if (empty($campaign)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No Banner Found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            // Add banners with meta data
            $campaign['media'] = $this->bannerRepository->getBannersWithMetaForCampaign($campaignId);
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $campaign,
                'message' => 'Banner campaign retrieved successfully'
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


    public function updateBannerCampaign(Request $request, Response $response): Response {
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
            $requiredFields = ['id'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }
            // check if compaign is exist 
            $compaign = $this->bannerRepository->findOneBy(['id'=>$data['id']]);
            if(!$compaign){
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Banner campaign not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            // check if data is provided then add it to updateData
            $updateData = [];
            if(isset($data['name'])){
                $updateData['name'] = $data['name'];
            }
            if(isset($data['description'])){
                $updateData['description'] = $data['description'];
            }
            if(isset($data['start_date'])){
                $updateData['start_date'] = $data['start_date'];
            }
            if(isset($data['end_date'])){
                $updateData['end_date'] = $data['end_date'];
            }
            if(isset($data['is_active'])){
                $updateData['is_active'] = (bool)$data['is_active'];
            }
            // add updated_by and updated_at
            $updateData['updated_by'] = $user_id;
            $updateData['updated_at'] = date('Y-m-d H:i:s');

            $updateCampaign = $this->bannerRepository->update( (int) $data['id'], $updateData);
            if(!$updateCampaign){
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to update banner campaign'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }


            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Banner campaign updated successfully',
                // 'id' => $id
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to update banner campaign: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}