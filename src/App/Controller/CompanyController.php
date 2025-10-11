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
                    'error' => 'Invalid media type. Must be image'
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

            // Generate unique filename and move file
            $extension = pathinfo($mediaFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('%s.%s', uniqid(), $extension);
            $mediaFile->moveTo($directory . $filename);
            $updateData['logo'] = 'media/company/' . $filename;
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