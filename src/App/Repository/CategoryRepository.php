<?php
declare(strict_types=1);
namespace App\Repository;

use DB;

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

    /**
     * Get all categories with their banners and associated products including discounts, variants, bundles, and images
     */
    public function getAllCategoriesWithBannersAndProducts()
    {
        $query = "
            SELECT 
                c.id as category_id,
                c.name as category_name,
                c.mainImage as category_banner,
                
                -- Product fields
                p.id as product_id,
                p.slug as product_slug,
                p.title as product_title,
                p.mainImage as product_image,
                p.price as product_price,
                p.originalPrice as product_original_price,
                p.discountType as product_discount_type,
                p.discountValue as product_discount_value,
                p.discountStartDate as product_discount_start,
                p.discountEndDate as product_discount_end,
                p.rating as product_rating,
                p.description as product_description,
                p.manufacturer as product_manufacturer,
                p.inStock as product_in_stock,
                
                -- Variant fields
                v.id as variant_id,
                v.name as variant_name,
                v.value as variant_value,
                v.price as variant_price,
                v.originalPrice as variant_original_price,
                v.discountType as variant_discount_type,
                v.discountValue as variant_discount_value,
                v.discountStartDate as variant_discount_start,
                v.discountEndDate as variant_discount_end,
                v.inStock as variant_in_stock,
                
                -- Additional images
                i.imageID as additional_image_id,
                i.image as additional_image,
                
                -- Bundle fields
                b.id as bundle_id,
                b.name as bundle_name,
                b.discountedPrice as bundle_price,
                b.originalPrice as bundle_original_price,
                b.discountType as bundle_discount_type,
                b.discountValue as bundle_discount_value,
                b.discountStartDate as bundle_discount_start,
                b.discountEndDate as bundle_discount_end
                
            FROM category c
            LEFT JOIN product p ON c.id = p.categoryId
            LEFT JOIN variant v ON p.id = v.productId
            LEFT JOIN media i ON p.id = i.productID
            LEFT JOIN bundle_product bp ON p.id = bp.productId OR v.id = bp.variantId
            LEFT JOIN bundle b ON bp.bundleId = b.id
            WHERE p.inStock = 1
            ORDER BY c.name, p.title, v.name, v.value
        ";
        
        $results = DB::query($query);
        
        // Group products by category
        $categories = [];
        foreach ($results as $row) {
            $categoryId = $row['category_id'];
            
            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'id' => $categoryId,
                    'name' => $row['category_name'],
                    'banner' => $row['category_banner'],
                    'products' => []
                ];
            }
            
            if ($row['product_id']) {
                $productId = $row['product_id'];
                $productKey = array_search($productId, array_column($categories[$categoryId]['products'], 'id'));
                
                if ($productKey === false) {
                    // New product
                    $product = $this->formatProductData($row);
                    $categories[$categoryId]['products'][] = $product;
                    $productKey = count($categories[$categoryId]['products']) - 1;
                } else {
                    $product = &$categories[$categoryId]['products'][$productKey];
                }
                
                // Add variant if exists
                if ($row['variant_id']) {
                    $variantId = $row['variant_id'];
                    $variantKey = array_search($variantId, array_column($product['variants'], 'id'));
                    
                    if ($variantKey === false) {
                        $product['variants'][] = $this->formatVariantData($row);
                    }
                }
                
                // Add additional image if exists
                if ($row['additional_image_id']) {
                    $imageId = $row['additional_image_id'];
                    $imageKey = array_search($imageId, array_column($product['additionalImages'], 'id'));
                    
                    if ($imageKey === false) {
                        $product['additionalImages'][] = [
                            'id' => $row['additional_image_id'],
                            'image' => $row['additional_image']
                        ];
                    }
                }
                
                // Add bundle if exists
                if ($row['bundle_id']) {
                    $bundleId = $row['bundle_id'];
                    $bundleKey = array_search($bundleId, array_column($product['bundles'], 'id'));
                    
                    if ($bundleKey === false) {
                        $product['bundles'][] = $this->formatBundleData($row);
                    }
                }
            }
        }
        
        return array_values($categories);
    }

    /**
     * Get specific category with banner and products including discounts, variants, bundles, and images
     */
    public function getCategoryWithBannerAndProducts($categoryId)
    {
        $query = "
            SELECT 
                c.id as category_id,
                c.name as category_name,
                c.mainImage as category_banner,
                
                -- Product fields
                p.id as product_id,
                p.slug as product_slug,
                p.title as product_title,
                p.mainImage as product_image,
                p.price as product_price,
                p.originalPrice as product_original_price,
                p.discountType as product_discount_type,
                p.discountValue as product_discount_value,
                p.discountStartDate as product_discount_start,
                p.discountEndDate as product_discount_end,
                p.rating as product_rating,
                p.description as product_description,
                p.manufacturer as product_manufacturer,
                p.inStock as product_in_stock,
                
                -- Variant fields
                v.id as variant_id,
                v.name as variant_name,
                v.value as variant_value,
                v.price as variant_price,
                v.originalPrice as variant_original_price,
                v.discountType as variant_discount_type,
                v.discountValue as variant_discount_value,
                v.discountStartDate as variant_discount_start,
                v.discountEndDate as variant_discount_end,
                v.inStock as variant_in_stock,
                
                -- Additional images
                i.imageID as additional_image_id,
                i.image as additional_image,
                
                -- Bundle fields
                b.id as bundle_id,
                b.name as bundle_name,
                b.discountedPrice as bundle_price,
                b.originalPrice as bundle_original_price,
                b.discountType as bundle_discount_type,
                b.discountValue as bundle_discount_value,
                b.discountStartDate as bundle_discount_start,
                b.discountEndDate as bundle_discount_end
                
            FROM category c
            LEFT JOIN product p ON c.id = p.categoryId
            LEFT JOIN variant v ON p.id = v.productId
            LEFT JOIN media i ON p.id = i.productID
            LEFT JOIN bundle_product bp ON p.id = bp.productId OR v.id = bp.variantId
            LEFT JOIN bundle b ON bp.bundleId = b.id
            WHERE c.id = %i AND p.inStock = 1
            ORDER BY p.title, v.name, v.value
        ";
        
        $results = DB::query($query, $categoryId);
        
        if (empty($results)) {
            return null;
        }
        
        $category = [
            'id' => $results[0]['category_id'],
            'name' => $results[0]['category_name'],
            'banner' => $results[0]['category_banner'],
            'products' => []
        ];
        
        $products = [];
        foreach ($results as $row) {
            if ($row['product_id']) {
                $productId = $row['product_id'];
                
                if (!isset($products[$productId])) {
                    $products[$productId] = $this->formatProductData($row);
                }
                
                $product = &$products[$productId];
                
                // Add variant if exists
                if ($row['variant_id']) {
                    $variantId = $row['variant_id'];
                    $variantKey = array_search($variantId, array_column($product['variants'], 'id'));
                    
                    if ($variantKey === false) {
                        $product['variants'][] = $this->formatVariantData($row);
                    }
                }
                
                // Add additional image if exists
                if ($row['additional_image_id']) {
                    $imageId = $row['additional_image_id'];
                    $imageKey = array_search($imageId, array_column($product['additionalImages'], 'id'));
                    
                    if ($imageKey === false) {
                        $product['additionalImages'][] = [
                            'id' => $row['additional_image_id'],
                            'image' => $row['additional_image']
                        ];
                    }
                }
                
                // Add bundle if exists
                if ($row['bundle_id']) {
                    $bundleId = $row['bundle_id'];
                    $bundleKey = array_search($bundleId, array_column($product['bundles'], 'id'));
                    
                    if ($bundleKey === false) {
                        $product['bundles'][] = $this->formatBundleData($row);
                    }
                }
            }
        }
        
        $category['products'] = array_values($products);
        return $category;
    }

    /**
     * Format product data with discounts
     */
    private function formatProductData(array $row): array
    {
        return [
            'id' => $row['product_id'],
            'slug' => $row['product_slug'],
            'title' => $row['product_title'],
            'image' => $row['product_image'],
            'price' => $row['product_price'],
            'originalPrice' => $row['product_original_price'],
            'discount' => [
                'type' => $row['product_discount_type'],
                'value' => $row['product_discount_value'],
                'startDate' => $row['product_discount_start'],
                'endDate' => $row['product_discount_end'],
                'isActive' => $this->isDiscountActive($row['product_discount_start'], $row['product_discount_end'])
            ],
            'rating' => $row['product_rating'],
            'description' => $row['product_description'],
            'manufacturer' => $row['product_manufacturer'],
            'inStock' => $row['product_in_stock'],
            'variants' => [],
            'additionalImages' => [],
            'bundles' => []
        ];
    }

    /**
     * Format variant data with discounts
     */
    private function formatVariantData(array $row): array
    {
        return [
            'id' => $row['variant_id'],
            'name' => $row['variant_name'],
            'value' => $row['variant_value'],
            'price' => $row['variant_price'],
            'originalPrice' => $row['variant_original_price'],
            'discount' => [
                'type' => $row['variant_discount_type'],
                'value' => $row['variant_discount_value'],
                'startDate' => $row['variant_discount_start'],
                'endDate' => $row['variant_discount_end'],
                'isActive' => $this->isDiscountActive($row['variant_discount_start'], $row['variant_discount_end'])
            ],
            'inStock' => $row['variant_in_stock']
        ];
    }

    /**
     * Format bundle data with discounts
     */
    private function formatBundleData(array $row): array
    {
        return [
            'id' => $row['bundle_id'],
            'name' => $row['bundle_name'],
            'price' => $row['bundle_price'],
            'originalPrice' => $row['bundle_original_price'],
            'discount' => [
                'type' => $row['bundle_discount_type'],
                'value' => $row['bundle_discount_value'],
                'startDate' => $row['bundle_discount_start'],
                'endDate' => $row['bundle_discount_end'],
                'isActive' => $this->isDiscountActive($row['bundle_discount_start'], $row['bundle_discount_end'])
            ]
        ];
    }

    /**
     * Check if discount is currently active
     */
    private function isDiscountActive(?string $startDate, ?string $endDate): bool
    {
        if (!$startDate || !$endDate) {
            return false;
        }
        
        $now = time();
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        
        return $now >= $start && $now <= $end;
    }

    /**
     * Get category by ID with basic info
     */
    public function getCategoryById($categoryId)
    {
        return $this->findOneBy(['id' => $categoryId]);
    }

    /**
     * Get all categories with basic info
     */
    public function getAllCategories()
    {
        return $this->findAll();
    }
}