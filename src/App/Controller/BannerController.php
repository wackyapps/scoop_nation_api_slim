<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\BannerRepository;
use App\Repository\MediaRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

class BannerController
{
    private BannerRepository $bannerRepository;
    private MediaRepository $mediaRepository;

    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
        $this->mediaRepository = new MediaRepository();
    }

    /**
     * Get all active banner campaigns for today's date and time
     * Returns campaigns with associated banners and meta data, filtered by branch if provided
     */
    public function getActiveBannerCampaignsForDateAndTime(Request $request, Response $response): Response
    {
        try {
            // Extract branch_id from request (e.g., header, query param, or session)
            $branchId = $request->getHeaderLine('X-Branch-Id') ? (int) $request->getHeaderLine('X-Branch-Id') : null;

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
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);
            $search = ($request->getQueryParams()['search'] ?? null);

            $campaigns = $this->bannerRepository->getAllBanners(1, $search, $limit, $page);



            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $campaigns['data'],
                'pagination' => [
                    'limit' => $limit,
                    'page' => $page,
                    'total_pages' => ceil((int) $campaigns['total'] / $limit),
                    'total' => $campaigns['total'],
                ],
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
            $campaignId = (int) $request->getQueryParams()['campaignId'] ?? null;
            if (!$campaignId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Missing campaignId in query parameters'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }


            // Extract branch_id from request (e.g., header, query param, or session)

            $campaign = $this->bannerRepository->findOneBy(['id' => $campaignId]);

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



    public function createBannerCampaign(Request $request, Response $response): Response
    {
        try {

            $data = json_decode($request->getBody()->getContents(), true);
            $user = $request->getAttribute('user');
            $files = $request->getUploadedFiles();

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            $user_id = (int) $user['id'];

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


            if (!isset($files['file'])) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'File upload is required']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Validate file upload
            $mime = $files['file']->getClientMediaType();
            if (!str_starts_with($mime, 'image/') && !str_starts_with($mime, 'video/')) {
                $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid file type. Must be image or video']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }


            // Create the campaign via repository
            $campaign = $this->bannerRepository->save([
                'name' => $data['name'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => (bool) $data['is_active'],
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'branch_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);


            if (!$campaign) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to create banner campaign'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $mediaFile = $files['file'];
            $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('%s.%s', uniqid(), $extension);
            $directory = __DIR__ . '/../../../public/media/banners/';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $mediaFile->moveTo($directory . $filename);
            $path = 'media/products/' . $filename;
            $this->mediaRepository->save([
                'image' => $path,
                'productID' => $campaign['id'],
                'type' => 'product',
                'mime_type' => $mime,
            ]);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $campaign,
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


    public function updateBannerCampaign(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $user = $request->getAttribute('user');
            $files = $request->getUploadedFiles();
            $media = !empty($data['media']) ? json_decode($data['media'], true) : [];
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            $user_id = (int) $user['id'];

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
            // check if campaign is exist 
            $campaign = $this->bannerRepository->findOneBy(['id' => $data['id']]);
            if (!$campaign) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Banner campaign not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            // check if data is provided then add it to updateData
            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['start_date'])) {
                $updateData['start_date'] = $data['start_date'];
            }
            if (isset($data['end_date'])) {
                $updateData['end_date'] = $data['end_date'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = (bool) $data['is_active'];
            }
            // add updated_by and updated_at
            $updateData['updated_by'] = $user_id;
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            $updateCampaign = $this->bannerRepository->update((int) $data['id'], $updateData);
            if (!$updateCampaign) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to update banner campaign'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
            $campaignId = (int) $campaign['id'];
            // 1. Get current media records
            $currentMedias = $this->mediaRepository->findMediaByCampaignId($campaignId); // array of db rows
            $mediaToKeep = [];
            if (!empty($data['media']) && is_array($media)) {
                $mediaToKeep = $media;// array of image paths or IDs to keep
            }
            // 2. Delete media not in data['media']
            foreach ($currentMedias as $media) {
                // Use image path for comparison (adjust if you use IDs)
                $imageIDsToKeep = array_column($mediaToKeep, 'imageID');
                if (!in_array($media['imageID'], $imageIDsToKeep)) {
                    $filePath = __DIR__ . '/../../../public/' . $media['image'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // Delete from DB
                    $this->mediaRepository->delete($media['imageID']);
                }
            }
            // 3. Add new uploaded files (support multiple)
            $mediaFiles = [];
            if (!empty($files['file'])) {
                if (is_array($files['file'])) {
                    foreach ($files['file'] as $file) {
                        if ($file && $file->getError() === UPLOAD_ERR_OK) {
                            $mediaFiles[] = $file;
                        }
                    }
                } else {
                    if ($files['file']->getError() === UPLOAD_ERR_OK) {
                        $mediaFiles[] = $files['file'];
                    }
                }
            }
            $directory = __DIR__ . '/../../../public/media/banners/';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            foreach ($mediaFiles as $mediaFile) {
                $mime = $mediaFile->getClientMediaType();
                if (!str_starts_with($mime, 'image/') && !str_starts_with($mime, 'video/')) {
                    $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid media type. Must be image or video']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
                $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
                $filename = sprintf('%s.%s', uniqid(), $extension);
                $mediaFile->moveTo($directory . $filename);
                $path = 'media/products/' . $filename;
                $this->mediaRepository->save([
                    'image' => $path,
                    'campaign_id' => $campaignId,
                    'type' => 'banner',
                    'mime_type' => $mime,
                ]);
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

    public function deleteBannerCampaign(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
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
            // check if campaign is exist 
            $campaign = $this->bannerRepository->findOneBy(['id' => $data['id']]);
            if (!$campaign) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Banner campaign not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }


            $deletedCompaign = $this->bannerRepository->delete((int) $data['id']);
            if (!$deletedCompaign) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to delete banner campaign'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
            // delete media
            // $deletedCompaignMedia = $this->bannerRepository->deleteCompaignMedia((int) $data['id']);
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Banner campaign deleted successfully',
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