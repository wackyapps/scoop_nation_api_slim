<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\EmailTemplateRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmailTemplateController
{
    private EmailTemplateRepository $emailTemplateRepository;

    public function __construct(EmailTemplateRepository $emailTemplateRepository)
    {
        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    /**
     * Get all active email templates
     */
    public function getActiveEmailTemplates(Request $request, Response $response): Response
    {
        try {
            $templates = $this->emailTemplateRepository->getActiveEmailTemplates();

            if (empty($templates)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No active email templates found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $templates,
                'message' => 'Active email templates retrieved successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve email templates: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get all email templates with pagination and optional search
     */
    public function getAllEmailTemplates(Request $request, Response $response): Response
    {
        try {
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);
            $search = $request->getQueryParams()['search'] ?? null;

            $templates = $this->emailTemplateRepository->getAllEmailTemplates($search, $limit, $page);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $templates['data'],
                'pagination' => [
                    'limit' => $limit,
                    'page' => $page,
                    'total_pages' => ceil((int) $templates['total'] / $limit),
                    'total' => $templates['total'],
                ],
                'message' => 'Email templates retrieved successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve email templates: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Get a single email template by ID
     */
    public function getEmailTemplateById(Request $request, Response $response): Response
    {
        try {
            $templateId = (int) ($request->getQueryParams()['id'] ?? 0);
            if (!$templateId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Missing id in query parameters'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $template = $this->emailTemplateRepository->findOneBy(['id' => $templateId]);

            if (empty($template)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Email template not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $template,
                'message' => 'Email template retrieved successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to retrieve email template: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Create a new email template
     */
    public function createEmailTemplate(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $user = $request->getAttribute('user');

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            $requiredFields = ['name', 'slug', 'subject', 'body_html', 'is_active'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Missing required field: {$field}"
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            // Validate slug uniqueness
            $existingTemplate = $this->emailTemplateRepository->findOneBy(['slug' => $data['slug']]);
            if ($existingTemplate) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Slug already exists'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $templateData = [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'subject' => $data['subject'],
                'body_html' => $data['body_html'],
                'variables' => $data['variables'] ?? null,
                'is_active' => (bool) $data['is_active'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $templateId = $this->emailTemplateRepository->save($templateData);

            if (!$templateId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to create email template'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => ['id' => $templateId],
                'message' => 'Email template created successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to create email template: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Update an existing email template
     */
    public function updateEmailTemplate(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $user = $request->getAttribute('user');

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

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

            $template = $this->emailTemplateRepository->findOneBy(['id' => $data['id']]);
            if (!$template) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Email template not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Validate slug uniqueness if provided
            if (isset($data['slug']) && $data['slug'] !== $template['slug']) {
                $existingTemplate = $this->emailTemplateRepository->findOneBy(['slug' => $data['slug']]);
                if ($existingTemplate) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Slug already exists'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            $updateData = [];
            foreach (['name', 'slug', 'subject', 'body_html', 'variables', 'is_active'] as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $field === 'is_active' ? (bool) $data[$field] : $data[$field];
                }
            }
            $updateData['updated_at'] = date('Y-m-d H:i:s');

            $affectedRows = $this->emailTemplateRepository->update((int) $data['id'], $updateData);

            if (!$affectedRows) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to update email template'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Email template updated successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to update email template: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Delete an email template
     */
    public function deleteEmailTemplate(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);

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

            $template = $this->emailTemplateRepository->findOneBy(['id' => $data['id']]);
            if (!$template) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Email template not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $affectedRows = $this->emailTemplateRepository->delete((int) $data['id']);

            if (!$affectedRows) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Failed to delete email template'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Email template deleted successfully'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to delete email template: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}