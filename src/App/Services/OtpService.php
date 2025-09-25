<?php
namespace App\Services;



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// require_once constants.php
require_once __DIR__ . '/../Constants.php';


class OtpService
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
     * Generate OTP again given email address 
     */

    public function generateOtp()
    {
        // generate numeric OTP code of 6 number length
        $otp = random_int(100000, 999999);
        return $otp;
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
            $this->mailer->setFrom('no-reply@example.com', HEADER_TITLE_TEXT);
            $this->mailer->addAddress($email);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = SINGLE_SIGN_ON_OTP_EMAIL_SUBJECT;
            $this->mailer->Body = "
                <h2>Login Verification</h2>
                <p>A login attempt was detected from a new device.</p>
                <p>Your OTP is: <strong>$otp</strong></p>
                <p>This OTP is valid for 10 minutes. If you did not initiate this login, please secure your account.</p>
            ";
            $this->mailer->AltBody = "Login Verification\nYour OTP is: $otp\nValid for 10 minutes.";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Failed to send OTP email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
