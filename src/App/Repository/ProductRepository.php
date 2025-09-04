<?php
declare(strict_types=1);
namespace App\Repository;

class ProductRepository extends BaseRepository
{
    protected $table = 'product';
    protected $primaryKey = 'id';

    public function findBySlug(string $slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findByCategory($categoryId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['categoryId' => $categoryId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function search(string $keyword, array $orderBy = null, $limit = null, $offset = null): array
    {
        $query = "
            SELECT * FROM product 
            WHERE title LIKE %ss OR description LIKE %ss
        ";
        
        $params = ["%{$keyword}%", "%{$keyword}%"];

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

    public function findWithImages($productId)
    {
        $query = "
            SELECT p.*, i.imageID, i.image 
            FROM product p 
            LEFT JOIN image i ON p.id = i.productID 
            WHERE p.id = %i
        ";
        
        return DB::query($query, $productId);
    }
}