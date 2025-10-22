<?php

namespace App\Services;

use DB;
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
            $body = $this->loadTemplateFromFile($template, $data);
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
     * Summary of sendEmailFromDB
     * @param mixed $to
     * @param mixed $subject
     * @param mixed $template
     * @param mixed $data
     * @param mixed $attachments
     * @return bool
     */
    private function sendEmailNotificationByTemplate($to, $subject, $template, $data = [], $attachments = []) 
    {
        $templateData = [
            'subject'=>'',
            'is_active'=> 0,
            'content'=>''
        ];
        try {
            // Load email template
            $templateData  = $this->loadTemplateFromDB($template, $data);
        } catch (Exception $e) {
            // error_log("Email Error: " . $e->getMessage());
            echo "Template Error: " . $e->getMessage();
            return false;
        }
        try {
           if ($templateData['is_active']) {
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
            $this->mailer->Subject = $templateData['subject'];
            $this->mailer->Body = $templateData['content'];

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
           }
           
           // Template is not active
           return false;
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
    private function loadTemplateFromFile($template, $data)
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
     * Summary of loadTemplateFromDB
     * @param mixed $templateName
     * @param mixed $data
     */
    private function loadTemplateFromDB($templateName, $data)
    {
        // Simulate fetching template from database
        // In real implementation, fetch from DB using your preferred method
        $template = $this->fetchTemplateFromDB($templateName);
        if (!$template) {
            $template  = "Template not found in database.";
        }

        $content = '';
        $is_active = 1;
        $subject = '';
        if (!$template) {
            $is_active = 0;
        }
        else{
            $content = $template['body_html'];
            $is_active = $template['is_active'] == 1?1:0;
            $subject = $template['subject'];
        }

        // Replace placeholders with actual values
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        $templateData = [
            'content' => $content,
            'is_active'=> $is_active,
            'subject'=> $subject
        ];
        return $templateData;
    }

    private function fetchTemplateFromDB($templateName)
    {
        $query = "select * from email_templates where name = '{$templateName}'";
        $template = DB::queryFirstRow($query);
        return $template;
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
    public function sendCustomerVerificationEmail(string $email, string $token, string $userName): bool
    {
        $verificationUrl = FRONTEND_URL . '?verify-email-token=' . $token;

        $data = [
            'name' => $userName,
            'verification_url' => $verificationUrl,
            'expiry_hours' => '24'
        ];

        return $this->sendEmailNotificationByTemplate(
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
    public function sendPasswordResetRequestEmail(string $email, string $token, string $userName): bool
    {
        $resetUrl = FRONTEND_URL . '?reset-password-token=' . $token;

        $data = [
            'name' => $userName,
            'reset_url' => $resetUrl,
            'expiry_minutes' => '60'
        ];

        return $this->sendEmailNotificationByTemplate(
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
    public function sendPasswordResetConfirmationEmail(string $email, string $userName): bool
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



    /**
     * Send customer welcome email
     * @param int $customerId Customer ID
     * @return bool Success status
     */
    public function sendCustomerWelcomeEmail(
        int $customerId
    ): bool {

        /**
         * Fetch customer information from  CustomerRepository by customerId
         */
        $customerRepository = new \App\Repository\CustomerRepository();
        $customer = $customerRepository->getCustomerDetails($customerId);

        if (!$customer) {
            return false;
        }

        /**
         * if customer email is empty, return false
         */
        if (empty($customer['email'])) {
            return false;
        }

        $data = array(
            'name' => $customer['fullname'],
            'signup_date' => date('F j, Y'),
            'website_url' => FRONTEND_URL,
            'support_email' => FROM_EMAIL
        );


        return $this->sendEmailNotificationByTemplate(
            $customer['email'],
            'Welcome to ' . DOMAIN,
            'customer_welcome',
            $data
        );
    }

    /**
     * Send admin notification for new customer registration
     * 
     * @param string $customerName New customer's name
     * @param string $customerEmail New customer's email
     * @param string $registrationDate Customer registration date
     * @param array $additionalData Additional template variables
     * @return bool Success status
     */
    public function sendAdminNewCustomerNotification(int $customerId, ): bool
    {
          /**
         * Fetch customer information from  CustomerRepository by customerId
         */
        $customerRepository = new \App\Repository\CustomerRepository();
        $customer = $customerRepository->getCustomerDetails($customerId);

        if (!$customer) {
            return false;
        }
        $adminEmail = ADMIN_EMAIL ?? FROM_EMAIL; // Fallback to FROM_EMAIL if ADMIN_EMAIL not defined

        $data = [
            'customer_name' => $customer['fullname'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'] ?? '',
            'registration_date' => $customer['createdAt'],
            // 'admin_url' => ADMIN_URL ?? FRONTEND_URL
        ];

        return $this->sendEmailNotificationByTemplate(
            ADMIN_EMAIL,
            'New Customer Registration - ' . DOMAIN,
            'admin_customer_joined',
            $data
        );
    }

    /**
     * Send customer new order confirmation email
     * 
     * @param string $email Customer email address
     * @param string $customerName Customer's name
     * @param string $orderId Order ID
     * @param array $orderDetails Order details
     * @param array $additionalData Additional template variables
     * @return bool Success status
     */
    public function sendCustomerNewOrderEmail(int $orderId): bool
    {
        $orderRepository = new \App\Repository\OrderRepository();
        $order = $orderRepository->getOrderByOrderId($orderId);
        if (!$order) {
            return false;
        }

        $data =[
            'customer_name' => $order['fullname'],
            'order_id' => $orderId,
            'order_date' => date('F j, Y'),
            'order_total' => $order['total'] ?? '',
            // 'order_items' => $order['items'] ?? [],
            // 'tracking_url' => $order['tracking_url'] ?? '',
            'customer_service_email' => FROM_EMAIL
        ];

        return $this->sendEmailNotificationByTemplate(
            ADMIN_EMAIL,
            'Order Confirmation - #' . $orderId,
            'customer_order_new_placed',
            $data
        );
    }

    /**
     * Send admin notification for new order placed
     * 
     * @param string $orderId Order ID
     * @param array $orderDetails Order details
     * @param array $customerDetails Customer details
     * @param array $additionalData Additional template variables
     * @return bool Success status
     */
    public function sendAdminNewOrderNotification(int $orderId): bool
    {
        $orderRepository = new \App\Repository\OrderRepository();
        $order = $orderRepository->getOrderByOrderId($orderId);
        if (!$order) {
            return false;
        }

        $data = [
            'order_id' => $orderId,
            'order_date' => date('F j, Y g:i A'),
            'order_total' => $order['total'] ?? '',
            'customer_name' => $order['fullname'] ?? '',
            'customer_email' => $order['email'] ?? '',
            'customer_phone' => $order['phone'] ?? '',
            // 'shipping_address' => $order['shipping_address'] ?? '',
            // 'billing_address' => $order['billing_address'] ?? '',
            // 'order_items' => $order['items'] ?? [],
            // 'admin_order_url' => $order['admin_order_url'] ?? ''
        ];

        return $this->sendEmailNotificationByTemplate(
            'ameerarif12348@gmail.com',
            'New Order Received - #' . $orderId,
            'admin_order_new_placed',
            $data
        );
    }

    /**
     * Send customer order shipped/delivered notification
     * @param string $orderId Order ID
     * @return bool Success status
     */
    public function sendCustomerOrderShippedEmail( int $orderId, ): bool
    {

        $orderRepository = new \App\Repository\OrderRepository();
        $order = $orderRepository->getOrderByOrderId($orderId);
        if (!$order) {
            return false;
        }

        $data = [
            'customer_name' => $order['fullname'],
            'customer_email'=> $order['email'],
            'customer_phone'=>$order['phone'],
            'order_id' => $orderId,
            // 'tracking_number' => $shippingDetails['tracking_number'] ?? '',
            // 'carrier' => $shippingDetails['carrier'] ?? '',
            // 'shipping_date' => $shippingDetails['shipping_date'] ?? date('F j, Y'),
            // 'estimated_delivery' => $shippingDetails['estimated_delivery'] ?? '',
            // 'tracking_url' => $shippingDetails['tracking_url'] ?? ''
        ];

        // $subject = isset($shippingDetails['is_delivered']) && $shippingDetails['is_delivered']
        //     ? 'Order Delivered - #' . $orderId
        //     : 'Order Shipped - #' . $orderId;

        // $template = isset($shippingDetails['is_delivered']) && $shippingDetails['is_delivered']
        //     ? 'customer_order_delivered'
        //     : 'customer_order_shipped';
        $subject ='customer_order_shipped';
        $template = 'customer_order_shipped';
        return $this->sendEmailNotificationByTemplate(
            $order['email'],
            $subject,
            $template,
            $data
        );
    }

    /**
     * Send admin notification for order shipped/delivered
     * 
     * @param string $orderId Order ID
     * @param array $shippingDetails Shipping details
     * @param array $customerDetails Customer details
     * @param array $additionalData Additional template variables
     * @return bool Success status
     */
    public function sendAdminOrderShippedNotification(int $orderId): bool
    {
        $adminEmail = ADMIN_EMAIL ?? FROM_EMAIL;
        $orderRepository = new \App\Repository\OrderRepository();
        $order = $orderRepository->getOrderByOrderId($orderId);
        if (!$order) {
            return false;
        }

        $data = [
            'order_id' => $orderId,
            'customer_name' => $order['fullname'] ?? '',
            'customer_email' => $order['email'] ?? '',
            'customer_phone'=>$order['phone'],
            // 'tracking_number' => $shippingDetails['tracking_number'] ?? '',
            // 'carrier' => $shippingDetails['carrier'] ?? '',
            // 'shipping_date' => $shippingDetails['shipping_date'] ?? date('F j, Y'),
            // 'shipping_address' => $shippingDetails['shipping_address'] ?? ''
        ];

        // $subject = isset($shippingDetails['is_delivered']) && $shippingDetails['is_delivered']
        //     ? 'Order Delivered - #' . $orderId
        //     : 'Order Shipped - #' . $orderId;

        // $template = isset($shippingDetails['is_delivered']) && $shippingDetails['is_delivered']
        //     ? 'admin_order_delivered'
        //     : 'admin_order_shipped';
        $subject ='';
        $template = 'admin_order_shipped';

        return $this->sendEmailNotificationByTemplate(
            'ameerarif12348@gmail.com',
            $subject,
            $template,
            $data
        );
    }

    /**
     * Send admin notification for cash on delivery received
     * 
     * @param string $orderId Order ID
     * @param string $amountReceived Amount received
     * @param array $orderDetails Order details
     * @param array $customerDetails Customer details
     * @param array $additionalData Additional template variables
     * @return bool Success status
     */
    public function sendAdminCashOnDeliveryReceived(string $orderId, string $amountReceived, array $orderDetails = [], array $customerDetails = [], array $additionalData = []): bool
    {
        $adminEmail = ADMIN_EMAIL ?? FROM_EMAIL;
        $orderRepository = new \App\Repository\OrderRepository();
        $order = $orderRepository->getOrderByOrderId($orderId);
        if (!$order) {
            return false;
        }
        
        $data = array_merge([
            'order_id' => $orderId,
            // 'amount_received' => $amountReceived,
            // 'received_date' => date('F j, Y g:i A'),
            'customer_name' => $customerDetails['name'] ?? '',
            'customer_email' => $customerDetails['email'] ?? '',
            // 'order_total' => $orderDetails['total'] ?? '',
            // 'payment_method' => $orderDetails['payment_method'] ?? 'Cash on Delivery',
            // 'collected_by' => $orderDetails['collected_by'] ?? ''
        ], $additionalData);

        return $this->sendEmailNotificationByTemplate(
            $adminEmail,
            'Cash on Delivery Received - #' . $orderId,
            'admin_cod_received',
            $data
        );
    }

    /**
     * Send admin notification for new address added
     * 
     * @param array $addressDetails Address details
     * @param array $customerDetails Customer details
     * @param array $additionalData Additional template variables
     * @return bool Success status
     */
    public function sendAdminNewAddressAdded(array $addressDetails = [], array $customerDetails = [], array $additionalData = []): bool
    {
        $adminEmail = ADMIN_EMAIL ?? FROM_EMAIL;

        $data = array_merge([
            'customer_name' => $customerDetails['name'] ?? '',
            'customer_email' => $customerDetails['email'] ?? '',
            'customer_id' => $customerDetails['id'] ?? '',
            'address_type' => $addressDetails['type'] ?? 'Shipping',
            'address_line1' => $addressDetails['address_line1'] ?? '',
            'address_line2' => $addressDetails['address_line2'] ?? '',
            'city' => $addressDetails['city'] ?? '',
            'state' => $addressDetails['state'] ?? '',
            'zip_code' => $addressDetails['zip_code'] ?? '',
            'country' => $addressDetails['country'] ?? '',
            'added_date' => date('F j, Y g:i A')
        ], $additionalData);

        return $this->sendEmailNotificationByTemplate(
            $adminEmail,
            'New Address Added by Customer - ' . ($customerDetails['name'] ?? 'Unknown'),
            'admin_new_address',
            $data
        );
    }
}
