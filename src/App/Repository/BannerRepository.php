<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class BannerRepository extends BaseRepository
{
    protected $table = 'banner_campaign';
    protected $primaryKey = 'id';

    /**
     * Get all active banner campaigns that are currently running
     * (is_active = 1 and current date is between start_date and end_date)
     *
     * @return array Array of active campaigns with basic info
     */
    public function getActiveBannerCampaigns(): array
    {
        $query = "
            SELECT id, name, description, start_date, end_date, is_active, created_at, updated_at
            FROM banner_campaign
            WHERE is_active = 1
            AND start_date <= NOW()
            AND end_date >= NOW()
            ORDER BY start_date ASC
        ";
        
        return DB::query($query);
    }

    /**
     * Get banner images for a specific campaign
     *
     * @param int $campaignId The ID of the banner campaign
     * @return array Array of banner media entries
     */
    public function getBannersForCampaign(int $campaignId): array
    {
        $query = "
            SELECT imageID, type, title, description, alt_text, mime_type, file_size,
                   width, height, is_featured, sort_order, banner_position,
                   banner_url, banner_target, campaign_id, status, image, created_at
            FROM media
            WHERE type = 'banner'
            AND campaign_id = %i
            AND status = 'active'
            ORDER BY sort_order ASC, is_featured DESC
        ";
        
        return DB::query($query, $campaignId);
    }

    /**
     * Get meta data for a specific media item
     *
     * @param int $mediaId The ID of the media item
     * @return array Array of meta key-value pairs
     */
    public function getMediaMeta(int $mediaId): array
    {
        $query = "
            SELECT meta_key, meta_value
            FROM media_meta
            WHERE media_id = %i
        ";
        
        $results = DB::query($query, $mediaId);
        
        // Format as associative array: ['key' => 'value']
        $meta = [];
        foreach ($results as $row) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }
        
        return $meta;
    }

    /**
     * Get banners for a campaign with their associated meta data
     * This method fetches banners and enriches each with its meta
     *
     * @param int $campaignId The ID of the banner campaign
     * @return array Array of banners, each with 'meta' key containing key-value pairs
     */
    public function getBannersWithMetaForCampaign(int $campaignId): array
    {
        $banners = $this->getBannersForCampaign($campaignId);
        
        foreach ($banners as &$banner) {
            $banner['meta'] = $this->getMediaMeta((int)$banner['imageID']); // Cast to int
        }
        
        return $banners;
    }

    /**
     * Get all active campaigns with their banners and meta
     * Combines active campaigns with their banners and meta for frontend use
     *
     * @return array Array of campaigns, each with 'banners' key containing banners with meta
     */
    public function getActiveCampaignsWithBannersAndMeta(): array
    {
        $campaigns = $this->getActiveBannerCampaigns();
        $result = [];
        
        foreach ($campaigns as $campaign) {
            $campaign['banners'] = $this->getBannersWithMetaForCampaign((int)$campaign['id']); // Cast to int
            $result[] = $campaign;
        }
        
        return $result;
    }

    /**
     * Get a single banner campaign by ID with banners and meta
     *
     * @param int $campaignId The ID of the banner campaign
     * @return array|null Campaign data with banners and meta, or null if not found
     */
    public function getCampaignWithBannersAndMeta(int $campaignId): ?array
    {
        $campaign = $this->findOneBy(['id' => $campaignId]);
        if (!$campaign) {
            return null;
        }
        
        // Check if active
        $now = date('Y-m-d H:i:s');
        if ($campaign['is_active'] != 1 || $campaign['start_date'] > $now || $campaign['end_date'] < $now) {
            return null; // Or return inactive flag, depending on needs
        }
        
        $campaign['banners'] = $this->getBannersWithMetaForCampaign($campaignId);
        return $campaign;
    }
}