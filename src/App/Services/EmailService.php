<?php
namespace App\Services;



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// require_once constants.php
require_once __DIR__ . '/../Constants.php';


class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // SMTP Configuration
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = SMTP_AUTH;
            $this->mailer->Username = SMTP_AUTH_USER;
            $this->mailer->Password = SMTP_AUTH_SECRET;
            $this->mailer->Port = SMTP_PORT;
            $this->mailer->SMTPSecure = SMTP_SECURED;
            // Email Defaults
            $this->mailer->setFrom(FROM_EMAIL, FROM_NAME);
            $this->mailer->isHTML(true); // Enable HTML emails
        } catch (Exception $e) {
            // error_log("Mailer Error: " . $e->getMessage());
            echo "Mailer Constructor Error: " . $e->getMessage();
        }
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $template Email template filename (inside templates folder)
     * @param array $data Associative array of template variables
     * @param array $attachments Array of file paths to attach
     * @return bool True if email sent, false otherwise
     */
    public function sendEmail($to, $subject, $template, $data = [], $attachments = [])
    {
        $body = '';
        try {
            // Load email template
            $body = $this->loadTemplate($template, $data);
        } catch (Exception $e) {
            // error_log("Email Error: " . $e->getMessage());
            echo "Template Error: " . $e->getMessage();
            return false;
        }

        try {

            // Set email parameters
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            // if $to is not array
            if (!is_array($to)) {
                $this->mailer->addAddress($to);
            } else {
                foreach ($to as $email) {
                    $this->mailer->addAddress($email);
                }
            }
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            // Attach files
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    if (file_exists($file)) {
                        $this->mailer->addAttachment($file);
                    }
                }
            }

            // Send email
            return $this->mailer->send();
        } catch (Exception $e) {
            // error_log("Email Error: " . $e->getMessage());
            echo "Email Sending Error: " . $e->getMessage();
            return false;
        }

    }

    /**
     * Load email template and replace placeholders with data
     * 
     * @param string $template Email template filename (without .html)
     * @param array $data Associative array of variables
     * @return string Processed HTML content
     */
    private function loadTemplate($template, $data)
    {
        // $templatePath = "/templates/{$template}.html";
        $templatePath = __DIR__ . "/templates/{$template}.html";

        if (!file_exists($templatePath)) {
            return "Template not found!";
        }

        $content = file_get_contents($templatePath);

        // Replace placeholders with actual values
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        return $content;
    }
    /**
     * Send OTP email to the user.
     *
     * @param string $email Recipient email
     * @param string $otp OTP code
     * @return bool True on success, false on failure
     */
    public function sendOtp($email, $otp)
    {
        try {
            $this->mailer->setFrom('no-reply@' . DOMAIN, DOMAIN);
            $this->mailer->addAddress($email);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = SINGLE_SIGN_ON_OTP_EMAIL_SUBJECT;
            $this->mailer->Body = "
                <h2>Hi,</h2>
                <p>Your verification PIN for " . DOMAIN . " is: <strong>$otp</strong></p>
            ";
            // $this->mailer->AltBody = "Login Verification\nYour OTP is: $otp\nValid for 10 minutes.";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Failed to send OTP email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Send email verification email
     * 
     * @param string $email Recipient email
     * @param string $token Verification token
     * @param string $userName User's name
     * @return bool Success status
     */
    public function sendEmailVerification(string $email, string $token, string $userName): bool
    {
        $verificationUrl = FRONTEND_URL . '?verify-email-token=' . $token;
        
        $data = [
            'name' => $userName,
            'verification_url' => $verificationUrl,
            'expiry_hours' => '24'
        ];

        return $this->sendEmail(
            $email,
            'Verify Your Email Address',
            'email_verification',
            $data
        );
    }

    /**
     * Send password reset request email
     * 
     * @param string $email Recipient email
     * @param string $token Reset token
     * @param string $userName User's name
     * @return bool Success status
     */
    public function sendPasswordResetRequest(string $email, string $token, string $userName): bool
    {
        $resetUrl = FRONTEND_URL . '?reset-password-token=' . $token;
        
        $data = [
            'name' => $userName,
            'reset_url' => $resetUrl,
            'expiry_minutes' => '60'
        ];

        return $this->sendEmail(
            $email,
            'Password Reset Request',
            'password_reset_request',
            $data
        );
    }

    /**
     * Send password reset confirmation email
     * 
     * @param string $email Recipient email
     * @param string $userName User's name
     * @return bool Success status
     */
    public function sendPasswordResetConfirmation(string $email, string $userName): bool
    {
        $data = [
            'name' => $userName,
            'reset_date' => date('F j, Y, g:i a')
        ];

        return $this->sendEmail(
            $email,
            'Password Reset Confirmation',
            'password_reset_confirmation',
            $data
        );
    }
}
