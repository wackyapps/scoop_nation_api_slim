<?php
declare(strict_types=1);
namespace App\Repository;

class CategoryRepository extends BaseRepository
{
    protected $table = 'category';
    protected $primaryKey = 'id';

    public function findByName(string $name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findWithProducts($categoryId)
    {
        $query = "
            SELECT c.*, p.id as product_id, p.title, p.price 
            FROM category c 
            LEFT JOIN product p ON c.id = p.categoryId 
            WHERE c.id = %i
        ";
        
        return DB::query($query, $categoryId);
    }
}