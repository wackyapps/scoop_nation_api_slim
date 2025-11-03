<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\AdminProfileRepository;
use App\Repository\UserRepository;
use DB;

class AdminProfileController
{
    private $adminProfileRepository;
    private $userRepository;

    public function __construct(
        AdminProfileRepository $adminProfileRepository,
        UserRepository $userRepository
    ) {
        $this->adminProfileRepository = $adminProfileRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get admin profile by user ID
     * 
     * @Route GET /api/admin/profile?user_id={user_id}
     */
    public function getAdminProfile(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (!isset($queryParams['user_id']) || empty($queryParams['user_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $userId = (int) $queryParams['user_id'];

            // Verify user exists and is an admin
            $user = $this->userRepository->find($userId);
            if (!$user || !in_array($user['role'], ['administrator', 'admin'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Admin user not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Get profile with user information
            $profile = $this->adminProfileRepository->findProfileWithUser($userId);

            if (!$profile) {
                // Return default structure if profile doesn't exist yet
                $profile = [
                    'user_id' => $userId,
                    'fullname' => null,
                    'gender' => null,
                    'date_of_birth' => null,
                    'avatar' => null,
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'role' => $user['role']
                ];
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $profile
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve admin profile: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Update admin profile
     * 
     * @Route POST /api/admin/profile/update
     */
    public function updateAdminProfile(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            if (!isset($data['user_id']) || empty($data['user_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $userId = (int) $data['user_id'];

            // Verify user exists and is an admin
            $user = $this->userRepository->find($userId);
            if (!$user || !in_array($user['role'], ['administrator', 'admin'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Admin user not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Prepare profile data
            $profileData = [];
            
            if (isset($data['fullname'])) {
                $profileData['fullname'] = trim($data['fullname']);
            }
            
            if (isset($data['gender']) && in_array($data['gender'], ['male', 'female', 'other'])) {
                $profileData['gender'] = $data['gender'];
            }
            
            if (isset($data['date_of_birth'])) {
                $profileData['date_of_birth'] = $data['date_of_birth'];
            }
            
            if (isset($data['avatar'])) {
                $profileData['avatar'] = $data['avatar'];
            }

            // Create or update profile
            $profileId = $this->adminProfileRepository->createOrUpdate($userId, $profileData);

            // Get updated profile
            $updatedProfile = $this->adminProfileRepository->findProfileWithUser($userId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Admin profile updated successfully',
                'data' => $updatedProfile
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to update admin profile: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Upload admin avatar
     * 
     * @Route POST /api/admin/profile/upload-avatar
     */
    public function uploadAvatar(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            if (!isset($data['user_id']) || empty($data['user_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User ID is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            if (!isset($uploadedFiles['avatar'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Avatar file is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $userId = (int) $data['user_id'];
            $uploadedFile = $uploadedFiles['avatar'];

            // Verify upload
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'File upload failed'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            $fileType = $uploadedFile->getClientMediaType();
            
            if (!in_array($fileType, $allowedTypes)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid file type. Only JPEG, PNG, and WebP images are allowed'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Generate unique filename
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            
            // Upload directory
            $uploadDir = __DIR__ . '/../../../public/media/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Move uploaded file
            $uploadedFile->moveTo($uploadDir . $filename);

            // Update avatar path in database
            $avatarPath = 'media/avatars/' . $filename;
            $this->adminProfileRepository->createOrUpdate($userId, ['avatar' => $avatarPath]);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'avatar_url' => $avatarPath
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to upload avatar: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get all admin profiles
     * 
     * @Route GET /api/admin/profiles
     */
    public function getAllAdminProfiles(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int) $queryParams['offset'] : null;

            $profiles = $this->adminProfileRepository->findAllAdminProfiles(null, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $profiles,
                'count' => count($profiles)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve admin profiles: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
