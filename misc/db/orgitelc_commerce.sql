-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2025 at 04:58 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `orgitelc_commerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `address_type` enum('home','work','other') COLLATE utf8mb4_unicode_ci DEFAULT 'home',
  `street_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Pakistan',
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_type`, `street_address`, `city`, `state`, `postal_code`, `country`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 3, 'home', '123 Home St', 'Karachi', 'Sindh', '74000', 'Pakistan', 1, '2025-09-19 07:20:00', '2025-09-19 07:20:00'),
(2, 5, 'work', '456 Work Ave', 'Karachi', 'Sindh', '74000', 'Pakistan', 0, '2025-09-19 07:25:00', '2025-09-19 07:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `banner_campaign`
--

CREATE TABLE `banner_campaign` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banner_campaign`
--

INSERT INTO `banner_campaign` (`id`, `name`, `description`, `start_date`, `end_date`, `is_active`, `created_by`, `updated_by`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'Summer Sale', 'Discount on cones', '2025-06-01 00:00:00', '2025-08-31 23:59:59', 1, 1, 1, 1, '2025-09-19 11:50:00.000', '2025-09-19 11:50:00.000'),
(2, 'Winter Promo', 'Warm deals', '2025-12-01 00:00:00', '2026-02-28 23:59:59', 1, 1, 1, 1, '2025-09-19 11:55:00.000', '2025-09-19 11:55:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `id` bigint(20) NOT NULL,
  `business_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., "Downtown Outlet" or "Online Delivery Hub"',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Branch city',
  `address` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Branch street address',
  `apartment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Branch house / office number',
  `area` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Branch area name',
  `country` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Branch country name',
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Branch postal code',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'For geolocation',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'For geolocation',
  `is_physical` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1 = Physical outlet, 0 = Online-only (virtual pickup for delivery)',
  `pickup_instructions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Special instructions for riders/customers',
  `delivery_status` tinyint(1) NOT NULL DEFAULT 1,
  `customer_support_email` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer support email address',
  `contact_number` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer support contact phone number (whats app)',
  `delivery_module` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Rider delivery module enabled or disabled',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`id`, `business_id`, `name`, `city`, `address`, `apartment`, `area`, `country`, `postal_code`, `latitude`, `longitude`, `is_physical`, `pickup_instructions`, `delivery_status`, `customer_support_email`, `contact_number`, `delivery_module`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Karachi Main Branch', 'Karachi', 'Shahrah-e-Faisal', 'Shop 101', 'Saddar', 'Pakistan', '74000', '24.86070000', '67.00110000', 1, 'Pickup at counter', 1, 'support@omore.com', '+923001234567', 1, 1, 1, 1, '2025-09-19 11:10:00.000', '2025-09-19 11:10:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `branch_product`
--

CREATE TABLE `branch_product` (
  `id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `variant_id` bigint(20) DEFAULT NULL COMMENT 'Optional for specific variants',
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `branch_price` int(11) DEFAULT NULL COMMENT 'Override global price if needed',
  `min_order_quantity` int(11) DEFAULT 1,
  `max_order_quantity` int(11) DEFAULT NULL,
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_product`
--

INSERT INTO `branch_product` (`id`, `branch_id`, `product_id`, `variant_id`, `is_available`, `branch_price`, `min_order_quantity`, `max_order_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 145, 1, 20, '2025-09-19 11:20:00.000', '2025-09-19 11:20:00.000'),
(2, 1, 2, NULL, 1, NULL, 1, 15, '2025-09-19 11:25:00.000', '2025-09-19 11:25:00.000'),
(3, 1, 3, 4, 1, 135, 2, 10, '2025-09-19 11:30:00.000', '2025-09-19 11:30:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `branch_special_days`
--

CREATE TABLE `branch_special_days` (
  `id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `date` date NOT NULL COMMENT 'Special date (holiday/exception)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reason for special timing',
  `open_time` time DEFAULT NULL COMMENT 'Special opening time',
  `close_time` time DEFAULT NULL COMMENT 'Special closing time',
  `is_closed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Closed for this special day',
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_special_days`
--

INSERT INTO `branch_special_days` (`id`, `branch_id`, `date`, `description`, `open_time`, `close_time`, `is_closed`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12-25', 'Christmas Holiday', NULL, NULL, 1, '2025-09-19 11:35:00.000', '2025-09-19 11:35:00.000'),
(2, 1, '2025-09-20', 'Special Event', '10:00:00', '16:00:00', 0, '2025-09-19 11:40:00.000', '2025-09-19 11:40:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `branch_timings`
--

CREATE TABLE `branch_timings` (
  `id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `day_of_week` tinyint(1) NOT NULL COMMENT '1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday, 7=Sunday',
  `open_time` time DEFAULT NULL COMMENT 'Opening time (NULL means closed)',
  `close_time` time DEFAULT NULL COMMENT 'Closing time (NULL means closed)',
  `is_closed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Closed for the entire day',
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_timings`
--

INSERT INTO `branch_timings` (`id`, `branch_id`, `day_of_week`, `open_time`, `close_time`, `is_closed`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '09:00:00', '21:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000'),
(2, 1, 2, '09:00:00', '21:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000'),
(3, 1, 3, '09:00:00', '21:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000'),
(4, 1, 4, '09:00:00', '21:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000'),
(5, 1, 5, '09:00:00', '21:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000'),
(6, 1, 6, '10:00:00', '22:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000'),
(7, 1, 7, '10:00:00', '22:00:00', 0, '2025-09-19 11:45:00.000', '2025-09-19 11:45:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `bundle`
--

CREATE TABLE `bundle` (
  `id` bigint(20) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discountedPrice` int(11) NOT NULL,
  `discountType` enum('PERCENTAGE','FIXED_AMOUNT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discountValue` int(11) DEFAULT NULL,
  `originalPrice` int(11) DEFAULT NULL,
  `discountStartDate` datetime(3) DEFAULT NULL,
  `discountEndDate` datetime(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bundle`
--

INSERT INTO `bundle` (`id`, `name`, `discountedPrice`, `discountType`, `discountValue`, `originalPrice`, `discountStartDate`, `discountEndDate`) VALUES
(1, 'Cone Bundle', 400, 'FIXED_AMOUNT', 50, 450, '2025-09-19 00:00:00.000', '2025-10-19 23:59:59.000'),
(2, 'Family Cone Pack', 600, 'PERCENTAGE', 15, 700, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bundle_product`
--

CREATE TABLE `bundle_product` (
  `id` bigint(20) NOT NULL,
  `bundleId` bigint(20) NOT NULL,
  `productId` bigint(20) DEFAULT NULL,
  `variantId` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bundle_product`
--

INSERT INTO `bundle_product` (`id`, `bundleId`, `productId`, `variantId`) VALUES
(1, 1, 1, 1),
(2, 1, 2, NULL),
(3, 2, 3, 4),
(4, 2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE `business` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loyality_points` tinyint(1) NOT NULL DEFAULT 0,
  `promo_codde` tinyint(1) NOT NULL DEFAULT 0,
  `auto_order_accept` tinyint(1) NOT NULL DEFAULT 0,
  `timezone` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_user_id` bigint(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business`
--

INSERT INTO `business` (`id`, `name`, `description`, `logo`, `loyality_points`, `promo_codde`, `auto_order_accept`, `timezone`, `owner_user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ScoopNation Ice Cream', 'Premium ice cream shop', 'omore_logo.png', 1, 1, 1, 'Asia/Karachi', 1, 1, '2025-09-19 11:05:00.000', '2025-09-19 19:42:53.921');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` bigint(20) NOT NULL,
  `userId` bigint(20) DEFAULT NULL,
  `sessionId` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdAt` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updatedAt` datetime(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `userId`, `sessionId`, `createdAt`, `updatedAt`) VALUES
(1, 3, 'sess123', '2025-09-19 13:10:00.000', '2025-09-19 13:10:00.000'),
(2, 5, 'sess456', '2025-09-19 13:15:00.000', '2025-09-19 13:15:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `id` bigint(20) NOT NULL,
  `cartId` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `variantId` bigint(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `createdAt` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updatedAt` datetime(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`id`, `cartId`, `productId`, `variantId`, `quantity`, `createdAt`, `updatedAt`) VALUES
(1, 1, 1, 1, 3, '2025-09-19 13:20:00.000', '2025-09-19 13:20:00.000'),
(2, 2, 3, 4, 1, '2025-09-19 13:25:00.000', '2025-09-19 13:25:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mainImage` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `mainImage`, `branch_id`) VALUES
(1, 'Ice Cream Cones', 'cones.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `fullname` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `createdAt` datetime(3) DEFAULT current_timestamp(3),
  `updatedAt` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3),
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `user_id`, `fullname`, `createdAt`, `updatedAt`, `gender`, `date_of_birth`) VALUES
(1, 3, 'Customer One', '2025-09-19 12:10:00.000', '2025-09-19 12:10:00.000', 'male', '1990-01-01'),
(2, 5, 'Customer Two', '2025-09-19 12:15:00.000', '2025-09-19 12:15:00.000', 'female', '1995-05-05');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_products`
--

CREATE TABLE `favorite_products` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorite_products`
--

INSERT INTO `favorite_products` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 3, 1, '2025-09-19 07:30:00'),
(2, 5, 2, '2025-09-19 07:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `imageID` bigint(20) NOT NULL,
  `type` enum('product','banner','gallery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'product',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image/jpeg',
  `file_size` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `productID` bigint(20) DEFAULT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `banner_position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci DEFAULT '_self',
  `campaign_id` bigint(20) DEFAULT NULL,
  `status` enum('active','inactive','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`imageID`, `type`, `title`, `description`, `alt_text`, `mime_type`, `file_size`, `width`, `height`, `is_featured`, `sort_order`, `productID`, `category_id`, `banner_position`, `banner_url`, `banner_target`, `campaign_id`, `status`, `created_by`, `updated_by`, `image`, `created_at`) VALUES
(1, 'product', 'Choco Cone Image', 'Image of chocolate cone', 'Choco alt', 'image/jpeg', 1024, 800, 600, 1, 1, 1, NULL, NULL, NULL, '_self', NULL, 'active', 1, 1, 'choco_cone.png', '2025-09-19 07:40:00'),
(2, 'banner', 'Sale Banner', 'Summer sale banner', 'Sale alt', 'image/png', 2048, 1200, 400, 0, 2, NULL, NULL, 'top', 'https://example.com', '_blank', 1, 'active', 1, 1, 'banner.png', '2025-09-19 07:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `media_meta`
--

CREATE TABLE `media_meta` (
  `id` bigint(20) NOT NULL,
  `media_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_meta`
--

INSERT INTO `media_meta` (`id`, `media_id`, `meta_key`, `meta_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'resolution', '800x600', '2025-09-19 12:50:00.000', '2025-09-19 12:50:00.000'),
(2, 2, 'campaign_type', 'seasonal', '2025-09-19 12:55:00.000', '2025-09-19 12:55:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `rider_id` bigint(20) DEFAULT NULL,
  `dateTime` datetime(3) DEFAULT current_timestamp(3),
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int(11) NOT NULL,
  `orderNotice` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_number` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `customer_id`, `branch_id`, `rider_id`, `dateTime`, `status`, `total`, `orderNotice`, `order_number`) VALUES
(1, 1, 1, 1, '2025-09-19 13:00:00.000', 'pending', 300, 'Deliver fast', 'ORD-001'),
(2, 2, 1, 2, '2025-09-19 13:05:00.000', 'shipped', 250, NULL, 'ORD-002');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` bigint(20) NOT NULL,
  `customerOrderId` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `variantId` bigint(20) DEFAULT NULL,
  `bundleId` bigint(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`id`, `customerOrderId`, `productId`, `variantId`, `bundleId`, `quantity`) VALUES
(1, 1, 1, 1, 1, 2),
(2, 2, 2, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` bigint(20) NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mainImage` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `discountType` enum('PERCENTAGE','FIXED_AMOUNT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discountValue` int(11) DEFAULT NULL,
  `originalPrice` int(11) DEFAULT NULL,
  `discountStartDate` datetime(3) DEFAULT NULL,
  `discountEndDate` datetime(3) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inStock` int(11) NOT NULL DEFAULT 1,
  `categoryId` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `slug`, `title`, `mainImage`, `price`, `discountType`, `discountValue`, `originalPrice`, `discountStartDate`, `discountEndDate`, `rating`, `description`, `manufacturer`, `inStock`, `categoryId`) VALUES
(1, 'chocolate-cone', 'Chocolate Cone', 'choco_cone.png', 150, 'PERCENTAGE', 10, 165, '2025-09-01 00:00:00.000', '2025-12-31 23:59:59.000', 4, 'Rich chocolate ice cream cone', 'Omore', 100, 1),
(2, 'vanilla-cone', 'Vanilla Cone', 'vanilla_cone.png', 120, NULL, NULL, NULL, NULL, NULL, 5, 'Classic vanilla ice cream cone', 'Omore', 150, 1),
(3, 'strawberry-cone', 'Strawberry Cone', 'strawberry_cone.png', 140, 'FIXED_AMOUNT', 20, 160, '2025-09-15 00:00:00.000', '2025-10-15 23:59:59.000', 3, 'Fresh strawberry ice cream cone', 'Omore', 80, 1);

-- --------------------------------------------------------

--
-- Table structure for table `promocode`
--

CREATE TABLE `promocode` (
  `id` bigint(20) NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discountAmount` int(11) NOT NULL,
  `discountType` enum('FLAT','PERCENTAGE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FLAT',
  `expiryDate` datetime(3) NOT NULL,
  `minimumOrderAmount` int(11) DEFAULT NULL,
  `createdAt` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updatedAt` datetime(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promocode`
--

INSERT INTO `promocode` (`id`, `code`, `discountAmount`, `discountType`, `expiryDate`, `minimumOrderAmount`, `createdAt`, `updatedAt`) VALUES
(1, 'OMORE10', 10, 'PERCENTAGE', '2025-12-31 23:59:59.000', 300, '2025-09-19 11:15:00.000', '2025-09-19 11:15:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `rider`
--

CREATE TABLE `rider` (
  `id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Optional link to user for login/app access',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` enum('bike','car','van') COLLATE utf8mb4_unicode_ci DEFAULT 'bike',
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `current_status` enum('available','busy','offline') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Current location',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Current location',
  `created_at` datetime(3) DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rider`
--

INSERT INTO `rider` (`id`, `branch_id`, `user_id`, `name`, `phone`, `vehicle_type`, `license_number`, `is_active`, `current_status`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'John Rider', '+923001234570', 'bike', 'BIKE123', 1, 'available', '24.86070000', '67.00110000', '2025-09-19 12:00:00.000', '2025-09-19 12:00:00.000'),
(2, 1, NULL, 'Jane Rider', '+923001234571', 'car', 'CAR456', 1, 'busy', '24.87070000', '67.01110000', '2025-09-19 12:05:00.000', '2025-09-19 12:05:00.000');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'administrator', 'Full system access', '2025-09-19 05:00:00'),
(2, 'visitor', 'Unauthenticated user', '2025-09-19 05:05:00'),
(3, 'customer', 'Registered customer', '2025-09-19 05:10:00'),
(4, 'delivery_rider', 'Delivery personnel', '2025-09-19 05:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) NOT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique, persistent session identifier (e.g., UUID or long random string)',
  `user_id` bigint(20) DEFAULT NULL COMMENT 'Links to user table after login/registration',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IPv4 or IPv6 address of the visitor',
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Browser/device fingerprint for additional identification',
  `cart_id` bigint(20) DEFAULT NULL COMMENT 'Links to the cart table for this anonymous session',
  `cookie_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token stored in frontend cookie for persistent identification',
  `last_activity` datetime(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3) COMMENT 'Timestamp of last interaction',
  `created_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `expires_at` datetime(3) DEFAULT NULL COMMENT 'Optional: Set an expiry date for long-term persistence',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Flag to mark if session is active or abandoned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Manages anonymous user sessions for cart persistence';

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `session_id`, `user_id`, `ip_address`, `user_agent`, `cart_id`, `cookie_token`, `last_activity`, `created_at`, `expires_at`, `is_active`) VALUES
(1, 'sess123', 3, '192.168.0.1', 'Mozilla/5.0', 1, 'cookie123', '2025-09-19 13:30:00.000', '2025-09-19 13:30:00.000', '2025-09-20 13:30:00.000', 1),
(2, 'sess456', 5, '192.168.0.2', 'Mozilla/5.0', 2, 'cookie456', '2025-09-19 13:35:00.000', '2025-09-19 13:35:00.000', '2025-09-20 13:35:00.000', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email address for authentication',
  `phone` varchar(65) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mobile phone number',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'encrypted password',
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'user' COMMENT 'User role assigned (customer, administrator, rider)',
  `phone_verified` tinyint(1) DEFAULT 0 COMMENT 'Phone number verified',
  `email_verified` tinyint(1) DEFAULT 0 COMMENT 'Email address verified',
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `phone`, `password`, `role`, `phone_verified`, `email_verified`, `createdAt`) VALUES
(1, 'admin@scoopnation.com', '+923001234567', '$2y$10$dummyadminhash', 'administrator', 1, 1, '2025-09-19 10:20:00'),
(2, 'visitor@scoopnation.com', '+923001234568', NULL, 'visitor', 0, 0, '2025-09-19 10:25:00'),
(3, 'customer@scoopnation.com', '+923001234569', '$2y$10$dummycustomerhash', 'customer', 1, 1, '2025-09-19 10:30:00'),
(4, 'rider@scoopnation.com', '+923001234570', '$2y$10$dummyriderhash', 'delivery_rider', 1, 1, '2025-09-19 10:35:00'),
(5, 'customer2@scoopnation.com', '+923001234571', '$2y$10$dummyhash2', 'customer', 0, 0, '2025-09-19 10:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`) VALUES
(1, 1, 1, '2025-09-19 05:45:00'),
(2, 2, 2, '2025-09-19 05:50:00'),
(3, 3, 3, '2025-09-19 05:55:00'),
(4, 4, 4, '2025-09-19 06:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `variant`
--

CREATE TABLE `variant` (
  `id` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `discountType` enum('PERCENTAGE','FIXED_AMOUNT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discountValue` int(11) DEFAULT NULL,
  `originalPrice` int(11) DEFAULT NULL,
  `discountStartDate` datetime(3) DEFAULT NULL,
  `discountEndDate` datetime(3) DEFAULT NULL,
  `inStock` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant`
--

INSERT INTO `variant` (`id`, `productId`, `name`, `value`, `price`, `discountType`, `discountValue`, `originalPrice`, `discountStartDate`, `discountEndDate`, `inStock`) VALUES
(1, 1, 'Size', 'Regular', 150, NULL, NULL, NULL, NULL, NULL, 50),
(2, 1, 'Size', 'Large', 200, 'PERCENTAGE', 5, 210, '2025-09-01 00:00:00.000', '2025-12-31 23:59:59.000', 50),
(3, 2, 'Topping', 'Nuts', 130, NULL, NULL, NULL, NULL, NULL, 75),
(4, 3, 'Flavor', 'Extra Berry', 150, NULL, NULL, NULL, NULL, NULL, 40);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `productId`, `userId`) VALUES
(1, 1, 3),
(2, 2, 5),
(3, 3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner_campaign`
--
ALTER TABLE `banner_campaign`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_banner_campaign_active` (`is_active`),
  ADD KEY `idx_banner_campaign_dates` (`start_date`,`end_date`),
  ADD KEY `fk_banner_campaign_created_by` (`created_by`),
  ADD KEY `fk_banner_campaign_updated_by` (`updated_by`),
  ADD KEY `fk_banner_campaign_branch` (`branch_id`),
  ADD KEY `idx_banner_campaign_branch_date` (`branch_id`,`start_date`,`end_date`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_branch_business` (`business_id`),
  ADD KEY `fk_branch_created_by` (`created_by`),
  ADD KEY `fk_branch_updated_by` (`updated_by`);

--
-- Indexes for table `branch_product`
--
ALTER TABLE `branch_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_branch_product_variant` (`branch_id`,`product_id`,`variant_id`),
  ADD KEY `fk_branch_product_branch` (`branch_id`),
  ADD KEY `fk_branch_product_product` (`product_id`),
  ADD KEY `fk_branch_product_variant` (`variant_id`);

--
-- Indexes for table `branch_special_days`
--
ALTER TABLE `branch_special_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_branch_date` (`branch_id`,`date`),
  ADD KEY `fk_branch_special_days_branch` (`branch_id`),
  ADD KEY `idx_special_date` (`date`);

--
-- Indexes for table `branch_timings`
--
ALTER TABLE `branch_timings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_branch_day` (`branch_id`,`day_of_week`),
  ADD KEY `fk_branch_timings_branch` (`branch_id`);

--
-- Indexes for table `bundle`
--
ALTER TABLE `bundle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bundle_product`
--
ALTER TABLE `bundle_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `BundleProduct_bundleId_fkey` (`bundleId`),
  ADD KEY `BundleProduct_productId_fkey` (`productId`),
  ADD KEY `BundleProduct_variantId_fkey` (`variantId`);

--
-- Indexes for table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_business_owner_user` (`owner_user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Cart_userId_key` (`userId`),
  ADD UNIQUE KEY `Cart_sessionId_key` (`sessionId`),
  ADD KEY `Cart_userId_fkey` (`userId`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `CartItem_cartId_productId_variantId_key` (`cartId`,`productId`,`variantId`),
  ADD KEY `CartItem_productId_fkey` (`productId`),
  ADD KEY `CartItem_variantId_fkey` (`variantId`),
  ADD KEY `CartItem_cartId_fkey` (`cartId`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_branch` (`branch_id`),
  ADD KEY `idx_category_branch` (`branch_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
