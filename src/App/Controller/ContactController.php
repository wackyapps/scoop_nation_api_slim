<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repository\ContactSubmissionRepository;
use App\Repository\UserRepository;
use App\Services\EmailService;

class ContactController
{
    private $contactRepository;
    private $emailService;
    // UserController
    private $userRepository;

    public function __construct(ContactSubmissionRepository $contactRepository, EmailService $emailService)
    {
        $this->contactRepository = $contactRepository;
        $this->emailService = $emailService;
        $this->userRepository = new UserRepository();
    }

    /**
     * Submit contact form
     * 
     * @Route POST /api/contact/submit
     */
    public function submitContactForm(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            // Validate required fields
            $required = ['full_name', 'email_address', 'message'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty(trim($data[$field]))) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => "Field '{$field}' is required"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            // Validate email format
            if (!filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid email address format'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Prepare submission data
            $submissionData = [
                'business_id' => $data['business_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'full_name' => trim($data['full_name']),
                'email_address' => trim($data['email_address']),
                'phone_number' => $data['phone_number'] ?? null,
                'message' => trim($data['message']),
                'submission_type' => $data['submission_type'] ?? 'general',
                'status' => 'new',
                'ip_address' => $request->getServerParams()['REMOTE_ADDR'] ?? null,
                'user_agent' => $request->getHeaderLine('User-Agent'),
                'referrer' => $request->getHeaderLine('Referer')
            ];

            // Create submission
            $submissionId = $this->contactRepository->createSubmission($submissionData);

            // findByRole from UserRepository
            $users = $this->userRepository->findByRole('admin');

            if ($users) {
                // loop through users and make an array of emails that will be used to send email later to all admins
                $adminEmails = [];
                foreach ($users as $user) {
                    $adminEmails[] = $user['email'];
                }
                $submissionData['admin_emails'] = $adminEmails;
            }
            // TODO: send email to admin
            // $this->emailService->sendEmail(
            //     $adminEmails, 
            //     'New contact submission', 
            //     'contact_notification', 
            //     $submissionData);




            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon.',
                'submission_id' => $submissionId
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to submit contact form: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get contact submissions (admin only)
     * 
     * @Route GET /api/contact/submissions
     */
    public function getSubmissions(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $businessId = isset($queryParams['business_id']) ? (int) $queryParams['business_id'] : null;
            $branchId = isset($queryParams['branch_id']) ? (int) $queryParams['branch_id'] : null;
            $status = $queryParams['status'] ?? null;

            $orderBy = isset($queryParams['sort']) ? [$queryParams['sort'] => $queryParams['order'] ?? 'DESC'] : ['created_at' => 'DESC'];
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : null;
            $offset = isset($queryParams['offset']) ? (int) $queryParams['offset'] : null;

            // Build criteria based on filters
            $criteria = [];
            if ($businessId)
                $criteria['business_id'] = $businessId;
            if ($branchId)
                $criteria['branch_id'] = $branchId;
            if ($status)
                $criteria['status'] = $status;

            $submissions = $this->contactRepository->findBy($criteria, $orderBy, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $submissions,
                'count' => count($submissions)
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to retrieve submissions: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Update submission status (admin only)
     * 
     * @Route PUT /api/contact/submissions/{id}/status
     */
    public function updateSubmissionStatus(Request $request, Response $response, array $args): Response
    {
        try {
            $submissionId = (int) $args['id'];
            $data = $request->getParsedBody();

            if (!isset($data['status'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Status field is required'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $allowedStatuses = ['new', 'in_progress', 'resolved', 'closed'];
            if (!in_array($data['status'], $allowedStatuses)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid status value'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $success = $this->contactRepository->updateStatus($submissionId, $data['status']);

            if ($success) {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Submission status updated successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Submission not found'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Failed to update submission status: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}