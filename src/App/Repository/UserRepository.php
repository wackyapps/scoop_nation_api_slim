<?php
declare(strict_types=1);

namespace App\Repository;
require_once __DIR__ . '/SQL_Table_Names.php';


use DB;
use App\Services\EmailService;
use App\Services\OtpService;

class UserRepository extends BaseRepository
{
    protected $table = TABLE_USER;
    protected $primaryKey = 'id';

    public function findByEmail(string $email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findByRole(string $role, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['role' => $role];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find user with their customer profile information
     */
    public function findUserWithCustomerProfile(int $userId)
    {
        $sql = "
            SELECT 
                u.*,
                c.id as customer_id,
                c.fullname,
                c.gender,
                c.date_of_birth,
                c.createdAt as customer_created,
                c.updatedAt as customer_updated
            FROM " . TABLE_USER . " u
            LEFT JOIN " . TABLE_CUSTOMER . " c ON u.id = c.user_id
            WHERE u.id = %i
        ";

        $user = DB::queryFirstRow($sql, $userId);
        // var_dump($user);
        return $user;
    }

    /**
     * Find user by email with customer profile
     */
    public function findByEmailWithProfile(string $email)
    {
        $sql = "
            SELECT 
                u.id,
                u.email,
                u.password,
                u.role,
                u.phone_verified,
                u.email_verified,
                u.phone,
                u.createdAt as user_created,
                c.id as customer_id,
                c.fullname,
                c.gender,
                c.date_of_birth
                #,
                #c.createdAt as customer_created,
                #c.updatedAt as customer_updated
            FROM " . TABLE_USER . " u
            LEFT JOIN " . TABLE_CUSTOMER . " c ON u.id = c.user_id
            WHERE u.email = %s
        ";

        $record = DB::queryFirstRow($sql, $email);
        // var_dump($record);
        return $record;
    }

    /**
     * Register a new customer user and create customer record
     */
    public function registerCustomerUser(array $userData, array $customerData)
    {
        DB::startTransaction();
        try {
            $userData['role'] = 'customer';
            $userId = $this->save($userData);

            $customerData['user_id'] = $userId;
            $customerRepository = new CustomerRepository();
            $customerRepository->save($customerData);

            DB::commit();
            return $userId;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Login customer user - verify credentials
     */
    public function loginCustomerUser(string $email, string $password): ?array
    {
        $user = $this->findByEmailWithProfile($email);
        if ($user && $user["role"] === 'customer' && password_verify($password, $user["password"])) {
            // remove password field from $user
            unset($user['password']);
            return $user;
        }

        // when user is not customer role type them return array with success false and error

        return array("success" => false, "error" => "No customer account found with given credentials");
    }
    /**
     * Login admin user - verify credentials
     */
    public function loginAdminUser(string $email, string $password): ?array
    {
        $user = $this->findByEmailWithProfile($email);

        // if user is administrator or delivery_rider and password matches then return user data without password field
        if ($user && in_array($user["role"], ['administrator', 'delivery_rider'])) {
            if (password_verify($password, $user["password"])) {
                // remove password field from $user
                unset($user['password']);
                return $user;
            }
        } 
        return array("success" => false, "error" => "No administrator or delivery_rider account found with given credentials");
    }

    /**
     * Register user with specific role (admin or rider)
     */
    public function registerUserWithRole(array $userData, string $role)
    {
        if (!in_array($role, ['admin', 'rider'])) {
            throw new \Exception('Invalid role for this method');
        }
        $userData['role'] = $role;
        return $this->save($userData);
    }

    /**
     * Handle forgot password - generate and send OTP
     */
    public function forgotUserPassword(string $email): bool
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }

        // Generate OTP (assuming you have an OtpService)
        $otpService = new OtpService();
        $otp = $otpService->generateOtp();
        $otpString = (string)$otp;
        // turn $otp to password_hash
        $saved = $this->saveOtp($user['email'], password_hash($otpString, PASSWORD_DEFAULT));

        // Send email if OTP saved successfully
        if (!$saved) {
            return false;
        }    

        // Send email
        $emailService = new EmailService();
        return $emailService->sendOtp($email, $otpString);
    }

    /**
     * private function to save OTP to database against user id
     */

    private function saveOtp(string $email, string $otp): bool
    {
        // find user by email
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }

        // save user otp to user table in password 
        $this->update($user['id'], ['password' => $otp]);
        return true;
    }

    /**
     * Save user profile updates
     */
    public function saveProfile(int $userId, array $userData, array $customerData): bool
    {
        DB::startTransaction();
        try {
            $this->update($userId, $userData);

            $customerRepository = new CustomerRepository();
            $customer = $customerRepository->findOneBy(['user_id' => $userId]);
            if ($customer) {
                $customerRepository->update($customer['id'], $customerData);
            } else {
                $customerData['user_id'] = $userId;
                $customerRepository->save($customerData);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get all users with their customer profiles (if available)
     */
    public function findAllWithProfiles(array $orderBy = null, $limit = null, $offset = null): array
    {
        $sql = "
            SELECT 
                u.*,
                c.id as customer_id,
                c.fullname,
                c.gender,
                c.date_of_birth,
                c.phone,
                c.company,
                c.address,
                c.apartment,
                c.postalCode,
                c.city,
                c.country
            FROM " . TABLE_USER . " u
            LEFT JOIN " . TABLE_CUSTOMER . " c ON u.id = c.user_id
        ";

        if ($orderBy) {
            $sql .= " ORDER BY ";
            $orders = [];
            foreach ($orderBy as $field => $direction) {
                $orders[] = "u.{$field} {$direction}";
            }
            $sql .= implode(', ', $orders);
        }

        if ($limit) {
            $sql .= " LIMIT %i";
            if ($offset) {
                $sql .= " OFFSET %i";
            }
        }

        return DB::query($sql, $limit ? ($offset ? [$limit, $offset] : [$limit]) : []);
    }

    /**
     * Find customers that are not linked to any user (guest customers)
     */
    public function findGuestCustomers(): array
    {
        $customerRepository = new CustomerRepository();
        return $customerRepository->findBy(['user_id' => null]);
    }

    public function linkCustomerToUser(int $customerId, int $userId)
    {
        $customerRepository = new CustomerRepository();
        return $customerRepository->update($customerId, ['user_id' => $userId]);
    }

    /**
     * Generate and store email verification token
     * 
     * @param int $userId
     * @return string Plain token (to be sent in email)
     */
    public function generateEmailVerificationToken(int $userId): string
    {
        // Generate secure 64-character token
        $plainToken = bin2hex(random_bytes(32));
        
        // Hash token before storing
        $hashedToken = password_hash($plainToken, PASSWORD_DEFAULT);
        
        // Set expiration to 24 hours from now
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Update user record with hashed token and expiration
        $this->update($userId, [
            'email_verification_token' => $hashedToken,
            'email_verification_expires_at' => $expiresAt
        ]);
        
        // Return plain token for email
        return $plainToken;
    }

    /**
     * Verify email using token
     * 
     * @param string $token Plain token from email link
     * @return bool Success status
     */
    public function verifyEmailWithToken(string $token): bool
    {
        // Find all users to check token match
        $sql = "SELECT * FROM " . TABLE_USER . " WHERE email_verification_token IS NOT NULL";
        $users = DB::query($sql);
        
        $matchedUser = null;
        foreach ($users as $user) {
            if (password_verify($token, $user['email_verification_token'])) {
                $matchedUser = $user;
                break;
            }
        }
        
        if (!$matchedUser) {
            return false;
        }
        
        // Check if token is expired
        if ($this->isVerificationTokenExpired((int) $matchedUser['id'])) {
            return false;
        }
        
        // Update email_verified to 1 and clear verification token fields
        $this->update($matchedUser['id'], [
            'email_verified' => 1,
            'email_verification_token' => null,
            'email_verification_expires_at' => null
        ]);
        
        return true;
    }

    /**
     * Check if verification token is expired
     * 
     * @param int $userId
     * @return bool True if expired
     */
    public function isVerificationTokenExpired(int $userId): bool
    {
        $user = $this->find($userId);
        
        if (!$user || !$user['email_verification_expires_at']) {
            return true;
        }
        
        $expiresAt = strtotime($user['email_verification_expires_at']);
        $now = time();
        
        return $now > $expiresAt;
    }

    /**
     * Resend verification email
     * 
     * @param string $email
     * @return bool Success status
     */
    public function resendVerificationEmail(string $email): bool
    {
        // Find user by email
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Check if email is already verified
        if ($user['email_verified'] == 1) {
            return false;
        }
        
        // Generate new verification token (this also invalidates existing token)
        $token = $this->generateEmailVerificationToken((int)$user['id']);
        
        // Get user name for email
        $userName = $user['email'];
        $sql = "SELECT c.fullname FROM " . TABLE_CUSTOMER . " c WHERE c.user_id = %i";
        $customer = DB::queryFirstRow($sql, $user['id']);
        if ($customer && !empty($customer['fullname'])) {
            $userName = $customer['fullname'];
        }
        
        // Send verification email via EmailService
        $emailService = new EmailService();
        return $emailService->sendCustomerVerificationEmail($email, $token, $userName);
    }

    /**
     * Generate and store password reset token
     * 
     * @param string $email
     * @return array ['success' => bool, 'token' => string|null]
     */
    public function generatePasswordResetToken(string $email): array
    {
        // Find user by email
        $user = $this->findByEmail($email);
        
        if (!$user) {
            // Return success even if user not found (prevent email enumeration)
            return ['success' => true, 'token' => null];
        }
        
        // Check rate limit (max 3 requests per hour)
        if (!$this->checkResetRateLimit($email)) {
            return ['success' => false, 'token' => null, 'error' => 'RATE_LIMIT_EXCEEDED'];
        }
        
        // Generate secure 64-character token
        $plainToken = bin2hex(random_bytes(32));
        
        // Hash token before storing
        $hashedToken = password_hash($plainToken, PASSWORD_DEFAULT);
        
        // Set expiration to 1 hour from now
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update user record with hashed token and expiration
        $this->update($user['id'], [
            'password_reset_token' => $hashedToken,
            'password_reset_expires_at' => $expiresAt
        ]);
        
        // Return array with success status and plain token
        return ['success' => true, 'token' => $plainToken];
    }

    /**
     * Validate password reset token
     * 
     * @param string $token Plain token from email link
     * @return array|null User data if valid, null otherwise
     */
    public function validatePasswordResetToken(string $token): ?array
    {
        // Find all users to check token match
        $sql = "SELECT * FROM " . TABLE_USER . " WHERE password_reset_token IS NOT NULL";
        $users = DB::query($sql);
        
        $matchedUser = null;
        foreach ($users as $user) {
            if (password_verify($token, $user['password_reset_token'])) {
                $matchedUser = $user;
                break;
            }
        }
        
        if (!$matchedUser) {
            return null;
        }
        
        // Check if token is expired
        if ($this->isResetTokenExpired($matchedUser['id'])) {
            return null;
        }
        
        // Return user data if valid
        return $matchedUser;
    }

    /**
     * Reset password using token
     * 
     * @param string $token
     * @param string $newPassword
     * @return bool Success status
     */
    public function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        // Validate token
        $user = $this->validatePasswordResetToken($token);
        
        if (!$user) {
            return false;
        }
        
        // Validate new password meets requirements (min 8 characters)
        if (strlen($newPassword) < 8) {
            return false;
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update user password and clear password reset token fields
        $this->update($user['id'], [
            'password' => $hashedPassword,
            'password_reset_token' => null,
            'password_reset_expires_at' => null
        ]);
        
        return true;
    }

    /**
     * Check if reset token is expired
     * 
     * @param int $userId
     * @return bool True if expired
     */
    public function isResetTokenExpired(int $userId): bool
    {
        $user = $this->find($userId);
        
        if (!$user || !$user['password_reset_expires_at']) {
            return true;
        }
        
        $expiresAt = strtotime($user['password_reset_expires_at']);
        $now = time();
        
        return $now > $expiresAt;
    }

    /**
     * Check reset rate limit (max 3 requests per hour)
     * 
     * @param string $email
     * @return bool True if within limit
     */
    private function checkResetRateLimit(string $email): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM " . TABLE_USER . " 
                WHERE email = %s 
                AND password_reset_expires_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $result = DB::queryFirstRow($sql, $email);
        return $result['count'] < 3;
    }
}
