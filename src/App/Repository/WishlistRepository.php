<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class WishlistRepository extends BaseRepository
{
    protected $table = TABLE_WISHLIST;
    protected $primaryKey = 'id';


    /**
     * Get all favorites for a user
     */
    public function getAllFavorites(int $userId ,int $page,int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT * FROM {$this->table}  WHERE userId = %i LIMIT %i OFFSET %i";
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table} WHERE userId = %i";

        $params = [$userId, $perPage, $offset];
        $countParams = [$userId];
        $items = DB::query($query, ...$params);
        $total = DB::queryFirstRow($countQuery, ...$countParams);
        
        $totalCount = $total['total'] ?? 0;
        return [
            'data' => $items,
            'total' => $totalCount,
            'per_page' => $perPage,
            'total_pages' => ceil($totalCount / $perPage),
            'page' => $page
        ];
    }

    /**
     * Add product to favorite (wishlist)
     */
    public function addProductToFavorite(int $userId, int $productId): int
    {
        // Check if already exists
        $existing = $this->findOneBy(['userId' => $userId, 'productId' => $productId]);
        if ($existing) {
            return (int) $existing['id'];
        }

        return (int) $this->save(['userId' => $userId, 'productId' => $productId]);
    }

    /**
     * Remove product from favorite (wishlist)
     */
    public function removeProductFromFavorite(int $userId, int $productId): bool
    {
        $wishlistItem = $this->findOneBy(['userId' => $userId, 'productId' => $productId]);
        if (!$wishlistItem) {
            return false;
        }

        $this->delete($wishlistItem['id']);
        return true;
    }
}