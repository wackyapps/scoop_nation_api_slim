<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

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
                fullname LIKE %ss OR 
                email LIKE %ss OR 
                phone LIKE %ss
            ORDER BY fullname
        ";
        
        $searchTerm = "%{$query}%";
        return DB::query($sql, $searchTerm, $searchTerm, $searchTerm);
    }

    /**
     * Find customers by city
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city], ['fullname' => 'ASC']);
    }

    /**
     * Find customers by country
     */
    public function findByCountry(string $country): array
    {
        return $this->findBy(['country' => $country], ['city' => 'ASC', 'fullname' => 'ASC']);
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
            ORDER BY c.fullname
        ";
        
        return DB::query($sql);
    }

    /**
     * Update customer's user_id reference
     */
    public function updateUserId(int $customerId, ?int $userId): bool
    {
        return $this->update($customerId, ['user_id' => $userId]) ? true : false;
    }

    /**
     * Get customer statistics
     */
    public function getStatistics(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_customers,
                SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as registered_customers,
                SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_customers,
                COUNT(DISTINCT country) as countries_count,
                COUNT(DISTINCT city) as cities_count
            FROM `customer`
        ";
        
        return DB::queryFirstRow($sql);
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
        
        return DB::query($sql);
    }
}