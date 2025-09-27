<?php
declare(strict_types=1);

namespace App\Repository;
require_once __DIR__ . '/SQL_Table_Names.php';

use DB;

class BranchRepository extends BaseRepository
{
    protected $table = TABLE_BRANCH;
    protected $primaryKey = 'id';

    /**
     * Get complete branch homepage data with all related entities
     * Optimized single query to fetch branch, timings, business, banners, products, media, bundles
     * 
     * @param int $businessId Business ID to filter
     * @param int $branchId Branch ID to filter
     * @return array Complete branch homepage data structure
     */
    public function getBranchHomePage(int $businessId, int $branchId): array
    {
        // Main optimized query to fetch all required data in a single join
        $query = "
            SELECT 
                -- Branch data
                b.id as branch_id,
                b.name as branch_name,
                b.city,
                b.address,
                b.apartment,
                b.area,
                b.country,
                b.postal_code,
                b.latitude,
                b.longitude,
                b.is_physical,
                b.pickup_instructions,
                b.delivery_status,
                b.customer_support_email,
                b.contact_number,
                b.delivery_module,
                b.is_active as branch_is_active,
                b.created_at as branch_created_at,
                b.updated_at as branch_updated_at,
                
                -- Business data (assuming business table exists)
                bs.id as business_id,
                bs.name as business_name,
                bs.logo as business_logo,
                bs.description as business_description,
                
                -- Branch timings
                bt.id as timing_id,
                bt.day_of_week,
                bt.open_time,
                bt.close_time,
                bt.is_closed,
                bt.created_at as timing_created_at,
                bt.updated_at as timing_updated_at,
                
                -- Banner campaigns (both global and branch-specific)
                bc.id as campaign_id,
                bc.name as campaign_name,
                bc.description as campaign_description,
                bc.start_date,
                bc.end_date,
                bc.is_active as campaign_is_active,
                bc.branch_id as campaign_branch_id,
                
                -- Banner media
                m.imageID,
                m.type as media_type,
                m.title as media_title,
                m.description as media_description,
                m.alt_text,
                m.mime_type,
                m.file_size,
                m.width,
                m.height,
                m.is_featured,
                m.sort_order,
                m.banner_position,
                m.banner_url,
                m.banner_target,
                m.status as media_status,
                m.image,
                
                -- Branch products
                bp.id as branch_product_id,
                bp.product_id,
                bp.variant_id,
                bp.is_available,
                bp.branch_price,
                bp.min_order_quantity,
                bp.max_order_quantity,
                bp.created_at as branch_product_created_at,
                bp.updated_at as branch_product_updated_at,
                
                -- Products
                p.id as product_id,
                p.slug,
                p.title as product_title,
                p.mainImage,
                p.price,
                p.discountType,
                p.discountValue,
                p.originalPrice,
                p.discountStartDate,
                p.discountEndDate,
                p.rating,
                p.description as product_description,
                p.manufacturer,
                p.inStock,
                p.categoryId,
                
                -- Bundles
                bd.id as bundle_id,
                bd.name as bundle_name,
                bd.discountedPrice,
                bd.discountType as bundle_discountType,
                bd.discountValue as bundle_discountValue,
                bd.originalPrice as bundle_originalPrice,
                bd.discountStartDate as bundle_discountStartDate,
                bd.discountEndDate as bundle_discountEndDate,
                
                -- Bundle products
                bdp.id as bundle_product_id,
                bdp.productId as bundle_productId,
                bdp.variantId as bundle_variantId
                
            FROM branch b
            LEFT JOIN business bs ON b.business_id = bs.id
            LEFT JOIN branch_timings bt ON b.id = bt.branch_id
            LEFT JOIN banner_campaign bc ON (bc.branch_id = b.id OR bc.branch_id IS NULL)
                AND bc.is_active = 1 
                AND bc.start_date <= NOW() 
                AND bc.end_date >= NOW()
            LEFT JOIN media m ON bc.id = m.campaign_id 
                AND m.type = 'banner' 
                AND m.status = 'active'
            LEFT JOIN branch_product bp ON b.id = bp.branch_id AND bp.is_available = 1
            LEFT JOIN product p ON bp.product_id = p.id
            LEFT JOIN bundle_product bdp ON p.id = bdp.productId
            LEFT JOIN bundle bd ON bdp.bundleId = bd.id
                AND (bd.discountStartDate IS NULL OR bd.discountStartDate <= NOW())
                AND (bd.discountEndDate IS NULL OR bd.discountEndDate >= NOW())
            WHERE b.business_id = %i 
                AND b.id = %i
                AND b.is_active = 1
            ORDER BY 
                bt.day_of_week ASC,
                m.sort_order ASC,
                m.is_featured DESC,
                p.title ASC
        ";

        $results = DB::query($query, $businessId, $branchId);

        if (empty($results)) {
            return [];
        }

        // Transform flat result set into nested structure
        return $this->transformToNestedStructure($results);
    }

    /**
     * Transform flat query results into nested JSON-friendly structure
     * 
     * @param array $results Raw query results
     * @return array Nested structure for API response
     */
    private function transformToNestedStructure(array $results): array
    {
        $branchData = [
            'branch' => [],
            'business' => [],
            'timings' => [],
            'banners' => [],
            'products' => [],
            'bundles' => []
        ];

        $bannerCampaigns = [];
        $products = [];
        $bundles = [];
        $timings = [];

        foreach ($results as $row) {
            // Initialize branch data (only once)
            if (empty($branchData['branch'])) {
                $branchData['branch'] = [
                    'id' => (int) $row['branch_id'],
                    'name' => $row['branch_name'],
                    'city' => $row['city'],
                    'address' => $row['address'],
                    'apartment' => $row['apartment'],
                    'area' => $row['area'],
                    'country' => $row['country'],
                    'postal_code' => $row['postal_code'],
                    'latitude' => $row['latitude'] ? (float) $row['latitude'] : null,
                    'longitude' => $row['longitude'] ? (float) $row['longitude'] : null,
                    'is_physical' => (int) $row['is_physical'],
                    'pickup_instructions' => $row['pickup_instructions'],
                    'delivery_status' => (int) $row['delivery_status'],
                    'customer_support_email' => $row['customer_support_email'],
                    'contact_number' => $row['contact_number'],
                    'delivery_module' => (int) $row['delivery_module'],
                    'is_active' => (int) $row['branch_is_active'],
                    'created_at' => $row['branch_created_at'],
                    'updated_at' => $row['branch_updated_at']
                ];
            }

            // Initialize business data (only once)
            if (empty($branchData['business']) && $row['business_id']) {
                $branchData['business'] = [
                    'id' => (int) $row['business_id'],
                    'name' => $row['business_name'],
                    'logo' => $row['business_logo'],
                    'description' => $row['business_description']
                ];
            }

            // Process timings
            if ($row['timing_id'] && !isset($timings[$row['timing_id']])) {
                $timings[$row['timing_id']] = [
                    'id' => (int) $row['timing_id'],
                    'day_of_week' => (int) $row['day_of_week'],
                    'open_time' => $row['open_time'],
                    'close_time' => $row['close_time'],
                    'is_closed' => (int) $row['is_closed'],
                    'created_at' => $row['timing_created_at'],
                    'updated_at' => $row['timing_updated_at']
                ];
            }

            // Process banner campaigns and media
            if ($row['campaign_id'] && !isset($bannerCampaigns[$row['campaign_id']])) {
                $bannerCampaigns[$row['campaign_id']] = [
                    'id' => (int) $row['campaign_id'],
                    'name' => $row['campaign_name'],
                    'description' => $row['campaign_description'],
                    'start_date' => $row['start_date'],
                    'end_date' => $row['end_date'],
                    'is_active' => (int) $row['campaign_is_active'],
                    'branch_id' => $row['campaign_branch_id'] ? (int) $row['campaign_branch_id'] : null,
                    'media' => []
                ];
            }

            // Add media to banner campaign
            if ($row['campaign_id'] && $row['imageID']) {
                $mediaItem = [
                    'id' => (int) $row['imageID'],
                    'type' => $row['media_type'],
                    'title' => $row['media_title'],
                    'description' => $row['media_description'],
                    'alt_text' => $row['alt_text'],
                    'mime_type' => $row['mime_type'],
                    'file_size' => $row['file_size'] ? (int) $row['file_size'] : null,
                    'width' => $row['width'] ? (int) $row['width'] : null,
                    'height' => $row['height'] ? (int) $row['height'] : null,
                    'is_featured' => (int) $row['is_featured'],
                    'sort_order' => (int) $row['sort_order'],
                    'banner_position' => $row['banner_position'],
                    'banner_url' => $row['banner_url'],
                    'banner_target' => $row['banner_target'],
                    'status' => $row['media_status'],
                    'image' => $row['image']
                ];

                if (!in_array($mediaItem, $bannerCampaigns[$row['campaign_id']]['media'])) {
                    $bannerCampaigns[$row['campaign_id']]['media'][] = $mediaItem;
                }
            }

            // Process products
            if ($row['product_id'] && !isset($products[$row['product_id']])) {
                $productRepository = new  ProductRepository();
                $variants = $productRepository->getProductVariantByProductId((int) $row['product_id']) ?? [];
                $mediaRepository = new MediaRepository();
                $media = $mediaRepository->findMediaByProductId((int) $row['product_id']) ?? [];


                $products[$row['product_id']] = [
                    'id' => (int) $row['product_id'],
                    'slug' => $row['slug'],
                    'title' => $row['product_title'],
                    'mainImage' => $row['mainImage'],
                    'price' => (int) $row['price'],
                    'discountType' => $row['discountType'],
                    'discountValue' => $row['discountValue'] ? (int) $row['discountValue'] : null,
                    'originalPrice' => $row['originalPrice'] ? (int) $row['originalPrice'] : null,
                    'discountStartDate' => $row['discountStartDate'],
                    'discountEndDate' => $row['discountEndDate'],
                    'rating' => (int) $row['rating'],
                    'description' => $row['product_description'],
                    'manufacturer' => $row['manufacturer'],
                    'inStock' => (int) $row['inStock'],
                    'categoryId' => (int) $row['categoryId'],
                    'branch_product' => [
                        'id' => (int) $row['branch_product_id'],
                        'is_available' => (int) $row['is_available'],
                        'branch_price' => $row['branch_price'] ? (int) $row['branch_price'] : null,
                        'min_order_quantity' => (int) $row['min_order_quantity'],
                        'max_order_quantity' => $row['max_order_quantity'] ? (int) $row['max_order_quantity'] : null,
                        'created_at' => $row['branch_product_created_at'],
                        'updated_at' => $row['branch_product_updated_at']
                    ],
                    'bundles' => [],
                    'variants' => $variants,
                    'media'=>$media
                ];
            }

            // Process bundles
            if ($row['bundle_id'] && !isset($bundles[$row['bundle_id']])) {
                $bundles[$row['bundle_id']] = [
                    'id' => (int) $row['bundle_id'],
                    'name' => $row['bundle_name'],
                    'discountedPrice' => (int) $row['discountedPrice'],
                    'discountType' => $row['bundle_discountType'],
                    'discountValue' => $row['bundle_discountValue'] ? (int) $row['bundle_discountValue'] : null,
                    'originalPrice' => $row['bundle_originalPrice'] ? (int) $row['bundle_originalPrice'] : null,
                    'discountStartDate' => $row['bundle_discountStartDate'],
                    'discountEndDate' => $row['bundle_discountEndDate'],
                    'products' => []
                ];
            }

            // Add product to bundle if applicable
            if ($row['bundle_id'] && $row['product_id']) {
                $bundleProduct = [
                    'id' => (int) $row['bundle_product_id'],
                    'productId' => (int) $row['bundle_productId'],
                    'variantId' => $row['bundle_variantId'] ? (int) $row['bundle_variantId'] : null
                ];

                if (!in_array($bundleProduct, $bundles[$row['bundle_id']]['products'])) {
                    $bundles[$row['bundle_id']]['products'][] = $bundleProduct;
                }

                // Also add bundle reference to product
                if (!in_array($bundles[$row['bundle_id']], $products[$row['product_id']]['bundles'])) {
                    $products[$row['product_id']]['bundles'][] = $bundles[$row['bundle_id']];
                }
            }
        }

        // Assign collected data to branchData
        $branchData['timings'] = array_values($timings);
        $branchData['banners'] = array_values($bannerCampaigns);
        $branchData['products'] = array_values($products);
        $branchData['bundles'] = array_values($bundles);

        return $branchData;
    }

    /**
     * Get branch with minimal data for quick lookups
     * 
     * @param int $branchId Branch ID
     * @return array|null Branch data or null if not found
     */
    public function getBranchSummary(int $branchId): ?array
    {
        $query = "
            SELECT 
                b.id,
                b.name,
                b.city,
                b.address,
                b.area,
                b.country,
                b.postal_code,
                b.latitude,
                b.longitude,
                b.is_physical,
                b.delivery_status,
                b.is_active,
                bs.name as business_name,
                bs.logo as business_logo
            FROM branch b
            LEFT JOIN business bs ON b.business_id = bs.id
            WHERE b.id = %i AND b.is_active = 1
        ";

        return DB::queryFirstRow($query, $branchId);
    }

    /**
     * Get branch operating hours for a specific day
     * 
     * @param int $branchId Branch ID
     * @param int $dayOfWeek Day of week (1=Monday, 7=Sunday)
     * @return array|null Timing data or null if not found
     */
    public function getBranchTimingByDay(int $branchId, int $dayOfWeek): ?array
    {
        $query = "
            SELECT * 
            FROM branch_timings 
            WHERE branch_id = %i AND day_of_week = %i
        ";

        return DB::queryFirstRow($query, $branchId, $dayOfWeek);
    }

    /**
     * Get all active branches for a business
     * 
     * @param int $businessId Business ID
     * @return array Array of active branches
     */
    public function getActiveBranchesByBusiness(int $businessId): array
    {
        $query = "
            SELECT 
                b.id,
                b.name,
                b.city,
                b.address,
                b.area,
                b.country,
                b.postal_code,
                b.latitude,
                b.longitude,
                b.is_physical,
                b.delivery_status,
                b.is_active,
                COUNT(bt.id) as timing_count
            FROM branch b
            LEFT JOIN branch_timings bt ON b.id = bt.branch_id
            WHERE b.business_id = %i AND b.is_active = 1
            GROUP BY b.id
            ORDER BY b.name ASC
        ";

        return DB::query($query, $businessId);
    }

    /**
     * Check if branch is open at current time
     * 
     * @param int $branchId Branch ID
     * @return bool True if branch is open, false otherwise
     */
    public function isBranchOpen(int $branchId): bool
    {
        $dayOfWeek = date('N'); // 1=Monday, 7=Sunday
        $currentTime = date('H:i:s');

        $query = "
            SELECT * 
            FROM branch_timings 
            WHERE branch_id = %i 
                AND day_of_week = %i 
                AND is_closed = 0 
                AND open_time IS NOT NULL 
                AND close_time IS NOT NULL 
                AND %s BETWEEN open_time AND close_time
        ";

        $timing = DB::queryFirstRow($query, $branchId, $dayOfWeek, $currentTime);
        return $timing !== null;
    }

    /**
     * Get branch timings for a specific branch
     * 
     * @param int $branchId Branch ID
     * @return array Array of branch timings
     */
    public function getBranchTimings(int $branchId): array
    {
        $query = "
            SELECT * FROM branch_timings 
            WHERE branch_id = %i 
            ORDER BY day_of_week ASC
        ";

        return DB::query($query, $branchId);
    }


    /**
     * Get active banner campaigns for a branch
     * 
     * @param int $branchId Branch ID
     * @return array Array of active banner campaigns
     */
    public function getActiveBannerCampaignsForBranch(int $branchId): array
    {
        $query = "
            SELECT bc.*, m.imageID, m.image, m.title, m.description, m.banner_position, m.banner_url, m.banner_target
            FROM banner_campaign bc
            LEFT JOIN media m ON bc.id = m.campaign_id AND m.type = 'banner' AND m.status = 'active'
            WHERE (bc.branch_id = %i OR bc.branch_id IS NULL)
                AND bc.is_active = 1 
                AND bc.start_date <= NOW() 
                AND bc.end_date >= NOW()
            ORDER BY bc.start_date ASC, m.sort_order ASC, m.is_featured DESC
        ";

        $results = DB::query($query, $branchId);

        // Structure the results to group media by campaign
        $campaigns = [];
        $campaignMap = [];

        foreach ($results as $row) {
            if (!isset($campaignMap[$row['id']])) {
                $campaigns[] = [
                    'id' => (int) $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'start_date' => $row['start_date'],
                    'end_date' => $row['end_date'],
                    'is_active' => (int) $row['is_active'],
                    'media' => []
                ];
                $campaignMap[$row['id']] = count($campaigns) - 1;
            }

            if ($row['imageID']) {
                $campaigns[$campaignMap[$row['id']]]['media'][] = [
                    'id' => (int) $row['imageID'],
                    'image' => $row['image'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'banner_position' => $row['banner_position'],
                    'banner_url' => $row['banner_url'],
                    'banner_target' => $row['banner_target']
                ];
            }
        }

        return $campaigns;
    }


    /**
     * Get available products for a branch
     * 
     * @param int $branchId Branch ID
     * @return array Array of available products with branch-specific data
     */
    public function getAvailableProductsForBranch(int $branchId): array
    {
        $query = "
            SELECT p.*, bp.id as branch_product_id, bp.is_available, bp.branch_price, 
                   bp.min_order_quantity, bp.max_order_quantity
            FROM product p
            INNER JOIN branch_product bp ON p.id = bp.product_id
            WHERE bp.branch_id = %i AND bp.is_available = 1
            ORDER BY p.title ASC
        ";
        
        return DB::query($query, $branchId);
    }

    /**
     * Get Privacy Policy for a branch or global business privacy policy 
     * 
     * @params int @businessId Business ID
     * @param int $branchId Branch ID (optional)
     */

    public function getPrivacyPolicy(int $businessId, ?int $branchId = null)
    {
        $query = "
            SELECT * 
            FROM privacy_policy 
            WHERE (branch_id = %i OR branch_id IS NULL) 
            AND business_id = %i
        ";

        return DB::queryFirstRow($query, $branchId, $businessId);
    }



    
}