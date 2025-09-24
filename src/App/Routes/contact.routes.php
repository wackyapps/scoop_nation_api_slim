<?php

/**
 * Contact Form Submission and Management Routes
 * 
 * These routes handle contact form submissions and administrative management
 * of contact inquiries within the e-commerce platform.
 */

/**
 * @OA\Post(
 *     path="/api/contact/submit",
 *     summary="Submit a contact form",
 *     description="Allows users to submit contact inquiries through the website contact form. This endpoint is publicly accessible and does not require authentication.",
 *     tags={"Contact"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Contact form submission data",
 *         @OA\JsonContent(
 *             required={"full_name", "email_address", "message"},
 *             @OA\Property(property="business_id", type="integer", example=1, description="Optional: ID of the business related to the inquiry"),
 *             @OA\Property(property="branch_id", type="integer", example=1, description="Optional: ID of the branch related to the inquiry"),
 *             @OA\Property(property="full_name", type="string", example="John Doe", description="User's full name"),
 *             @OA\Property(property="email_address", type="string", format="email", example="john@example.com", description="User's email address"),
 *             @OA\Property(property="phone_number", type="string", example="+1234567890", description="Optional: User's phone number"),
 *             @OA\Property(property="message", type="string", example="I have a question about your products...", description="Detailed message from the user"),
 *             @OA\Property(property="submission_type", type="string", enum={"general", "support", "feedback", "complaint", "partnership"}, example="general", description="Type of contact submission")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Contact form submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Thank you for your message. We will get back to you soon."),
 *             @OA\Property(property="submission_id", type="integer", example=123)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request - Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Field 'full_name' is required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Failed to submit contact form")
 *         )
 *     )
 * )
 */
$app->post('/api/contact/submit', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\ContactController::class);
    return $controller->submitContactForm($request, $response);
});

/**
 * @OA\Get(
 *     path="/api/contact/submissions",
 *     summary="Get contact submissions (Admin Only)",
 *     description="Retrieve contact form submissions with filtering and pagination. Requires administrator authentication.",
 *     tags={"Contact"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="business_id",
 *         in="query",
 *         description="Filter by business ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="branch_id",
 *         in="query",
 *         description="Filter by branch ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="Filter by submission status",
 *         @OA\Schema(type="string", enum={"new", "in_progress", "resolved", "closed"})
 *     ),
 *     @OA\Parameter(
 *         name="sort",
 *         in="query",
 *         description="Field to sort by (e.g., created_at, updated_at)",
 *         @OA\Schema(type="string", example="created_at")
 *     ),
 *     @OA\Parameter(
 *         name="order",
 *         in="query",
 *         description="Sort order (ASC or DESC)",
 *         @OA\Schema(type="string", enum={"ASC", "DESC"}, example="DESC")
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of records to return",
 *         @OA\Schema(type="integer", example=20)
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         description="Number of records to skip",
 *         @OA\Schema(type="integer", example=0)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", 
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="business_id", type="integer", example=1),
 *                     @OA\Property(property="branch_id", type="integer", example=1),
 *                     @OA\Property(property="full_name", type="string", example="John Doe"),
 *                     @OA\Property(property="email_address", type="string", example="john@example.com"),
 *                     @OA\Property(property="phone_number", type="string", example="+1234567890"),
 *                     @OA\Property(property="message", type="string", example="I have a question..."),
 *                     @OA\Property(property="submission_type", type="string", example="general"),
 *                     @OA\Property(property="status", type="string", example="new"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-24 22:02:38")
 *                 )
 *             ),
 *             @OA\Property(property="count", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Admin authentication required",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Token not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Failed to retrieve submissions")
 *         )
 *     )
 * )
 */
$app->get('/api/contact/submissions', function ($request, $response) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\ContactController::class);
    return $controller->getSubmissions($request, $response);
});

/**
 * @OA\Put(
 *     path="/api/contact/submissions/{id}/status",
 *     summary="Update submission status (Admin Only)",
 *     description="Update the status of a contact form submission. Requires administrator authentication.",
 *     tags={"Contact"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Contact submission ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="New status for the submission",
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"new", "in_progress", "resolved", "closed"}, example="in_progress", description="New status for the submission")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Submission status updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request - Invalid status value",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Invalid status value")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - Admin authentication required",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Token not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Submission not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Submission not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Failed to update submission status")
 *         )
 *     )
 * )
 */
$app->put('/api/contact/submissions/{id}/status', function ($request, $response, $args) use ($app) {
    $controller = $app->getContainer()->get(App\Controller\ContactController::class);
    return $controller->updateSubmissionStatus($request, $response, $args);
});
