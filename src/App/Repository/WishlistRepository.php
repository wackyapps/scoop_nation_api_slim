<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class WishlistRepository extends BaseRepository
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';

    /**
     * Add product to favorite (wishlist)
     */
    public function addProductToFavorite(int $userId, int $productId): int
    {
        // Check if already exists
        $existing = $this->findOneBy(['userId' => $userId, 'productId' => $productId]);
        if ($existing) {
            return $existing['id'];
        }
        
        return $this->save(['userId' => $userId, 'productId' => $productId]);
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