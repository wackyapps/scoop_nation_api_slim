<?php
declare(strict_types=1);

namespace App\Repository;

class CustomerRepository extends BaseRepository
{
    protected $table = 'customer';
    protected $primaryKey = 'id';

    /**
     * Find customer by email
     */
    public function findByEmail(string $email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Search customers by name or email
     */
    public function search(string $query): array
    {
        $sql = "
            SELECT * 
            FROM `customer` 
            WHERE 
                firstname LIKE ? OR 
                lastname LIKE ? OR 
                email LIKE ? OR 
                CONCAT(firstname, ' ', lastname) LIKE ?
            ORDER BY firstname, lastname
        ";
        
        $searchTerm = "%{$query}%";
        $result = $this->executeQuery($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return is_object($result) ? $result->fetchAll() : $result;
    }

    /**
     * Find customers by city
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city], ['firstname' => 'ASC', 'lastname' => 'ASC']);
    }

    /**
     * Find customers by country
     */
    public function findByCountry(string $country): array
    {
        return $this->findBy(['country' => $country], ['city' => 'ASC', 'firstname' => 'ASC']);
    }

    /**
     * Find customers without user accounts (guest customers)
     */
    public function findGuestCustomers(): array
    {
        return $this->findBy(['user_id' => null], ['createdAt' => 'DESC']);
    }

    /**
     * Find customers with user accounts
     */
    public function findRegisteredCustomers(): array
    {
        $sql = "
            SELECT c.*, u.role as user_role 
            FROM `customer` c 
            INNER JOIN `user` u ON c.user_id = u.id 
            ORDER BY c.firstname, c.lastname
        ";
        
        $result = $this->executeQuery($sql);
        return is_object($result) ? $result->fetchAll() : $result;
    }

    /**
     * Update customer's user_id reference
     */
    public function updateUserId(int $customerId, ?int $userId): bool
    {
        return $this->update($customerId, ['user_id' => $userId]);
    }

    /**
     * Get customer statistics
     */
    public function getStatistics(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_customers,
                COUNT(user_id) as registered_customers,
                COUNT(*) - COUNT(user_id) as guest_customers,
                COUNT(DISTINCT country) as countries_count,
                COUNT(DISTINCT city) as cities_count
            FROM `customer`
        ";
        
        $result = $this->executeQuery($sql);
        return is_object($result) ? $result->fetch() : $result;
    }

    /**
     * Get customers with their order count
     */
    public function findCustomersWithOrderCount(): array
    {
        $sql = "
            SELECT 
                c.*,
                COUNT(o.id) as order_count,
                COALESCE(SUM(o.total), 0) as total_spent
            FROM `customer` c
            LEFT JOIN `order` o ON c.id = o.customer_id
            GROUP BY c.id
            ORDER BY total_spent DESC, order_count DESC
        ";
        
        $result = $this->executeQuery($sql);
        return is_object($result) ? $result->fetchAll() : $result;
    }
}