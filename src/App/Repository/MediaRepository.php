<?php
declare(strict_types=1);
namespace App\Repository;

class MediaRepository extends BaseRepository
{
    protected $table = 'media';
    protected $primaryKey = 'imageID';
    public function findMediaByProductId($productId): array
    {
        $criteria = ['productID' => $productId,'type'=>'product'];
        return $this->findBy($criteria, );
    }
}