<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\CompanyRepository;

class CompanyController
{
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Get all customers with search, pagination, and comprehensive data
     * For admin dashboard customer data table
     * 
     * @Route GET /api/admin/customers
     */
public function updateCompany(Request $request, Response $response): Response
{
    try {
        $data = $request->getParsedBody() ?? [];
        $files = $request->getUploadedFiles() ?? [];
        $businessId = 1;

        // Initialize update data array
        $updateData = [];
        if (!empty($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        if (!empty($data['description'])) {
            $updateData['description'] = $data['description'];
        }

        // Fetch existing company data
        $company = $this->companyRepository->findCompanyById($businessId);
        if (!$company) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Company not found'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Handle file uploads
        $mediaFiles = [];
        if (!empty($files['logo'])) {
            if (is_array($files['logo'])) {
                foreach ($files['logo'] as $file) {
                    if ($file && $file->getError() === UPLOAD_ERR_OK) {
                        $mediaFiles[] = $file;
                    }
                }
            } else {
                if ($files['logo']->getError() === UPLOAD_ERR_OK) {
                    $mediaFiles[] = $files['logo'];
                }
            }
        }

        // Check if at least one field is provided
        if (empty($updateData) && empty($mediaFiles)) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'At least one field (name, description, or logo) must be provided'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Handle logo upload if present
        if (!empty($mediaFiles)) {
            try {
                // Check if GD library is available
                if (!extension_loaded('gd')) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'GD library is not installed. Please enable GD extension in php.ini'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }

                $directory = __DIR__ . '/../../../public/media/company/';
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }

                $mediaFile = $mediaFiles[0];
                $mime = $mediaFile->getClientMediaType();

                // Validate media type
                if (!str_starts_with($mime, 'image/')) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Invalid media type. Must be image. Received: ' . $mime
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }

                // Delete existing logo if it exists
                if (!empty($company['logo'])) {
                    $existingLogoPath = __DIR__ . '/../../../public/' . $company['logo'];
                    if (file_exists($existingLogoPath)) {
                        unlink($existingLogoPath);
                    }
                }

                // Move uploaded file to temporary location
                $tempFilename = uniqid() . '_temp_' . $mediaFile->getClientFilename();
                $tempPath = $directory . $tempFilename;
                $mediaFile->moveTo($tempPath);

                if (!file_exists($tempPath)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Failed to upload file to temporary location'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }

                // Convert image to PNG
                $image = null;
                $extension = strtolower(pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION));
                
                // Suppress warnings and capture errors
                error_reporting(E_ALL);
                
                // Create image resource based on file type
                try {
                    switch ($extension) {
                        case 'jpg':
                        case 'jpeg':
                            $image = @imagecreatefromjpeg($tempPath);
                            break;
                        case 'png':
                            $image = @imagecreatefrompng($tempPath);
                            break;
                        case 'gif':
                            $image = @imagecreatefromgif($tempPath);
                            break;
                        case 'webp':
                            if (function_exists('imagecreatefromwebp')) {
                                $image = @imagecreatefromwebp($tempPath);
                            }
                            break;
                        case 'bmp':
                            if (function_exists('imagecreatefrombmp')) {
                                $image = @imagecreatefrombmp($tempPath);
                            }
                            break;
                        default:
                            // Try to detect from MIME type if extension fails
                            if (strpos($mime, 'jpeg') !== false) {
                                $image = @imagecreatefromjpeg($tempPath);
                            } elseif (strpos($mime, 'png') !== false) {
                                $image = @imagecreatefrompng($tempPath);
                            } elseif (strpos($mime, 'gif') !== false) {
                                $image = @imagecreatefromgif($tempPath);
                            } elseif (strpos($mime, 'webp') !== false && function_exists('imagecreatefromwebp')) {
                                $image = @imagecreatefromwebp($tempPath);
                            } elseif (strpos($mime, 'bmp') !== false && function_exists('imagecreatefrombmp')) {
                                $image = @imagecreatefrombmp($tempPath);
                            }
                    }
                } catch (\Exception $imgEx) {
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Failed to read image: ' . $imgEx->getMessage()
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }

                if (!$image) {
                    // Clean up temp file
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Failed to process image. Format: ' . $extension . ', MIME: ' . $mime . '. Make sure the file is a valid image.'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }

                // Save as PNG with filename logo.png
                $filename = 'logo.png';
                $finalPath = $directory . $filename;
                
                // Enable alpha blending and save alpha channel
                imagealphablending($image, false);
                imagesavealpha($image, true);
                
                // Save as PNG
                $saveResult = @imagepng($image, $finalPath, 9);
                
                if (!$saveResult) {
                    imagedestroy($image);
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'Failed to save PNG image. Check directory permissions.'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }

                // Clean up
                imagedestroy($image);
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                $updateData['logo'] = 'media/company/' . $filename;
                
            } catch (\Exception $fileEx) {
                // Clean up temp file if it exists
                if (isset($tempPath) && file_exists($tempPath)) {
                    unlink($tempPath);
                }
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Image processing error: ' . $fileEx->getMessage()
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        }

        // Update company in repository
        $company = $this->companyRepository->updateCompany($updateData, $businessId);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $company
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'error' => 'Failed to update company: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}
public function getCompany(Request $request, Response $response): Response
{
    try {
        
        $businessId = 1;

        $company = $this->companyRepository->findCompanyById($businessId);

        if (!$company) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Company not found'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }


        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $company
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'error' => 'Failed to get company: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}




}