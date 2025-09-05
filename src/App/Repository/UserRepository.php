<?php
declare(strict_types=1);
namespace App\Repository;

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
                c.firstname,
                c.lastname,
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
            WHERE u.id = ?
        ";
        
        $result = $this->executeQuery($sql, [$userId]);
        return is_object($result) ? $result->fetch() : $result;
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
                c.firstname,
                c.lastname,
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
            WHERE u.email = ?
        ";
        
        $result = $this->executeQuery($sql, [$email]);
        return is_object($result) ? $result->fetch() : $result;
    }

    /**
     * Create a new user and optionally link to an existing customer
     */
    public function createUserWithCustomerLink(array $userData, ?int $customerId = null): int
    {
        $this->getDB()->beginTransaction();
        
        try {
            // Insert user
            $userId = $this->insert($userData);
            
            // If customer ID provided, link the customer to this user
            if ($customerId !== null) {
                $this->linkCustomerToUser($customerId, $userId);
            }
            
            $this->getDB()->commit();
            return $userId;
            
        } catch (\Exception $e) {
            $this->getDB()->rollBack();
            throw $e;
        }
    }

    /**
     * Link an existing customer to a user
     */
    public function linkCustomerToUser(int $customerId, int $userId): bool
    {
        $sql = "UPDATE `customer` SET user_id = ? WHERE id = ?";
        $stmt = $this->getDB()->prepare($sql);
        return $stmt->execute([$userId, $customerId]);
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
                c.firstname,
                c.lastname,
                c.phone,
                c.email as customer_email,
                c.company,
                c.address,
                c.apartment,
                c.postalCode,
                c.city,
                c.country
            FROM `user` u
            LEFT JOIN `customer` c ON u.id = c.user_id
        ";
        
        // Add ORDER BY if specified
        if ($orderBy !== null) {
            $orderParts = [];
            foreach ($orderBy as $field => $direction) {
                $orderParts[] = "u.{$field} {$direction}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderParts);
        }
        
        // Add LIMIT and OFFSET if specified
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        
        $result = $this->executeQuery($sql);
        return is_object($result) ? $result->fetchAll() : $result;
    }

    /**
     * Find customers that are not linked to any user (guest customers)
     */
    public function findGuestCustomers(): array
    {
        $sql = "
            SELECT c.* 
            FROM `customer` c 
            WHERE c.user_id IS NULL
        ";
        
        $result = $this->executeQuery($sql);
        return is_object($result) ? $result->fetchAll() : $result;
    }
}