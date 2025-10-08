<?php
declare(strict_types=1);

namespace App\Repository;
require_once __DIR__ . '/SQL_Table_Names.php';


use DB;

class BannerRepository extends BaseRepository
{
    protected $table = TABLE_BANNER_CAMPAIGN;
    protected $primaryKey = 'id';

    /**
     * Get all active banner campaigns that are currently running
     * Filters by branch_id if provided (NULL for global banners)
     *
     * @param int|null $branchId The ID of the branch to filter banners, or null for all
     * @return array Array of active campaigns with basic info
     */
    public function getActiveBannerCampaigns(?int $branchId = null): array
    {
        $query = "
            SELECT id, name, description, start_date, end_date, is_active, created_at, updated_at, branch_id
            FROM banner_campaign
            WHERE is_active = 1
            AND start_date <= NOW()
            AND end_date >= NOW()
            " . ($branchId ? "AND (branch_id = %i OR branch_id IS NULL)" : "") . "
            ORDER BY start_date ASC
        ";

        return $branchId ? DB::query($query, $branchId) : DB::query($query);
    }
    public function deleteCompaignMedia(int $campaignId): bool
    {
        $this->executeQuery('DELETE FROM ' . TABLE_MEDIA . ' WHERE campaign_id = %i', ['campaign_id' => $campaignId]);
        // $this->executeQuery('DELETE FROM ' . TABLE_MEDIA_META . ' WHERE media_id IN (SELECT imageID FROM ' . TABLE_MEDIA . ' WHERE campaign_id = %i)', $campaignId);
        return true;
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
            FROM " . TABLE_MEDIA . "
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
            FROM " . TABLE_MEDIA_META . "
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
            $banner['meta'] = $this->getMediaMeta((int) $banner['imageID']); // Cast to int
        }

        return $banners;
    }

    /**
     * Get all active campaigns with their banners and meta
     * Combines active campaigns with their banners and meta for frontend use
     *
     * @param int|null $branchId The ID of the branch to filter banners, or null for all
     * @return array Array of campaigns, each with 'banners' key containing banners with meta
     */
    public function getActiveCampaignsWithBannersAndMeta(?int $branchId = null, $limit = 10, $offset = 0): array
    {
        $campaigns = $this->findBy(['branch_id' => $branchId], null, $limit, $offset);
        $result = [];
        foreach ($campaigns as $campaign) {
            $campaign['media'] = $this->getBannersWithMetaForCampaign((int) $campaign['id']); // Cast to int
            $result[] = $campaign;
        }

        return $result;
    }
    public function getAllBanners(?int $branchId = null, $search = null, $limit = 10, $page = 0): array
    {
        $query = "SELECT * FROM banner_campaign ";
        $params = [];
        $whereClauses = [];
        if ($branchId) {
            $whereClauses[] = " branch_id = %i ";
            $params[] = $branchId;
        }
        if ($search) {
            $whereClauses[] = "  (name LIKE %s OR description LIKE %s) ";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if (!empty($whereClauses)) {
            $query .= "WHERE " . implode(" AND ", $whereClauses) . " ";
        }

        $query .= " ORDER BY start_date DESC LIMIT %i OFFSET %i";
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;


        $campaigns = DB::query($query, ...$params);

        $countQuery = "SELECT COUNT(id) as total FROM banner_campaign ";
        $countWhereClauses = [];
        $countParams = [];



        if ($branchId) {
            $countWhereClauses[] = " branch_id = %i ";
            $countParams[] = $branchId; // Bug: Appending to wrong array ($params instead of $countParams)
        }
        if ($search) {
            $countWhereClauses[] = " (name LIKE %s OR description LIKE %s) ";
            $countParams[] = "%{$search}%";
            $countParams[] = "%{$search}%";
        }
        if (!empty($countWhereClauses)) {
            $countQuery .= "WHERE " . implode(" AND ", $countWhereClauses) . " "; // Bug: Appending to $query instead of $countQuery
        }
        $totalResult = DB::queryFirstRow($countQuery, ...$countParams);

        $result = [];
        foreach ($campaigns as $campaign) {
            $campaign['media'] = $this->getBannersWithMetaForCampaign((int) $campaign['id']); // Cast to int
            $result[] = $campaign;
        }

        return ['data' => $result, 'total' => $totalResult['total']];
    }



    /**
     * Get a single banner campaign by ID with banners and meta
     *
     * @param int $campaignId The ID of the banner campaign
     * @param int|null $branchId The ID of the branch to verify campaign applicability
     * @return array|null Campaign data with banners and meta, or null if not found/active
     */
    public function getCampaignWithBannersAndMeta(int $campaignId, ?int $branchId = null): ?array
    {
        $campaign = $this->findOneBy(['id' => $campaignId]);
        if (!$campaign) {
            return null;
        }

        // Check if active and branch matches (if specified)
        $now = date('Y-m-d H:i:s');
        if ($campaign['is_active'] != 1 || $campaign['start_date'] > $now || $campaign['end_date'] < $now) {
            return null;
        }
        if ($branchId && $campaign['branch_id'] !== null && $campaign['branch_id'] != $branchId) {
            return null; // Campaign is branch-specific and doesn't match
        }

        $campaign['banners'] = $this->getBannersWithMetaForCampaign($campaignId);
        return $campaign;
    }
}