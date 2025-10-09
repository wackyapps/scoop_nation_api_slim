<?php
// Updated MediaRepository.php
// Handles media operations
// Added deleteByProductId for bulk deletion by product

declare(strict_types=1);
namespace App\Repository;

use DB;

class MediaRepository extends BaseRepository
{
    protected $table = 'media';
    protected $primaryKey = 'imageID';

    public function findMediaByProductId($productId): array
    {
        $criteria = ['productID' => $productId, 'type' => 'product'];
        return $this->findBy($criteria);
    }
    public function findMediaByCampaignId($productId): array
    {
        $criteria = ['campaign_id' => $productId, 'type' => 'banner'];
        return $this->findBy($criteria);
    }

    public function deleteByProductId(int $productId): int
    {
        DB::delete($this->table, "productID = %i AND type = %s", $productId, 'product');
        return DB::affectedRows();
    }
}
?>