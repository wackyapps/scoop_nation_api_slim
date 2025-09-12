<?php
declare(strict_types=1);

namespace App\Repository;

use DB;
use App\Services\EmailService;

class UserRepository extends BaseRepository
{
    protected $table = 'user';
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
                c.phone,
                c.company,
                c.address,
                c.apartment,
                c.postalCode,
                c.city,
                c.country,
                c.createdAt as customer_created,
                c.updatedAt as customer_updated
            FROM `user` u
            LEFT JOIN `customer` c ON u.id = c.user_id
            WHERE u.id = %i
        ";
        
        return DB::queryFirstRow($sql, $userId);
    }

    /**
     * Find user by email with customer profile
     */
    public function findByEmailWithProfile(string $email)
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
                c.country,
                c.createdAt as customer_created,
                c.updatedAt as customer_updated
            FROM `user` u
            LEFT JOIN `customer` c ON u.id = c.user_id
            WHERE u.email = %s
        ";
        
        return DB::queryFirstRow($sql, $email);
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
        if ($user && $user->role === 'customer' && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
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
        $otpService->saveOtp($user->id, $otp);
        
        // Send email (assuming EmailService exists)
        $emailService = new EmailService();
        return $emailService->sendOtp($email, $otp);
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
            FROM `user` u
            LEFT JOIN `customer` c ON u.id = c.user_id
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
}