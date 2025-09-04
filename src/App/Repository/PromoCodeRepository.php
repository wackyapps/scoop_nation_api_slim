<?php
declare(strict_types=1);
namespace App\Repository;

class PromoCodeRepository extends BaseRepository
{
    protected $table = 'promocode';
    protected $primaryKey = 'id';

    public function findByCode(string $code)
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function findValidPromoCode(string $code)
    {
        $currentDate = date('Y-m-d H:i:s');
        
        $query = "
            SELECT * FROM promocode 
            WHERE code = %s 
            AND expiryDate > %s 
            AND (minimumOrderAmount IS NULL OR minimumOrderAmount = 0 OR minimumOrderAmount <= %i)
        ";
        
        // Note: The minimumOrderAmount condition will need to be handled differently
        // This is a simplified version
        return DB::queryFirstRow($query, $code, $currentDate, 0);
    }

    public function getActivePromoCodes(array $orderBy = null, $limit = null, $offset = null): array
    {
        $currentDate = date('Y-m-d H:i:s');
        
        $query = "SELECT * FROM promocode WHERE expiryDate > %s";
        $params = [$currentDate];

        if ($orderBy) {
            $query .= " ORDER BY ";
            $orders = [];
            foreach ($orderBy as $field => $direction) {
                $orders[] = "{$field} {$direction}";
            }
            $query .= implode(', ', $orders);
        }

        if ($limit) {
            $query .= " LIMIT %i";
            $params[] = $limit;
        }

        if ($offset) {
            $query .= " OFFSET %i";
            $params[] = $offset;
        }

        return DB::query($query, ...$params);
    }
}