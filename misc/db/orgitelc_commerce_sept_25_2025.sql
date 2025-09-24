-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 09:08 PM
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `longitude` decimal(10,8) NOT NULL,
  `latitude` decimal(10,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_type`, `street_address`, `city`, `state`, `postal_code`, `country`, `is_default`, `created_at`, `updated_at`, `longitude`, `latitude`) VALUES
(1, 3, 'home', '123 Home St', 'Karachi', 'Sindh', '74000', 'Pakistan', 1, '2025-09-19 07:20:00', '2025-09-19 07:20:00', '0.00000000', '0.00000000'),
(2, 5, 'work', '456 Work Ave', 'Karachi', 'Sindh', '74000', 'Pakistan', 0, '2025-09-19 07:25:00', '2025-09-19 07:25:00', '0.00000000', '0.00000000');

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
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` bigint(20) NOT NULL,
  `business_id` bigint(20) DEFAULT NULL COMMENT 'Reference to business table',
  `branch_id` bigint(20) DEFAULT NULL COMMENT 'Reference to branch table',
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `submission_type` enum('general','support','feedback','complaint','partnership') COLLATE utf8mb4_unicode_ci DEFAULT 'general' COMMENT 'Type of contact submission',
  `status` enum('new','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'new' COMMENT 'Status of the submission',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User IP address for tracking',
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User browser information',
  `referrer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Page where form was submitted from',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `business_id`, `branch_id`, `full_name`, `email_address`, `phone_number`, `message`, `submission_type`, `status`, `ip_address`, `user_agent`, `referrer`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Waqas Mahmmood', 'wmkhan101@gmail.com', '03109438554', 'Hi, I would like to open a franchise of Scoop Nation in Kasur.', 'partnership', 'new', '::1', 'PostmanRuntime/7.47.1', '', '2025-09-24 18:00:51', '2025-09-24 18:00:51'),
(2, 1, 1, 'Waqas Mahmmood', 'wmkhan101@gmail.com', '03109438554', 'Hi, I would like to open a franchise of Scoop Nation in Kasur.', 'partnership', 'new', '::1', 'PostmanRuntime/7.47.1', '', '2025-09-24 18:01:07', '2025-09-24 18:01:07'),
(3, 1, 1, 'Waqas Mahmmood', 'wmkhan101@gmail.com', '03109438554', 'Hi, I would like to open a franchise of Scoop Nation in Kasur.', 'partnership', 'new', '::1', 'PostmanRuntime/7.47.1', '', '2025-09-24 18:23:39', '2025-09-24 18:23:39'),
(4, 1, 1, 'Waqas Mahmmood', 'wmkhan101@gmail.com', '03109438554', 'Hi, I would like to open a franchise of Scoop Nation in Kasur.', 'partnership', 'new', '::1', 'PostmanRuntime/7.47.1', '', '2025-09-24 19:06:30', '2025-09-24 19:06:30');

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
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `post_type` enum('post','attachment','banner') NOT NULL,
  `post_status` enum('draft','published','archived') DEFAULT 'draft',
  `post_parent` bigint(20) UNSIGNED DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `business_id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mime_type` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `post_type`, `post_status`, `post_parent`, `file_path`, `author_id`, `business_id`, `branch_id`, `created_at`, `updated_at`, `mime_type`) VALUES
(4, 'Best Blinds for Small Windows', 'best-blinds-for-small-windows-1', '<p>Perfect solutions for tiny spaces.</p>', 'post', 'archived', NULL, NULL, 1, 0, 0, '2025-03-04 09:00:00', '2025-03-15 17:03:16', ''),
(17, 'Updated Post1', 'updated-post1-1', '<p>Updated <em>content</em> with <style>p { color: red; }</style></p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-17 07:00:00', '2025-03-17 13:42:22', ''),
(19, 'Updated Post2', 'updated-post2', '<p><br></p><p>Updated <em>content</em> with <style>p { color: red; }</style></p><p><br></p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-19 04:00:00', '2025-03-17 13:42:38', ''),
(24, 'Small Window Blinds', NULL, 'Blinds on a small window', 'attachment', 'published', 4, 'https://images.unsplash.com/photo-1600585154526-990dced4db0d', 1, 0, 0, '2025-03-04 09:05:00', '2025-03-04 09:05:00', ''),
(37, 'Seasonal Blinds', NULL, 'Seasonal blind decor', 'attachment', 'published', 17, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c', 2, 0, 0, '2025-03-17 07:05:00', '2025-03-17 07:05:00', ''),
(39, 'Coastal Blinds', NULL, 'Blinds for coastal homes', 'attachment', 'published', 19, 'https://images.unsplash.com/photo-1600585152915-d208bec867a1', 1, 0, 0, '2025-03-19 04:05:00', '2025-03-19 04:05:00', ''),
(44, '67d377dd02f61_WhatsApp Image 2022-11-09 at 17.44.22.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d377dd02f61_WhatsApp Image 2022-11-09 at 17.44.22.jpg', 1, 0, 0, '2025-03-14 00:27:09', '2025-03-14 00:27:09', ''),
(45, '67d3783e27f7b_WhatsApp Image 2022-11-09 at 17.44.22.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d3783e27f7b_WhatsApp Image 2022-11-09 at 17.44.22.jpg', 1, 0, 0, '2025-03-14 00:28:46', '2025-03-14 00:28:46', ''),
(46, '67d3ad4d552ae_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d3ad4d552ae_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-14 04:15:09', '2025-03-14 04:15:09', ''),
(54, '67d46c691c89a_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d46c691c89a_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-14 17:50:33', '2025-03-14 17:50:33', ''),
(55, '67d57422d58ae_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d57422d58ae_services-location-map.png', 1, 0, 0, '2025-03-15 12:35:46', '2025-03-15 12:35:46', ''),
(56, '67d574397eab9_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d574397eab9_services-location-map.png', 1, 0, 0, '2025-03-15 12:36:09', '2025-03-15 12:36:09', ''),
(57, '67d576993075c_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d576993075c_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 12:46:17', '2025-03-15 12:46:17', ''),
(58, '67d5770b6c367_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5770b6c367_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 12:48:11', '2025-03-15 12:48:11', ''),
(59, '67d57852287b3_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d57852287b3_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 12:53:38', '2025-03-15 12:53:38', ''),
(60, '67d5785706d0f_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5785706d0f_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 12:53:43', '2025-03-15 12:53:43', ''),
(61, '67d5787cba09e_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5787cba09e_services-location-map.png', 1, 0, 0, '2025-03-15 12:54:20', '2025-03-15 12:54:20', ''),
(62, '67d57a226c958_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d57a226c958_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:01:22', '2025-03-15 13:01:22', ''),
(63, '67d57a2796d8b_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d57a2796d8b_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:01:27', '2025-03-15 13:01:27', ''),
(64, '67d57f46d9cbe_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d57f46d9cbe_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:23:18', '2025-03-15 13:23:18', ''),
(65, '67d580728a25b_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d580728a25b_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:28:18', '2025-03-15 13:28:18', ''),
(66, '67d583b9ae2fd_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d583b9ae2fd_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:42:17', '2025-03-15 13:42:17', ''),
(67, '67d583c0dedde_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d583c0dedde_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:42:24', '2025-03-15 13:42:24', ''),
(68, '67d583f6d4f92_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d583f6d4f92_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 13:43:18', '2025-03-15 13:43:18', ''),
(77, '67d59f7e7e6d0_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d59f7e7e6d0_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 15:40:46', '2025-03-15 15:40:46', ''),
(87, '67d5e691a80ea_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5e691a80ea_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 20:44:01', '2025-03-15 20:44:01', ''),
(88, '67d5e6fd7b156_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5e6fd7b156_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 20:45:49', '2025-03-15 20:45:49', ''),
(89, '67d5e765a8886_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5e765a8886_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 20:47:33', '2025-03-15 20:47:33', ''),
(92, '67d5e85de8a0a_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5e85de8a0a_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-15 20:51:41', '2025-03-15 20:51:41', ''),
(93, '67d5e8dfd7df9_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5e8dfd7df9_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 20:53:51', '2025-03-15 20:53:51', ''),
(95, 'Updated Post', 'updated-post-1', '<p>Updated <em>content</em> with lOrem<style>p { color: red; }</style></p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-15 15:58:08', '2025-03-17 13:41:52', ''),
(97, '67d5eb34a294e_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5eb34a294e_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 21:03:48', '2025-03-15 21:03:48', ''),
(98, '67d5ec151201f_dF6U8mKTofwXnUdT-generated_image.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5ec151201f_dF6U8mKTofwXnUdT-generated_image.jpg', 1, 0, 0, '2025-03-15 21:07:33', '2025-03-15 21:07:33', ''),
(99, '67d5ecb31b9b7_dF6U8mKTofwXnUdT-generated_image.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5ecb31b9b7_dF6U8mKTofwXnUdT-generated_image.jpg', 1, 0, 0, '2025-03-15 21:10:11', '2025-03-15 21:10:11', ''),
(100, '67d5ed6178d42_dF6U8mKTofwXnUdT-generated_image.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5ed6178d42_dF6U8mKTofwXnUdT-generated_image.jpg', 1, 0, 0, '2025-03-15 21:13:05', '2025-03-15 21:13:05', ''),
(101, '67d5edd346db6_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5edd346db6_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 21:14:59', '2025-03-15 21:14:59', ''),
(102, '67d5f15bb1496_dF6U8mKTofwXnUdT-generated_image.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5f15bb1496_dF6U8mKTofwXnUdT-generated_image.jpg', 1, 0, 0, '2025-03-15 21:30:03', '2025-03-15 21:30:03', ''),
(103, '67d5f2a765169_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5f2a765169_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-15 21:35:35', '2025-03-15 21:35:35', ''),
(104, '67d5f59cbc5b1_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5f59cbc5b1_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 21:48:12', '2025-03-15 21:48:12', ''),
(105, '67d5f915e8195_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5f915e8195_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 22:03:01', '2025-03-15 22:03:01', ''),
(106, '67d5fa5246c29_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5fa5246c29_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 22:08:18', '2025-03-15 22:08:18', ''),
(107, '67d5fa5988008_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d5fa5988008_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-15 22:08:25', '2025-03-15 22:08:25', ''),
(119, '67d6f34bdadba_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f34bdadba_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 15:50:35', '2025-03-16 15:50:35', ''),
(122, 'TESt', 'test-2', '<p>This is the content</p>', 'post', 'archived', NULL, NULL, 1, 0, 0, '2025-03-16 10:51:44', '2025-03-16 15:51:44', ''),
(123, '67d6f3ad3375f_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f3ad3375f_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 15:52:13', '2025-03-16 15:52:13', ''),
(127, '67d6f4be8b659_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f4be8b659_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-16 15:56:46', '2025-03-16 15:56:46', ''),
(128, '67d6f52611629_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f52611629_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 15:58:30', '2025-03-16 15:58:30', ''),
(129, '67d6f53866402_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f53866402_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 15:58:48', '2025-03-16 15:58:48', ''),
(130, '67d6f57721845_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f57721845_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 15:59:51', '2025-03-16 15:59:51', ''),
(131, '67d6f580ec440_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f580ec440_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:00:00', '2025-03-16 16:00:00', ''),
(132, '67d6f5e334760_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f5e334760_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:01:39', '2025-03-16 16:01:39', ''),
(133, '67d6f5ecbcb43_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f5ecbcb43_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:01:48', '2025-03-16 16:01:48', ''),
(134, '67d6f5f2f218b_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f5f2f218b_services-location-map.png', 1, 0, 0, '2025-03-16 16:01:54', '2025-03-16 16:01:54', ''),
(135, '67d6f60dd63e3_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f60dd63e3_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:02:21', '2025-03-16 16:02:21', ''),
(136, '67d6f7861b297_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6f7861b297_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:08:38', '2025-03-16 16:08:38', ''),
(137, '67d6fa28959a5_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6fa28959a5_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:19:52', '2025-03-16 16:19:52', ''),
(138, 'Test', 'test', '<p>Test</p>', 'post', 'archived', NULL, NULL, 1, 0, 0, '2025-03-16 11:19:56', '2025-03-16 16:19:56', ''),
(139, 'Test', 'test-1', '<p>Test</p>', 'post', 'archived', NULL, NULL, 1, 0, 0, '2025-03-16 11:20:04', '2025-03-16 16:20:04', ''),
(140, 'Test', 'test-3', '<p>Test</p>', 'post', 'archived', NULL, NULL, 1, 0, 0, '2025-03-16 11:20:18', '2025-03-16 16:20:18', ''),
(141, 'Test', 'test-4', '<p>Test</p>', 'post', 'archived', NULL, NULL, 1, 0, 0, '2025-03-16 11:20:56', '2025-03-16 16:20:56', ''),
(142, '67d6fa83098d9_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d6fa83098d9_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg', 1, 0, 0, '2025-03-16 16:21:23', '2025-03-16 16:21:23', ''),
(143, 'test', 'test-5', '<p>test</p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-16 11:21:30', '2025-03-16 16:21:30', ''),
(150, '67d70927b747f_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d70927b747f_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-16 17:23:51', '2025-03-16 17:23:51', ''),
(151, '67d7092f8be8f_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d7092f8be8f_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-16 17:23:59', '2025-03-16 17:23:59', ''),
(154, '67d82115689a7_background.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d82115689a7_background.jpg', 1, 0, 0, '2025-03-17 13:18:13', '2025-03-17 13:18:13', ''),
(155, '67d82128342b4_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d82128342b4_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:18:32', '2025-03-17 13:18:32', ''),
(156, '67d8247ebeff1_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8247ebeff1_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:32:46', '2025-03-17 13:32:46', ''),
(157, '67d824ceca0e6_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d824ceca0e6_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:34:06', '2025-03-17 13:34:06', ''),
(158, '67d825dc144e9_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d825dc144e9_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:38:36', '2025-03-17 13:38:36', ''),
(159, '67d825fbb6f0f_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d825fbb6f0f_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:39:07', '2025-03-17 13:39:07', ''),
(160, '67d8268598c86_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8268598c86_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:41:25', '2025-03-17 13:41:25', ''),
(161, '67d826e9de4bd_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d826e9de4bd_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:43:05', '2025-03-17 13:43:05', ''),
(162, '67d82755073d3_1584_1_90594.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d82755073d3_1584_1_90594.jpg', 1, 0, 0, '2025-03-17 13:44:53', '2025-03-17 13:44:53', ''),
(163, '67d8277062df7_background.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8277062df7_background.jpg', 1, 0, 0, '2025-03-17 13:45:20', '2025-03-17 13:45:20', ''),
(164, '67d82787adb5c_1666551058771D6FC0D0B-1F87-4A9A-9334-9CADD0E2C81F.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d82787adb5c_1666551058771D6FC0D0B-1F87-4A9A-9334-9CADD0E2C81F.jpg', 1, 0, 0, '2025-03-17 13:45:43', '2025-03-17 13:45:43', ''),
(165, 'New Post from Waqas', 'new-post-from-waqas-1', '<div style=\"color: blue;\">Hello <strong>World</strong><script>alert(\'test\');</script></div>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-17 09:27:01', '2025-03-17 13:41:11', ''),
(166, '67d86cc0914e9_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d86cc0914e9_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-17 18:41:04', '2025-03-17 18:41:04', ''),
(167, '67d86cde07562_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d86cde07562_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-17 18:41:34', '2025-03-17 18:41:34', ''),
(168, '67d8ae2cacc1f_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8ae2cacc1f_background.png', 1, 0, 0, '2025-03-17 23:20:12', '2025-03-17 23:20:12', ''),
(169, '67d8ae4688789_cover.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8ae4688789_cover.png', 1, 0, 0, '2025-03-17 23:20:38', '2025-03-17 23:20:38', ''),
(170, '67d8ae5093523_cover.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8ae5093523_cover.png', 1, 0, 0, '2025-03-17 23:20:48', '2025-03-17 23:20:48', ''),
(171, '67d8ae58321da_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8ae58321da_background.png', 1, 0, 0, '2025-03-17 23:20:56', '2025-03-17 23:20:56', ''),
(172, 'Benefit from our exclusive offer', NULL, 'Ends in 3 days', 'banner', 'draft', NULL, NULL, 1, 0, 0, '2025-03-17 18:22:01', '2025-03-21 19:50:32', ''),
(176, '67d8afff39c60_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8afff39c60_background.png', 1, 0, 0, '2025-03-17 23:27:59', '2025-03-17 23:27:59', ''),
(177, 'How Smart Blinds Can Transform Your Home', 'how-smart-blinds-can-transform-your-home-1', '<p><img src=\"http://www.techsolutions.com\" alt=\"\" width=\"300\">Zebra blinds (also known as dual-layer or day-and-night blinds) are a versatile window treatment option that has gained popularity in recent years. These distinctive blinds feature alternating horizontal bands of solid and sheer fabric on a continuous loop. Here\'s a comprehensive overview:</p>\n<h2><strong><a href=\"https://www.coolmax.com.pk/\">How Zebra Blinds Work</a></strong></h2>\n<p>When adjusted, the solid and sheer layers align in different ways to control light and privacy:</p>\n\n<p>When the solid bands align with solid bands, the blinds provide complete privacy and light blockage</p>\n<p>When sheer bands align with sheer bands, they filter light while maintaining visibility</p><ul>\n<li>When solid and sheer bands overlap, they create a zebra-like striped pattern (hence the name) and provide partial light filtering</li>\n</ul>\n<h2>Benefits of Zebra Blinds</h2>\n<ol>\n<li><strong>Light Control</strong>: Offer precise control over the amount of light entering a room</li>\n<li><strong>Privacy Options</strong>: Provide multiple levels of privacy from complete to partial</li>\n<li><strong>Modern Aesthetic</strong>: Clean lines and contemporary design complement modern interiors</li>\n<li><strong>Space-Efficient</strong>: Roll up compactly when not in use</li>\n<li><strong>Versatility</strong>: Function similar to both blinds and shades in one product</li>\n</ol>\n<h2>Design Considerations</h2>\n<ul>\n<li>Available in various fabric opacities from light-filtering to room-darkening</li>\n<li>Come in a wide range of colors and textures to match different decor styles</li>\n<li>Can be motorized for convenient operation</li>\n<li>Typically installed inside or outside the window frame</li>\n</ul>\n<h2>Best Applications</h2>\n<p>Zebra blinds work particularly well in:</p>\n<ul>\n<li>Living rooms where light control is important throughout the day</li>\n<li>Home offices requiring glare reduction while maintaining some natural light</li>\n<li>Modern bedrooms seeking a sleek alternative to traditional blinds</li>\n<li>Spaces where privacy needs vary throughout the day</li>\n</ul>\n<p>Would you like more specific information about installation, pricing, or how to choose the right zebra blinds for particular spaces?</p>', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-03-17 18:29:53', '2025-05-10 14:53:38', ''),
(178, '67d8b2f69edc8_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d8b2f69edc8_background.png', 1, 0, 0, '2025-03-17 23:40:38', '2025-03-17 23:40:38', ''),
(185, '67d963d427d9e_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d963d427d9e_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-18 12:15:16', '2025-03-18 12:15:16', ''),
(186, '67d964ba70b70_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67d964ba70b70_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-18 12:19:06', '2025-03-18 12:19:06', ''),
(191, '67daf8f4e65ef_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67daf8f4e65ef_services-location-map.png', 1, 0, 0, '2025-03-19 17:03:48', '2025-03-19 17:03:48', ''),
(196, '67db1612f4187_IMG_0871.JPG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67db1612f4187_IMG_0871.JPG', 1, 0, 0, '2025-03-19 19:08:03', '2025-03-19 19:08:03', ''),
(197, 'The Ultimate Guide to Choosing the Perfect Blinds for Your Home', 'the-ultimate-guide-to-choosing-the-perfect-blinds-for-your-home', '<p data-pm-slice=\"1 1 []\"><span>Blinds are an essential part of home decor, offering both style and functionality. Whether you want to control the amount of natural light, enhance privacy, or add a modern touch to your interior, the right blinds can transform any space. In this guide, we’ll help you navigate the different types of blinds and how to choose the perfect one for your home.</span><span><br></span></p><h3><span><strong>1. Types of Blinds</strong></span></h3><p><span>Blinds come in various styles, materials, and functionalities. Here are some of the most popular types:</span></p><ul data-spread=\"false\"><li><p><span><strong>Venetian Blinds</strong></span><span>: These classic horizontal slats allow precise light control and are available in materials like wood, aluminum, and PVC.</span></p></li><li><p><span><strong>Roller Blinds</strong></span><span>: A sleek and minimalistic option, roller blinds are easy to use and come in a variety of colors and patterns.</span></p></li><li><p><span><strong>Roman Blinds</strong></span><span>: Made of fabric, Roman blinds fold up neatly when raised, offering a soft and elegant appearance.</span></p></li><li><p><span><strong>Vertical Blinds</strong></span><span>: Ideal for large windows and sliding doors, vertical blinds provide excellent light control and a contemporary look.</span></p></li><li><p><span><strong>Smart Blinds</strong></span><span>: The latest innovation in window treatments, smart blinds can be controlled via remote or smartphone for convenience and energy efficiency.</span></p></li><li><p><span><img src=\"https://lahorewindowblinds.pk/wp-content/uploads/2021/03/shangrila-blind.jpeg\" alt=\"\" width=\"300\"></span></p></li></ul><h3><span><strong>2. Factors to Consider When Choosing Blinds</strong></span></h3><p><span>When selecting blinds for your home, keep the following factors in mind:</span></p><ul data-spread=\"false\"><li><p><span><strong>Light Control &amp; Privacy</strong></span><span>: Consider how much sunlight you want to filter. Sheer blinds allow soft light, while blackout blinds provide complete darkness.</span></p></li><li><p><span><strong>Material &amp; Durability</strong></span><span>: If you\'re looking for moisture-resistant options for kitchens or bathrooms, PVC or aluminum blinds are great choices.</span></p></li><li><p><span><strong>Style &amp; Aesthetic</strong></span><span>: Match the blinds with your home’s decor for a cohesive look. Wooden blinds add warmth, while sleek metal blinds create a modern feel.</span></p></li><li><p><span><strong>Ease of Maintenance</strong></span><span>: Some blinds require regular dusting, while others are easier to clean with a damp cloth.</span></p></li><li><p><span><strong>Budget</strong></span><span>: Blinds come in a wide price range, so set a budget before exploring options.</span></p></li><li><p><br></p><span><strong>3. Benefits of Installing Blinds</strong></span></li></ul><p><span>Investing in quality blinds can provide several advantages:</span></p><ul data-spread=\"false\"><li><p><span><strong>Energy Efficiency</strong></span><span>: Certain blinds, such as cellular shades, help insulate your home, reducing heating and cooling costs.</span></p></li><li><p><span><strong>Improved Privacy</strong></span><span>: Blinds allow you to adjust visibility, ensuring your home stays private from prying eyes.</span></p></li><li><p><span><strong>Enhanced Aesthetic Appeal</strong></span><span>: The right blinds can add sophistication and complement your existing decor.</span></p></li><li><p><span><strong>Convenience</strong></span><span>: With options like motorized blinds, you can effortlessly adjust your window coverings with the push of a button.</span></p></li><li><p><span><br></span></p></li></ul><h3><span><strong>4. How to Maintain Your Blinds</strong></span></h3><p><span>To keep your blinds looking new and functional, follow these maintenance tips:</span></p><p><br></p><ol style=\"list-style-type: lower-alpha;\"><li><span>Dust regularly with a microfiber cloth or a vacuum brush attachment.</span></li></ol><p><br></p><p><br></p><ol style=\"list-style-type: lower-alpha;\"><li><span>Clean fabric blinds with a mild detergent and water.</span></li></ol><p><br></p><p><br></p><ol style=\"list-style-type: lower-alpha;\"><li><span>Avoid using harsh chemicals on wooden blinds to prevent damage.</span></li></ol><p><br></p><p><br></p><ol style=\"list-style-type: lower-alpha;\"><li><span>Check and repair cords or mechanisms periodically to ensure smooth operation.</span></li></ol><p><br></p><p><br></p><p><br></p><h3><span><strong>Final Thoughts</strong></span></h3><p><span>Choosing the perfect blinds for your home involves balancing style, functionality, and budget. Whether you prefer the timeless appeal of Venetian blinds or the modern convenience of smart blinds, there’s an option to suit every space. Explore different styles and materials to find the best fit for your needs, and enjoy the comfort and elegance that well-chosen blinds can bring to your home!</span></p><p><br></p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-19 14:09:16', '2025-03-21 01:52:32', ''),
(198, '67db16ab9a4e0_IMG_0876.JPG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67db16ab9a4e0_IMG_0876.JPG', 1, 0, 0, '2025-03-19 19:10:35', '2025-03-19 19:10:35', ''),
(199, '67db16b5f0988_image-20250307-175214.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67db16b5f0988_image-20250307-175214.png', 1, 0, 0, '2025-03-19 19:10:45', '2025-03-19 19:10:45', ''),
(202, '67dbd98bd3033_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dbd98bd3033_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-20 09:02:03', '2025-03-20 09:02:03', ''),
(213, '67dbe63069764_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dbe63069764_services-location-map.png', 1, 0, 0, '2025-03-20 09:56:00', '2025-03-20 09:56:00', ''),
(216, '67dc3d659aa5a_image-20250307-175214.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dc3d659aa5a_image-20250307-175214.png', 1, 0, 0, '2025-03-20 16:08:05', '2025-03-20 16:08:05', ''),
(217, 'TEST', 'test-7', '<p>TEST</p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-20 11:08:14', '2025-03-20 11:08:14', 'text/html'),
(218, '67dccccf499e8_image-20250307-175214.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dccccf499e8_image-20250307-175214.png', 1, 0, 0, '2025-03-21 02:19:59', '2025-03-21 02:19:59', ''),
(219, 'test', 'test-8', '<p><span style=\"font-family: Impact, Charcoal, sans-serif;\">test</span></p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-20 21:20:12', '2025-03-20 21:20:12', 'text/html'),
(223, '67dceca269cd9_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dceca269cd9_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-21 04:35:46', '2025-03-21 04:35:46', ''),
(224, '67dcecc3cb2e0_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcecc3cb2e0_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-21 04:36:19', '2025-03-21 04:36:19', ''),
(225, 'test2', 'test2', '<p>test</p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-20 23:36:26', '2025-03-20 23:36:26', 'text/html'),
(226, '67dced0f5a484_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dced0f5a484_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-21 04:37:35', '2025-03-21 04:37:35', ''),
(228, '67dcf0115209c_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf0115209c_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-21 04:50:25', '2025-03-21 04:50:25', ''),
(229, '67dcf1be0a2eb_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf1be0a2eb_services-location-map.png', 1, 0, 0, '2025-03-21 04:57:34', '2025-03-21 04:57:34', ''),
(230, '67dcf2375dd01_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf2375dd01_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 04:59:35', '2025-03-21 04:59:35', ''),
(231, '67dcf267a1d75_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf267a1d75_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-21 05:00:23', '2025-03-21 05:00:23', ''),
(232, 'test', 'test-10', '<figure class=\"image\"><img style=\"aspect-ratio:548/667;\" src=\"https://itelc.org/metblind_api/uploads/67dcf2375dd01_videouploaddropzondebranch.PNG\" width=\"548\" height=\"667\"></figure><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>fdaifhdiafhdi&nbsp;</p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-21 00:00:53', '2025-03-21 00:00:53', 'text/html'),
(233, 'Roller Blind Zebra Blinds - Shop Now', 'roller-blind-zebra-blinds-shop-now-1', '<div><h3><span style=\"font-family: Georgia, Palatino, serif; font-size: 34px;\">Roller Blind Zebra Blinds - Shop Now</span></h3>\n<p class=\"break-words\" style=\"white-space: pre-wrap;\">Zebra blinds are modern window coverings that combine the functionality of roller blinds with the aesthetic appeal of sheer curtains. These innovative blinds offer a stylish and versatile solution for any home or office, providing both light control and privacy with ease.</p><p class=\"break-words\" style=\"white-space: pre-wrap;\"><br></p><p class=\"break-words\" style=\"white-space: pre-wrap;\"><img src=\"https://www.melrosewindowcoverings.com/cdn/shop/products/Untitleddesign-18_1024x1024@2x.png?v=1632864349\" alt=\"Zebra Blind\" width=\"444\" height=\"444\" style=\"display: block; margin-left: auto; margin-right: auto;\"></p><p class=\"break-words\" style=\"white-space: pre-wrap;\"><br></p>\n<h4>5 Benefits of Zebra Blinds (Summary Points)</h4>\n<ul class=\"marker:text-secondary\">\n<li class=\"break-words\"><strong>Enhanced Light Control:</strong> Adjust light levels effortlessly with alternating sheer and solid fabric stripes.</li>\n<li class=\"break-words\"><strong>Privacy Made Simple:</strong> Block visibility from outside while maintaining a soft, natural glow indoors.</li>\n<li class=\"break-words\"><strong>Stylish Design:</strong> Add a contemporary touch to any space with their sleek, dual-layered look.</li>\n<li class=\"break-words\"><strong>Easy Operation:</strong> Smoothly raise, lower, or adjust with minimal effort for daily convenience.</li>\n<li class=\"break-words\"><strong>Versatile Fit:</strong> Perfect for any room, from living spaces to offices, with customizable options.</li>\n</ul>\n<h4>5 Health Benefits of Zebra Blinds (Detail Points)</h4>\n<ul class=\"marker:text-secondary\">\n<li class=\"break-words\"><strong>Reduced Eye Strain:</strong> Filter harsh sunlight to prevent glare, protecting your eyes during work or relaxation.</li>\n<li class=\"break-words\"><strong>Improved Sleep Quality:</strong> Block out light effectively at night, promoting better rest and circadian rhythm balance.</li>\n<li class=\"break-words\"><strong>Allergy-Friendly:</strong> Made from durable, dust-resistant materials that minimize allergen buildup compared to traditional curtains.</li>\n<li class=\"break-words\"><strong>UV Protection:</strong> Shield your skin and eyes from harmful ultraviolet rays by diffusing sunlight.</li>\n<li class=\"break-words\"><strong>Stress Reduction:</strong> Create a calm, comfortable environment with customizable light and privacy settings.</li></ul></div>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-21 00:10:21', '2025-03-21 12:02:03', 'text/html'),
(234, '67dcf590a96ba_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf590a96ba_search.PNG', 1, 0, 0, '2025-03-21 05:13:52', '2025-03-21 05:13:52', ''),
(235, '67dcf5df13d7f_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf5df13d7f_search.PNG', 1, 0, 0, '2025-03-21 05:15:11', '2025-03-21 05:15:11', ''),
(236, '67dcf60fa86c9_customerappointment modal datatable.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf60fa86c9_customerappointment modal datatable.PNG', 1, 0, 0, '2025-03-21 05:15:59', '2025-03-21 05:15:59', ''),
(237, '67dcf7297ec2a_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcf7297ec2a_search.PNG', 1, 0, 0, '2025-03-21 05:20:41', '2025-03-21 05:20:41', ''),
(238, '67dcfbae092cc_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcfbae092cc_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 05:39:58', '2025-03-21 05:39:58', ''),
(239, 'test44', 'test44', '<p>Test</p>', 'post', 'draft', NULL, NULL, 1, 0, 0, '2025-03-21 00:40:01', '2025-03-21 00:40:01', 'text/html'),
(240, '67dcfbde9306e_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dcfbde9306e_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 05:40:46', '2025-03-21 05:40:46', ''),
(247, '67dd0ca55e32b_ftp.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd0ca55e32b_ftp.png', 1, 0, 0, '2025-03-21 06:52:21', '2025-03-21 06:52:21', ''),
(248, '67dd53301dcd2_services-location-map.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd53301dcd2_services-location-map.png', 1, 0, 0, '2025-03-21 11:53:20', '2025-03-21 11:53:20', ''),
(249, '67dd5567e4cda_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd5567e4cda_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 12:02:47', '2025-03-21 12:02:47', ''),
(250, '67dd667a98749_image-20250307-175214.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd667a98749_image-20250307-175214.png', 1, 0, 0, '2025-03-21 13:15:38', '2025-03-21 13:15:38', ''),
(252, '67dd66a199c48_image-20250307-175214.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd66a199c48_image-20250307-175214.png', 1, 0, 0, '2025-03-21 13:16:17', '2025-03-21 13:16:17', ''),
(272, '67dd8cf415974_image-20250307-175214.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd8cf415974_image-20250307-175214.png', 1, 0, 0, '2025-03-21 15:59:48', '2025-03-21 15:59:48', ''),
(274, '67dd966012a2f_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd966012a2f_background.png', 1, 0, 0, '2025-03-21 16:40:00', '2025-03-21 16:40:00', ''),
(275, '67dd9bddeb1d4_WhatsApp Image 2025-03-21 at 10.03.01 PM.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd9bddeb1d4_WhatsApp Image 2025-03-21 at 10.03.01 PM.jpeg', 1, 0, 0, '2025-03-21 17:03:25', '2025-03-21 17:03:25', ''),
(276, '67dd9c90675a1_WhatsApp Image 2025-03-21 at 10.03.08 PM.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dd9c90675a1_WhatsApp Image 2025-03-21 at 10.03.08 PM.jpeg', 1, 0, 0, '2025-03-21 17:06:24', '2025-03-21 17:06:24', ''),
(277, '67dda6d43994d_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda6d43994d_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 17:50:12', '2025-03-21 17:50:12', ''),
(278, '67dda7992d2a9_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda7992d2a9_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 17:53:29', '2025-03-21 17:53:29', ''),
(279, '67dda7a223bec_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda7a223bec_search.PNG', 1, 0, 0, '2025-03-21 17:53:38', '2025-03-21 17:53:38', ''),
(280, '67dda8408bed5_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda8408bed5_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 17:56:16', '2025-03-21 17:56:16', ''),
(281, '67dda85c101dc_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda85c101dc_search.PNG', 1, 0, 0, '2025-03-21 17:56:44', '2025-03-21 17:56:44', ''),
(282, '67dda8c96bd77_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda8c96bd77_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 17:58:33', '2025-03-21 17:58:33', ''),
(283, '67dda8cd83331_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda8cd83331_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 17:58:37', '2025-03-21 17:58:37', ''),
(284, '67dda92dad22d_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda92dad22d_search.PNG', 1, 0, 0, '2025-03-21 18:00:13', '2025-03-21 18:00:13', ''),
(285, '67dda9398077a_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda9398077a_search.PNG', 1, 0, 0, '2025-03-21 18:00:25', '2025-03-21 18:00:25', ''),
(286, '67dda94e9b88f_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda94e9b88f_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:00:46', '2025-03-21 18:00:46', ''),
(287, '67dda990e0e92_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda990e0e92_search.PNG', 1, 0, 0, '2025-03-21 18:01:52', '2025-03-21 18:01:52', ''),
(288, '67dda9ef46291_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda9ef46291_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:03:27', '2025-03-21 18:03:27', ''),
(289, '67dda9fc38a50_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67dda9fc38a50_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:03:40', '2025-03-21 18:03:40', ''),
(290, '67ddaacddce27_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddaacddce27_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:07:09', '2025-03-21 18:07:09', ''),
(291, '67ddaadf63f1f_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddaadf63f1f_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:07:27', '2025-03-21 18:07:27', ''),
(292, '67ddab1b1b56b_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddab1b1b56b_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:08:27', '2025-03-21 18:08:27', ''),
(293, '67ddab8b212de_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddab8b212de_search.PNG', 1, 0, 0, '2025-03-21 18:10:19', '2025-03-21 18:10:19', ''),
(294, '67ddabb650e10_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddabb650e10_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:11:02', '2025-03-21 18:11:02', ''),
(295, '67ddabd10b15c_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddabd10b15c_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:11:29', '2025-03-21 18:11:29', ''),
(296, '67ddabe298c36_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddabe298c36_search.PNG', 1, 0, 0, '2025-03-21 18:11:46', '2025-03-21 18:11:46', ''),
(297, '67ddac08a3bf4_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddac08a3bf4_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:12:24', '2025-03-21 18:12:24', ''),
(298, '67ddac2f02538_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddac2f02538_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:13:03', '2025-03-21 18:13:03', ''),
(299, '67ddac43e4de6_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddac43e4de6_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:13:23', '2025-03-21 18:13:23', ''),
(300, '67ddac9bc25ab_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddac9bc25ab_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:14:51', '2025-03-21 18:14:51', ''),
(301, '67ddacfb71079_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddacfb71079_search.PNG', 1, 0, 0, '2025-03-21 18:16:27', '2025-03-21 18:16:27', ''),
(302, '67ddad24c2458_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddad24c2458_search.PNG', 1, 0, 0, '2025-03-21 18:17:08', '2025-03-21 18:17:08', ''),
(303, '67ddad3de0571_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddad3de0571_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:17:33', '2025-03-21 18:17:33', ''),
(304, '67ddad50d140c_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddad50d140c_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:17:52', '2025-03-21 18:17:52', ''),
(305, '67ddad68241fd_search.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddad68241fd_search.PNG', 1, 0, 0, '2025-03-21 18:18:16', '2025-03-21 18:18:16', ''),
(306, '67ddae0483bf1_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddae0483bf1_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:20:52', '2025-03-21 18:20:52', ''),
(307, '67ddae2c4d0b7_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddae2c4d0b7_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:21:32', '2025-03-21 18:21:32', ''),
(308, '67ddaea387e16_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddaea387e16_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:23:31', '2025-03-21 18:23:31', ''),
(309, '67ddb36a4c99e_videouploaddropzondebranch.PNG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67ddb36a4c99e_videouploaddropzondebranch.PNG', 1, 0, 0, '2025-03-21 18:43:54', '2025-03-21 18:43:54', ''),
(313, '67de099718b38_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67de099718b38_background.png', 1, 0, 0, '2025-03-22 00:51:35', '2025-03-22 00:51:35', ''),
(314, '67de099f9d4ed_cover.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67de099f9d4ed_cover.png', 1, 0, 0, '2025-03-22 00:51:43', '2025-03-22 00:51:43', ''),
(315, '67de09a4292d3_background.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67de09a4292d3_background.png', 1, 0, 0, '2025-03-22 00:51:48', '2025-03-22 00:51:48', ''),
(316, '30% OFF SHADES', NULL, 'WE MANUFACTURE BLINDS IN CALGARY FOR ALBERTA HOMES & BUSINESSES. a variety of window shades and treatments that give you 100% privacy and add a modern elegant touch to your home.', 'banner', 'published', NULL, NULL, 1, 0, 0, '2025-03-21 19:51:51', '2025-03-22 10:25:14', ''),
(317, '67de1e1ff007c_IMG_0871.JPG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67de1e1ff007c_IMG_0871.JPG', 1, 0, 0, '2025-03-22 02:19:12', '2025-03-22 02:19:12', '');
INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `post_type`, `post_status`, `post_parent`, `file_path`, `author_id`, `business_id`, `branch_id`, `created_at`, `updated_at`, `mime_type`) VALUES
(318, 'The Ultimate Guide to Choosing the Perfect Blinds for Your Home', 'the-ultimate-guide-to-choosing-the-perfect-blinds-for-your-home-1', '<p data-pm-slice=\"1 1 []\"><span><img src=\"https://www.youtube.com/embed/RQCiiriDrIA?si=Qw-HeW8fqmg_92tt\" alt=\"\" width=\"300\"><img src=\"https://metblinds-wackyapps-projects.vercel.app/admin/blogs/%3Ciframe%20width=%22560%22%20height=%22315%22%20src=%22https://www.youtube.com/embed/RQCiiriDrIA?si=Qw-HeW8fqmg_92tt%22%20title=%22YouTube%20video%20player%22%20frameborder=%220%22%20allow=%22accelerometer;%20autoplay;%20clipboard-write;%20encrypted-media;%20gyroscope;%20picture-in-picture;%20web-share%22%20referrerpolicy=%22strict-origin-when-cross-origin%22%20allowfullscreen%3E%3C/iframe%3E\" alt=\"\" width=\"300\"><img src=\"http://www.techsolutions.com/\" alt=\"\" width=\"300\"><img src=\"https://www.youtube.com/embed/RQCiiriDrIA?si=Qw-HeW8fqmg_92tt\" alt=\"\" width=\"300\">Blinds are an essential part of home decor, offering both style and functionality. Whether you want to control the amount of natural light, enhance privacy, or add a modern touch to your interior, the right blinds can transform any space. In this guide, we’ll help you navigate the different types of blinds and how to choose the perfect one for your home.</span></p><p data-pm-slice=\"1 1 []\"><span><img src=\"https://h2imports.ca/wp-content/uploads/2024/03/Zebra-blinds-1.webp\" width=\"551\" height=\"310\" style=\"border-radius: 10px;\"></span></p><p data-pm-slice=\"1 1 []\"><span><strong>1. Types of Blinds</strong></span></p><p><span>Blinds come in various styles, materials, and functionalities. Here are some of the most popular types:</span></p><ul data-spread=\"false\"><li><p><span><strong>Venetian Blinds</strong></span><span>: These classic horizontal slats allow precise light control and are available in materials like wood, aluminum, and PVC.</span></p></li><li><p><span><strong>Roller Blinds</strong></span><span>: A sleek and minimalistic option, roller blinds are easy to use and come in a variety of colors and patterns.</span></p></li><li><p><span><strong>Roman Blinds</strong></span><span>: Made of fabric, Roman blinds fold up neatly when raised, offering a soft and elegant appearance.</span></p></li><li><p><span><strong>Vertical Blinds</strong></span><span>: Ideal for large windows and sliding doors, vertical blinds provide excellent light control and a contemporary look.</span></p></li><li><p><span><strong>Smart Blinds</strong></span><span>: The latest innovation in window treatments, smart blinds can be controlled via remote or smartphone for convenience and energy efficiency.</span></p></li></ul><p><img src=\"https://blog.blinds-2go.co.uk/wp-content/uploads/Moreno-Oak-Roller-Blind.jpg\" alt=\"\" width=\"300\"></p><p><br></p><h3><span><strong>2. Factors to Consider When Choosing Blinds</strong></span></h3><p><span>When selecting blinds for your home, keep the following factors in mind:</span></p><ul data-spread=\"false\"><li><p><span><strong>Light Control &amp; Privacy</strong></span><span>: Consider how much sunlight you want to filter. Sheer blinds allow soft light, while blackout blinds provide complete darkness.</span></p></li><li><p><span><strong>Material &amp; Durability</strong></span><span>: If you\'re looking for moisture-resistant options for kitchens or bathrooms, PVC or aluminum blinds are great choices.</span></p></li><li><p><span><strong>Style &amp; Aesthetic</strong></span><span>: Match the blinds with your home’s decor for a cohesive look. Wooden blinds add warmth, while sleek metal blinds create a modern feel.</span></p></li><li><p><span><strong>Ease of Maintenance</strong></span><span>: Some blinds require regular dusting, while others are easier to clean with a damp cloth.</span></p></li><li><p><span><strong>Budget</strong></span><span>: Blinds come in a wide price range, so set a budget before exploring options.</span></p></li></ul><h3><span><strong>3. Benefits of Installing Blinds</strong></span></h3><p><span>Investing in quality blinds can provide several advantages:</span></p><ul data-spread=\"false\"><li><p><span><strong>Energy Efficiency</strong></span><span>: Certain blinds, such as cellular shades, help insulate your home, reducing heating and cooling costs.</span></p></li><li><p><span><strong>Improved Privacy</strong></span><span>: Blinds allow you to adjust visibility, ensuring your home stays private from prying eyes.</span></p></li><li><p><span><strong>Enhanced Aesthetic Appeal</strong></span><span>: The right blinds can add sophistication and complement your existing decor.</span></p></li><li><p><span><strong>Convenience</strong></span><span>: With options like motorized blinds, you can effortlessly adjust your window coverings with the push of a button.</span></p></li></ul><h3><span><strong>4. How to Maintain Your Blinds</strong></span></h3><p><span>To keep your blinds looking new and functional, follow these maintenance tips:</span></p><ul data-spread=\"false\"><li><p><span>Dust regularly with a microfiber cloth or a vacuum brush attachment.</span></p></li><li><p><span>Clean fabric blinds with a mild detergent and water.</span></p></li><li><p><span>Avoid using harsh chemicals on wooden blinds to prevent damage.</span></p></li><li><p><span>Check and repair cords or mechanisms periodically to ensure smooth operation.</span></p></li></ul><h3><span><strong>Final Thoughts</strong></span></h3><p><span>Choosing the perfect blinds for your home involves balancing style, functionality, and budget. Whether you prefer the timeless appeal of Venetian blinds or the modern convenience of smart blinds, there’s an option to suit every space. Explore different styles and materials to find the best fit for your needs, and enjoy the comfort and elegance that well-chosen blinds can bring to your home!</span></p><p><br></p>', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-03-21 21:19:21', '2025-05-10 14:51:55', 'text/html'),
(319, '67de1fbb42a29_totalshade-charcoal-54-blackout-pleated-a.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67de1fbb42a29_totalshade-charcoal-54-blackout-pleated-a.jpg', 1, 0, 0, '2025-03-22 02:26:03', '2025-03-22 02:26:03', ''),
(320, '67de1fea2f769_images.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/67de1fea2f769_images.jpeg', 1, 0, 0, '2025-03-22 02:26:50', '2025-03-22 02:26:50', ''),
(321, 'Buy 8 motors & get a smart hub free', NULL, 'Ends in 3 days', 'banner', 'published', NULL, NULL, 1, 0, 0, '2025-03-21 21:29:32', '2025-03-22 10:25:07', ''),
(323, '6818d5eb3ec9a_wallpaperflare.com_wallpaper (2).jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6818d5eb3ec9a_wallpaperflare.com_wallpaper (2).jpg', 1, 0, 0, '2025-05-05 15:14:51', '2025-05-05 15:14:51', ''),
(326, '6818ecbd960ef_peakpx (1).jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6818ecbd960ef_peakpx (1).jpg', 1, 0, 0, '2025-05-05 16:52:13', '2025-05-05 16:52:13', ''),
(329, '6819c75b0b31d_React Mern Full Stack.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6819c75b0b31d_React Mern Full Stack.jpg', 1, 0, 0, '2025-05-06 08:24:59', '2025-05-06 08:24:59', ''),
(331, '6819c80ab0814_wallpaperflare.com_wallpaper (1).jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6819c80ab0814_wallpaperflare.com_wallpaper (1).jpg', 1, 0, 0, '2025-05-06 08:27:54', '2025-05-06 08:27:54', ''),
(332, '6819ccada5a81_peakpx.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6819ccada5a81_peakpx.jpg', 1, 0, 0, '2025-05-06 08:47:41', '2025-05-06 08:47:41', ''),
(333, '6819ccb836b4c_React Mern Full Stack.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6819ccb836b4c_React Mern Full Stack.jpg', 1, 0, 0, '2025-05-06 08:47:52', '2025-05-06 08:47:52', ''),
(334, 'Avail New Offer', NULL, 'Molestias officiis u', 'banner', 'published', NULL, NULL, 2, 0, 0, '2025-05-06 03:48:00', '2025-05-06 09:35:05', ''),
(335, '681a1d27199d0_rollershadekf.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681a1d27199d0_rollershadekf.jpg', 1, 0, 0, '2025-05-06 14:31:03', '2025-05-06 14:31:03', ''),
(336, '681a1d462a4b0_draperykf.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681a1d462a4b0_draperykf.jpg', 1, 0, 0, '2025-05-06 14:31:34', '2025-05-06 14:31:34', ''),
(337, '681ca7a571f4d_award2.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681ca7a571f4d_award2.png', 1, 0, 0, '2025-05-08 12:46:29', '2025-05-08 12:46:29', ''),
(338, '681ca865212ec_award2.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681ca865212ec_award2.png', 1, 0, 0, '2025-05-08 12:49:41', '2025-05-08 12:49:41', ''),
(339, '681ca871a037b_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681ca871a037b_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', 1, 0, 0, '2025-05-08 12:49:53', '2025-05-08 12:49:53', ''),
(340, '681ca879ca87c_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681ca879ca87c_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', 1, 0, 0, '2025-05-08 12:50:01', '2025-05-08 12:50:01', ''),
(344, '681cab112346f_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681cab112346f_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', 1, 0, 0, '2025-05-08 13:01:05', '2025-05-08 13:01:05', ''),
(345, '681cab160ef90_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681cab160ef90_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg', 1, 0, 0, '2025-05-08 13:01:10', '2025-05-08 13:01:10', ''),
(349, '681e4a9e61118_peter.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681e4a9e61118_peter.png', 1, 0, 0, '2025-05-09 18:34:06', '2025-05-09 18:34:06', ''),
(351, '681e4e7e3a968_image 622.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681e4e7e3a968_image 622.png', 1, 0, 0, '2025-05-09 18:50:38', '2025-05-09 18:50:38', '');
INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `post_type`, `post_status`, `post_parent`, `file_path`, `author_id`, `business_id`, `branch_id`, `created_at`, `updated_at`, `mime_type`) VALUES
(354, 'The Rise of AI in Everyday Life: How It\'s Changing Our World', 'the-rise-of-ai-in-everyday-life-how-it-s-changing-our-world', '<h3 data-start=\"444\" data-end=\"463\"><span class=\"_fadeIn_m1hgl_8\">? </span><span class=\"_fadeIn_m1hgl_8\">Introduction</span></h3>\n<p data-start=\"465\" data-end=\"798\"><span class=\"_fadeIn_m1hgl_8\">We </span><span class=\"_fadeIn_m1hgl_8\">used </span><span class=\"_fadeIn_m1hgl_8\">to </span><span class=\"_fadeIn_m1hgl_8\">think </span><span class=\"_fadeIn_m1hgl_8\">of </span><span class=\"_fadeIn_m1hgl_8\">artificial </span><span class=\"_fadeIn_m1hgl_8\">intelligence (</span><span class=\"_fadeIn_m1hgl_8\">AI) </span><span class=\"_fadeIn_m1hgl_8\">as </span><span class=\"_fadeIn_m1hgl_8\">something </span><span class=\"_fadeIn_m1hgl_8\">you\'d </span><span class=\"_fadeIn_m1hgl_8\">find </span><span class=\"_fadeIn_m1hgl_8\">in </span><span class=\"_fadeIn_m1hgl_8\">sci-</span><span class=\"_fadeIn_m1hgl_8\">fi </span><span class=\"_fadeIn_m1hgl_8\">movies — </span><span class=\"_fadeIn_m1hgl_8\">advanced </span><span class=\"_fadeIn_m1hgl_8\">robots, </span><span class=\"_fadeIn_m1hgl_8\">space </span><span class=\"_fadeIn_m1hgl_8\">missions, </span><span class=\"_fadeIn_m1hgl_8\">or </span><span class=\"_fadeIn_m1hgl_8\">futuristic </span><span class=\"_fadeIn_m1hgl_8\">cities. </span><span class=\"_fadeIn_m1hgl_8\">But </span><span class=\"_fadeIn_m1hgl_8\">in </span><span class=\"_fadeIn_m1hgl_8\">2025, </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">is </span><span class=\"_fadeIn_m1hgl_8\">part </span><span class=\"_fadeIn_m1hgl_8\">of </span><span class=\"_fadeIn_m1hgl_8\">our </span><span class=\"_fadeIn_m1hgl_8\">daily </span><span class=\"_fadeIn_m1hgl_8\">routine. </span><span class=\"_fadeIn_m1hgl_8\">Whether </span><span class=\"_fadeIn_m1hgl_8\">you\'re </span><span class=\"_fadeIn_m1hgl_8\">asking </span><span class=\"_fadeIn_m1hgl_8\">Siri </span><span class=\"_fadeIn_m1hgl_8\">to </span><span class=\"_fadeIn_m1hgl_8\">play </span><span class=\"_fadeIn_m1hgl_8\">your </span><span class=\"_fadeIn_m1hgl_8\">favorite </span><span class=\"_fadeIn_m1hgl_8\">song </span><span class=\"_fadeIn_m1hgl_8\">or </span><span class=\"_fadeIn_m1hgl_8\">letting </span><span class=\"_fadeIn_m1hgl_8\">Netflix </span><span class=\"_fadeIn_m1hgl_8\">suggest </span><span class=\"_fadeIn_m1hgl_8\">your </span><span class=\"_fadeIn_m1hgl_8\">next </span><span class=\"_fadeIn_m1hgl_8\">binge, </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">is </span><span class=\"_fadeIn_m1hgl_8\">the </span><span class=\"_fadeIn_m1hgl_8\">silent </span><span class=\"_fadeIn_m1hgl_8\">helper </span><span class=\"_fadeIn_m1hgl_8\">behind </span><span class=\"_fadeIn_m1hgl_8\">the </span><span class=\"_fadeIn_m1hgl_8\">scenes.</span></p>\n<hr data-start=\"800\" data-end=\"803\">\n<h3 data-start=\"805\" data-end=\"822\"><span class=\"_fadeIn_m1hgl_8\">? </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">at </span><span class=\"_fadeIn_m1hgl_8\">Home</span></h3>\n<p data-start=\"824\" data-end=\"974\"><span class=\"_fadeIn_m1hgl_8\">From </span><span class=\"_fadeIn_m1hgl_8\">smart </span><span class=\"_fadeIn_m1hgl_8\">thermostats </span><span class=\"_fadeIn_m1hgl_8\">that </span><span class=\"_fadeIn_m1hgl_8\">learn </span><span class=\"_fadeIn_m1hgl_8\">your </span><span class=\"_fadeIn_m1hgl_8\">schedule </span><span class=\"_fadeIn_m1hgl_8\">to </span><span class=\"_fadeIn_m1hgl_8\">voice </span><span class=\"_fadeIn_m1hgl_8\">assistants </span><span class=\"_fadeIn_m1hgl_8\">like </span><span class=\"_fadeIn_m1hgl_8\">Alexa </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">Google </span><span class=\"_fadeIn_m1hgl_8\">Assistant, </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">is </span><span class=\"_fadeIn_m1hgl_8\">making </span><span class=\"_fadeIn_m1hgl_8\">our </span><span class=\"_fadeIn_m1hgl_8\">homes </span><span class=\"_fadeIn_m1hgl_8\">more </span><span class=\"_fadeIn_m1hgl_8\">convenient </span><span class=\"_fadeIn_m1hgl_8\">than </span><span class=\"_fadeIn_m1hgl_8\">ever.</span></p>\n\n<p><img src=\"data:image/jpeg;base64,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\" width=\"528\" style=\"\" height=\"296\"></p><hr data-start=\"1073\" data-end=\"1076\">\n<h3 data-start=\"1078\" data-end=\"1099\"><span class=\"_fadeIn_m1hgl_8\">? </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">on </span><span class=\"_fadeIn_m1hgl_8\">the </span><span class=\"_fadeIn_m1hgl_8\">Road</span></h3>\n<p data-start=\"1101\" data-end=\"1250\"><span class=\"_fadeIn_m1hgl_8\">Self-</span><span class=\"_fadeIn_m1hgl_8\">driving </span><span class=\"_fadeIn_m1hgl_8\">cars </span><span class=\"_fadeIn_m1hgl_8\">may </span><span class=\"_fadeIn_m1hgl_8\">not </span><span class=\"_fadeIn_m1hgl_8\">yet </span><span class=\"_fadeIn_m1hgl_8\">be </span><span class=\"_fadeIn_m1hgl_8\">mainstream, </span><span class=\"_fadeIn_m1hgl_8\">but </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">already </span><span class=\"_fadeIn_m1hgl_8\">helps </span><span class=\"_fadeIn_m1hgl_8\">power </span><span class=\"_fadeIn_m1hgl_8\">features </span><span class=\"_fadeIn_m1hgl_8\">like </span><span class=\"_fadeIn_m1hgl_8\">lane </span><span class=\"_fadeIn_m1hgl_8\">detection, </span><span class=\"_fadeIn_m1hgl_8\">adaptive </span><span class=\"_fadeIn_m1hgl_8\">cruise </span><span class=\"_fadeIn_m1hgl_8\">control, </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">emergency </span><span class=\"_fadeIn_m1hgl_8\">braking.</span></p>\n<h3 data-start=\"1395\" data-end=\"1412\"><span class=\"_fadeIn_m1hgl_8\"><img src=\"data:image/jpeg;base64,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\" alt=\"\" width=\"300\"></span></h3><h3 data-start=\"1395\" data-end=\"1412\"><span class=\"_fadeIn_m1hgl_8\">? </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">at </span><span class=\"_fadeIn_m1hgl_8\">Work</span></h3>\n<p data-start=\"1414\" data-end=\"1597\"><span class=\"_fadeIn_m1hgl_8\">Businesses </span><span class=\"_fadeIn_m1hgl_8\">use </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">for </span><span class=\"_fadeIn_m1hgl_8\">customer </span><span class=\"_fadeIn_m1hgl_8\">service </span><span class=\"_fadeIn_m1hgl_8\">chatbots, </span><span class=\"_fadeIn_m1hgl_8\">data </span><span class=\"_fadeIn_m1hgl_8\">analysis, </span><span class=\"_fadeIn_m1hgl_8\">fraud </span><span class=\"_fadeIn_m1hgl_8\">detection, </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">even </span><span class=\"_fadeIn_m1hgl_8\">hiring </span><span class=\"_fadeIn_m1hgl_8\">decisions. </span><span class=\"_fadeIn_m1hgl_8\">It’s </span><span class=\"_fadeIn_m1hgl_8\">not </span><span class=\"_fadeIn_m1hgl_8\">replacing </span><span class=\"_fadeIn_m1hgl_8\">people — </span><span class=\"_fadeIn_m1hgl_8\">it’s </span><span class=\"_fadeIn_m1hgl_8\">helping </span><span class=\"_fadeIn_m1hgl_8\">them </span><span class=\"_fadeIn_m1hgl_8\">make </span><span class=\"_fadeIn_m1hgl_8\">faster, </span><span class=\"_fadeIn_m1hgl_8\">smarter </span><span class=\"_fadeIn_m1hgl_8\">choices.</span></p><p data-start=\"1414\" data-end=\"1597\"><span class=\"_fadeIn_m1hgl_8\"><img src=\"data:image/jpeg;base64,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\" alt=\"\" width=\"300\"></span></p>\n\n<hr data-start=\"1722\" data-end=\"1725\">\n\n<hr data-start=\"2338\" data-end=\"2341\">\n<h3 data-start=\"2343\" data-end=\"2362\"><span class=\"_fadeIn_m1hgl_8\">? </span><span class=\"_fadeIn_m1hgl_8\">What’s </span><span class=\"_fadeIn_m1hgl_8\">Next?</span></h3>\n<p data-start=\"2364\" data-end=\"2593\"><span class=\"_fadeIn_m1hgl_8\">As </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">continues </span><span class=\"_fadeIn_m1hgl_8\">to </span><span class=\"_fadeIn_m1hgl_8\">grow, </span><span class=\"_fadeIn_m1hgl_8\">we’ll </span><span class=\"_fadeIn_m1hgl_8\">see </span><span class=\"_fadeIn_m1hgl_8\">even </span><span class=\"_fadeIn_m1hgl_8\">more </span><span class=\"_fadeIn_m1hgl_8\">integration </span><span class=\"_fadeIn_m1hgl_8\">in </span><span class=\"_fadeIn_m1hgl_8\">education, </span><span class=\"_fadeIn_m1hgl_8\">entertainment, </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">personal </span><span class=\"_fadeIn_m1hgl_8\">productivity. </span><span class=\"_fadeIn_m1hgl_8\">But </span><span class=\"_fadeIn_m1hgl_8\">with </span><span class=\"_fadeIn_m1hgl_8\">all </span><span class=\"_fadeIn_m1hgl_8\">its </span><span class=\"_fadeIn_m1hgl_8\">power </span><span class=\"_fadeIn_m1hgl_8\">comes </span><span class=\"_fadeIn_m1hgl_8\">responsibility — </span><span class=\"_fadeIn_m1hgl_8\">we </span><span class=\"_fadeIn_m1hgl_8\">must </span><span class=\"_fadeIn_m1hgl_8\">build </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">use </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">ethically, </span><span class=\"_fadeIn_m1hgl_8\">keeping </span><span class=\"_fadeIn_m1hgl_8\">human </span><span class=\"_fadeIn_m1hgl_8\">needs </span><span class=\"_fadeIn_m1hgl_8\">at </span><span class=\"_fadeIn_m1hgl_8\">the </span><span class=\"_fadeIn_m1hgl_8\">center.</span></p>\n<hr data-start=\"2595\" data-end=\"2598\">\n<h3 data-start=\"2600\" data-end=\"2621\"><span class=\"_fadeIn_m1hgl_8\">? </span><span class=\"_fadeIn_m1hgl_8\">Final </span><span class=\"_fadeIn_m1hgl_8\">Thoughts</span></h3>\n<p data-start=\"2623\" data-end=\"2824\"><span class=\"_fadeIn_m1hgl_8\">The </span><span class=\"_fadeIn_m1hgl_8\">age </span><span class=\"_fadeIn_m1hgl_8\">of </span><span class=\"_fadeIn_m1hgl_8\">AI </span><span class=\"_fadeIn_m1hgl_8\">is </span><span class=\"_fadeIn_m1hgl_8\">here — </span><span class=\"_fadeIn_m1hgl_8\">not </span><span class=\"_fadeIn_m1hgl_8\">in </span><span class=\"_fadeIn_m1hgl_8\">a </span><span class=\"_fadeIn_m1hgl_8\">distant </span><span class=\"_fadeIn_m1hgl_8\">tomorrow, </span><span class=\"_fadeIn_m1hgl_8\">but </span><span class=\"_fadeIn_m1hgl_8\">in </span><span class=\"_fadeIn_m1hgl_8\">the </span><span class=\"_fadeIn_m1hgl_8\">notifications, </span><span class=\"_fadeIn_m1hgl_8\">apps, </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">gadgets </span><span class=\"_fadeIn_m1hgl_8\">we </span><span class=\"_fadeIn_m1hgl_8\">use </span><span class=\"_fadeIn_m1hgl_8\">every </span><span class=\"_fadeIn_m1hgl_8\">day. </span><span class=\"_fadeIn_m1hgl_8\">Embracing </span><span class=\"_fadeIn_m1hgl_8\">it </span><span class=\"_fadeIn_m1hgl_8\">means </span><span class=\"_fadeIn_m1hgl_8\">understanding </span><span class=\"_fadeIn_m1hgl_8\">how </span><span class=\"_fadeIn_m1hgl_8\">it </span><span class=\"_fadeIn_m1hgl_8\">works </span><span class=\"_fadeIn_m1hgl_8\">and </span><span class=\"_fadeIn_m1hgl_8\">how </span><span class=\"_fadeIn_m1hgl_8\">we </span><span class=\"_fadeIn_m1hgl_8\">can </span><span class=\"_fadeIn_m1hgl_8\">shape </span><span class=\"_fadeIn_m1hgl_8\">it </span><span class=\"_fadeIn_m1hgl_8\">to </span><span class=\"_fadeIn_m1hgl_8\">serve </span><span class=\"_fadeIn_m1hgl_8\">everyone.</span></p>', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-05-10 20:38:18', '2025-05-14 23:49:36', 'text/html');
INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `post_type`, `post_status`, `post_parent`, `file_path`, `author_id`, `business_id`, `branch_id`, `created_at`, `updated_at`, `mime_type`) VALUES
(355, '681ffffd1dba2_React Mern.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/681ffffd1dba2_React Mern.jpg', 1, 0, 0, '2025-05-11 01:40:13', '2025-05-11 01:40:13', ''),
(356, '6820006cafd54_Capture1.JPG', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820006cafd54_Capture1.JPG', 1, 0, 0, '2025-05-11 01:42:04', '2025-05-11 01:42:04', ''),
(357, '6820007985624_React Mern Full Stack 3.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820007985624_React Mern Full Stack 3.jpg', 1, 0, 0, '2025-05-11 01:42:17', '2025-05-11 01:42:17', ''),
(361, 'Quo obcaecati minima', 'quo-obcaecati-minima', '<p><br></p>', 'post', 'draft', NULL, NULL, 2, 0, 0, '2025-05-10 20:44:30', '2025-05-10 20:44:30', 'text/html'),
(362, 'Inventore quibusdam ', 'inventore-quibusdam-1', '<p>Possimus, hic id non.</p>', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-05-10 20:44:59', '2025-05-10 20:46:27', 'text/html'),
(363, '6820016f2fbc9_Full Stack Mern React Custom Development.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820016f2fbc9_Full Stack Mern React Custom Development.jpg', 1, 0, 0, '2025-05-11 01:46:23', '2025-05-11 01:46:23', ''),
(364, '6820018e1d99d_React Mern Full Stack.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820018e1d99d_React Mern Full Stack.jpg', 1, 0, 0, '2025-05-11 01:46:54', '2025-05-11 01:46:54', ''),
(365, 'Culpa sit ad evenie', 'culpa-sit-ad-evenie-1', '<p>Do fuga. Laudantium.</p>', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-05-10 20:47:00', '2025-05-10 20:47:06', 'text/html'),
(366, '682001b39ac9b_pngaaa.com-7365622.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/682001b39ac9b_pngaaa.com-7365622.png', 1, 0, 0, '2025-05-11 01:47:31', '2025-05-11 01:47:31', ''),
(367, 'Numquam obcaecati vi', 'numquam-obcaecati-vi', 'Et obcaecati corpori.', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-05-10 20:47:35', '2025-05-10 20:47:35', 'text/html'),
(368, '682001ecdc98a_React Mern Full Stack Developer Website.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/682001ecdc98a_React Mern Full Stack Developer Website.jpg', 1, 0, 0, '2025-05-11 01:48:29', '2025-05-11 01:48:29', ''),
(369, 'Doloremque temporibu', 'doloremque-temporibu', '<p>Et recusandae. Optio.</p>', 'post', 'published', NULL, NULL, 2, 0, 0, '2025-05-10 20:48:33', '2025-05-10 20:48:33', 'text/html'),
(370, '6820cfb5f0595_parrot.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820cfb5f0595_parrot.jpg', 1, 0, 0, '2025-05-11 16:26:30', '2025-05-11 16:26:30', ''),
(371, '6820cff83e05e_body-skeleton-scan.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820cff83e05e_body-skeleton-scan.jpg', 1, 0, 0, '2025-05-11 16:27:36', '2025-05-11 16:27:36', ''),
(372, '6820d001d5bda_fabric_car.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6820d001d5bda_fabric_car.jpeg', 1, 0, 0, '2025-05-11 16:27:45', '2025-05-11 16:27:45', ''),
(373, '68226a581fb81_Pink and Black Gradient TikTok Profile Picture.png', NULL, NULL, 'attachment', 'published', NULL, '/uploads/68226a581fb81_Pink and Black Gradient TikTok Profile Picture.png', 1, 0, 0, '2025-05-12 21:38:32', '2025-05-12 21:38:32', ''),
(374, '68226aa567de5_peakpx.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/68226aa567de5_peakpx.jpg', 1, 0, 0, '2025-05-12 21:39:49', '2025-05-12 21:39:49', ''),
(375, '68226ae62d7ea_peakpx (1).jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/68226ae62d7ea_peakpx (1).jpg', 1, 0, 0, '2025-05-12 21:40:54', '2025-05-12 21:40:54', ''),
(376, '68226af17a8e4_wallpaperflare.com_wallpaper.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/68226af17a8e4_wallpaperflare.com_wallpaper.jpg', 1, 0, 0, '2025-05-12 21:41:05', '2025-05-12 21:41:05', ''),
(377, 'Ea et natus magni ma', NULL, 'Voluptatum consectet', 'banner', 'draft', NULL, NULL, 2, 0, 0, '2025-05-12 16:41:11', '2025-05-12 21:41:11', ''),
(378, '6827aec9e750c_hqdefault.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6827aec9e750c_hqdefault.jpg', 1, 0, 0, '2025-05-16 21:31:53', '2025-05-16 21:31:53', ''),
(379, '6827aed1a6151_Screenshot 2025-05-16 205335.jpg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6827aed1a6151_Screenshot 2025-05-16 205335.jpg', 1, 0, 0, '2025-05-16 21:32:01', '2025-05-16 21:32:01', ''),
(380, 'Lorem eius in offici', NULL, 'In cum sint rerum qu', 'banner', 'draft', NULL, NULL, 2, 0, 0, '2025-05-16 16:32:10', '2025-05-16 22:04:27', ''),
(381, '6827fcb355052_67de1fea2f769_images.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6827fcb355052_67de1fea2f769_images.jpeg', 1, 0, 0, '2025-05-17 03:04:19', '2025-05-17 03:04:19', ''),
(382, '6827fcb903d8e_67de1fea2f769_images.jpeg', NULL, NULL, 'attachment', 'published', NULL, '/uploads/6827fcb903d8e_67de1fea2f769_images.jpeg', 1, 0, 0, '2025-05-17 03:04:25', '2025-05-17 03:04:25', '');

-- --------------------------------------------------------

--
-- Table structure for table `post_meta`
--

CREATE TABLE `post_meta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post_meta`
--

INSERT INTO `post_meta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(4, 4, 'featured_image_id', '24'),
(17, 17, 'featured_image_id', '37'),
(19, 19, 'featured_image_id', '39'),
(24, 24, 'alt_text', 'Blinds on a small window'),
(37, 37, 'alt_text', 'Seasonal blind decor'),
(39, 39, 'alt_text', 'Blinds for coastal homes'),
(41, 44, 'thumbnail_path', '/uploads/thumbnails/thumb_67d377dd02f61_WhatsApp Image 2022-11-09 at 17.44.22.jpg'),
(42, 45, 'thumbnail_path', '/uploads/thumbnails/thumb_67d3783e27f7b_WhatsApp Image 2022-11-09 at 17.44.22.jpg'),
(43, 46, 'thumbnail_path', '/uploads/thumbnails/thumb_67d3ad4d552ae_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(44, 54, 'thumbnail_path', '/uploads/thumbnails/thumb_67d46c691c89a_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(45, 55, 'thumbnail_path', '/uploads/thumbnails/thumb_67d57422d58ae_services-location-map.png'),
(46, 56, 'thumbnail_path', '/uploads/thumbnails/thumb_67d574397eab9_services-location-map.png'),
(47, 57, 'thumbnail_path', '/uploads/thumbnails/thumb_67d576993075c_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(48, 58, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5770b6c367_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(49, 59, 'thumbnail_path', '/uploads/thumbnails/thumb_67d57852287b3_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(50, 60, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5785706d0f_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(51, 61, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5787cba09e_services-location-map.png'),
(52, 62, 'thumbnail_path', '/uploads/thumbnails/thumb_67d57a226c958_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(53, 63, 'thumbnail_path', '/uploads/thumbnails/thumb_67d57a2796d8b_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(54, 64, 'thumbnail_path', '/uploads/thumbnails/thumb_67d57f46d9cbe_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(55, 65, 'thumbnail_path', '/uploads/thumbnails/thumb_67d580728a25b_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(56, 66, 'thumbnail_path', '/uploads/thumbnails/thumb_67d583b9ae2fd_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(57, 67, 'thumbnail_path', '/uploads/thumbnails/thumb_67d583c0dedde_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(58, 68, 'thumbnail_path', '/uploads/thumbnails/thumb_67d583f6d4f92_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(59, 77, 'thumbnail_path', '/uploads/thumbnails/thumb_67d59f7e7e6d0_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(96, 87, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5e691a80ea_customerappointment modal datatable.PNG'),
(97, 88, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5e6fd7b156_customerappointment modal datatable.PNG'),
(98, 89, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5e765a8886_customerappointment modal datatable.PNG'),
(111, 92, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5e85de8a0a_videouploaddropzondebranch.PNG'),
(112, 93, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5e8dfd7df9_customerappointment modal datatable.PNG'),
(125, 97, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5eb34a294e_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(126, 98, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5ec151201f_dF6U8mKTofwXnUdT-generated_image.jpg'),
(127, 99, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5ecb31b9b7_dF6U8mKTofwXnUdT-generated_image.jpg'),
(128, 100, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5ed6178d42_dF6U8mKTofwXnUdT-generated_image.jpg'),
(129, 101, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5edd346db6_customerappointment modal datatable.PNG'),
(130, 102, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5f15bb1496_dF6U8mKTofwXnUdT-generated_image.jpg'),
(131, 103, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5f2a765169_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(132, 104, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5f59cbc5b1_customerappointment modal datatable.PNG'),
(133, 105, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5f915e8195_customerappointment modal datatable.PNG'),
(134, 106, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5fa5246c29_customerappointment modal datatable.PNG'),
(135, 107, 'thumbnail_path', '/uploads/thumbnails/thumb_67d5fa5988008_customerappointment modal datatable.PNG'),
(172, 119, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f34bdadba_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(173, 123, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f3ad3375f_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(174, 127, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f4be8b659_videouploaddropzondebranch.PNG'),
(175, 128, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f52611629_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(176, 129, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f53866402_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(177, 130, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f57721845_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(178, 131, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f580ec440_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(179, 132, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f5e334760_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(180, 133, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f5ecbcb43_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(181, 134, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f5f2f218b_services-location-map.png'),
(182, 135, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f60dd63e3_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(183, 136, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6f7861b297_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(184, 137, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6fa28959a5_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(185, 142, 'thumbnail_path', '/uploads/thumbnails/thumb_67d6fa83098d9_WhatsApp Image 2025-03-09 at 16.08.48_50ced336.jpg'),
(202, 150, 'thumbnail_path', '/uploads/thumbnails/thumb_67d70927b747f_customerappointment modal datatable.PNG'),
(203, 151, 'thumbnail_path', '/uploads/thumbnails/thumb_67d7092f8be8f_videouploaddropzondebranch.PNG'),
(212, 154, 'thumbnail_path', '/uploads/67d82115689a7_background.jpg'),
(213, 155, 'thumbnail_path', '/uploads/thumbnails/thumb_67d82128342b4_1584_1_90594.jpg'),
(214, 156, 'thumbnail_path', '/uploads/67d8247ebeff1_1584_1_90594.jpg'),
(215, 157, 'thumbnail_path', '/uploads/thumbnails/thumb_67d824ceca0e6_1584_1_90594.jpg'),
(216, 158, 'thumbnail_path', '/uploads/thumbnails/2025/03/thumb_67d825dc144e9_1584_1_90594.jpg'),
(217, 159, 'thumbnail_path', '/uploads/thumbnails/2025/03/thumb_67d825fbb6f0f_1584_1_90594.jpg'),
(218, 160, 'thumbnail_path', '/uploads/thumbnails/2025/03/thumb_67d8268598c86_1584_1_90594.jpg'),
(219, 161, 'thumbnail_path', '/uploads/thumbnails/thumb_67d826e9de4bd_1584_1_90594.jpg'),
(220, 162, 'thumbnail_path', '/uploads/thumbnails/thumb_67d82755073d3_1584_1_90594.jpg'),
(221, 163, 'thumbnail_path', '/uploads/67d8277062df7_background.jpg'),
(222, 164, 'thumbnail_path', '/uploads/thumbnails/thumb_67d82787adb5c_1666551058771D6FC0D0B-1F87-4A9A-9334-9CADD0E2C81F.jpg'),
(223, 166, 'thumbnail_path', '/uploads/thumbnails/thumb_67d86cc0914e9_customerappointment modal datatable.PNG'),
(224, 167, 'thumbnail_path', '/uploads/thumbnails/thumb_67d86cde07562_customerappointment modal datatable.PNG'),
(229, 168, 'thumbnail_path', '/uploads/thumbnails/thumb_67d8ae2cacc1f_background.png'),
(230, 169, 'thumbnail_path', '/uploads/thumbnails/thumb_67d8ae4688789_cover.png'),
(231, 170, 'thumbnail_path', '/uploads/thumbnails/thumb_67d8ae5093523_cover.png'),
(232, 171, 'thumbnail_path', '/uploads/thumbnails/thumb_67d8ae58321da_background.png'),
(233, 172, 'discount_percentage', '20%'),
(234, 172, 'offer_ends', '2025-03-31'),
(235, 172, 'redirect_url', '/'),
(236, 172, 'cover_image_id', '170'),
(237, 172, 'background_image_id', '171'),
(238, 172, 'button_text', 'Shop Sales'),
(239, 172, 'subtitle', 'Buy 8 motors & get a smart hub free'),
(249, 176, 'thumbnail_path', '/uploads/thumbnails/thumb_67d8afff39c60_background.png'),
(250, 178, 'thumbnail_path', '/uploads/thumbnails/thumb_67d8b2f69edc8_background.png'),
(269, 185, 'thumbnail_path', '/uploads/thumbnails/thumb_67d963d427d9e_customerappointment modal datatable.PNG'),
(270, 186, 'thumbnail_path', '/uploads/thumbnails/thumb_67d964ba70b70_customerappointment modal datatable.PNG'),
(278, 191, 'thumbnail_path', '/uploads/thumbnails/thumb_67daf8f4e65ef_services-location-map.png'),
(279, 196, 'thumbnail_path', '/uploads/thumbnails/thumb_67db1612f4187_IMG_0871.JPG'),
(280, 198, 'thumbnail_path', '/uploads/thumbnails/thumb_67db16ab9a4e0_IMG_0876.JPG'),
(281, 199, 'thumbnail_path', '/uploads/thumbnails/thumb_67db16b5f0988_image-20250307-175214.png'),
(289, 202, 'thumbnail_path', '/uploads/thumbnails/thumb_67dbd98bd3033_customerappointment modal datatable.PNG'),
(300, 213, 'thumbnail_path', '/uploads/thumbnails/thumb_67dbe63069764_services-location-map.png'),
(303, 216, 'thumbnail_path', '/uploads/thumbnails/thumb_67dc3d659aa5a_image-20250307-175214.png'),
(304, 217, 'featured_image_id', '216'),
(305, 218, 'thumbnail_path', '/uploads/thumbnails/thumb_67dccccf499e8_image-20250307-175214.png'),
(306, 219, 'featured_image_id', '218'),
(316, 223, 'thumbnail_path', '/uploads/thumbnails/thumb_67dceca269cd9_customerappointment modal datatable.PNG'),
(318, 224, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcecc3cb2e0_customerappointment modal datatable.PNG'),
(319, 225, 'featured_image_id', '224'),
(320, 226, 'thumbnail_path', '/uploads/thumbnails/thumb_67dced0f5a484_customerappointment modal datatable.PNG'),
(322, 228, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf0115209c_customerappointment modal datatable.PNG'),
(323, 229, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf1be0a2eb_services-location-map.png'),
(324, 230, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf2375dd01_videouploaddropzondebranch.PNG'),
(325, 231, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf267a1d75_customerappointment modal datatable.PNG'),
(326, 232, 'featured_image_id', '231'),
(327, 233, 'featured_image_id', '274'),
(328, 234, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf590a96ba_search.PNG'),
(329, 235, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf5df13d7f_search.PNG'),
(330, 236, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf60fa86c9_customerappointment modal datatable.PNG'),
(331, 237, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcf7297ec2a_search.PNG'),
(332, 238, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcfbae092cc_videouploaddropzondebranch.PNG'),
(333, 239, 'featured_image_id', '238'),
(334, 240, 'thumbnail_path', '/uploads/thumbnails/thumb_67dcfbde9306e_videouploaddropzondebranch.PNG'),
(347, 247, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd0ca55e32b_ftp.png'),
(348, 197, 'featured_image_id', '247'),
(349, 248, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd53301dcd2_services-location-map.png'),
(350, 249, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd5567e4cda_videouploaddropzondebranch.PNG'),
(351, 250, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd667a98749_image-20250307-175214.png'),
(353, 252, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd66a199c48_image-20250307-175214.png'),
(409, 272, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd8cf415974_image-20250307-175214.png'),
(411, 274, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd966012a2f_background.png'),
(412, 275, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd9bddeb1d4_WhatsApp Image 2025-03-21 at 10.03.01 PM.jpeg'),
(413, 177, 'featured_image_id', '275'),
(414, 276, 'thumbnail_path', '/uploads/thumbnails/thumb_67dd9c90675a1_WhatsApp Image 2025-03-21 at 10.03.08 PM.jpeg'),
(415, 277, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda6d43994d_videouploaddropzondebranch.PNG'),
(416, 278, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda7992d2a9_videouploaddropzondebranch.PNG'),
(417, 279, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda7a223bec_search.PNG'),
(418, 280, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda8408bed5_videouploaddropzondebranch.PNG'),
(419, 281, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda85c101dc_search.PNG'),
(420, 282, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda8c96bd77_videouploaddropzondebranch.PNG'),
(421, 283, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda8cd83331_videouploaddropzondebranch.PNG'),
(422, 284, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda92dad22d_search.PNG'),
(423, 285, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda9398077a_search.PNG'),
(424, 286, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda94e9b88f_videouploaddropzondebranch.PNG'),
(425, 287, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda990e0e92_search.PNG'),
(426, 288, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda9ef46291_videouploaddropzondebranch.PNG'),
(427, 289, 'thumbnail_path', '/uploads/thumbnails/thumb_67dda9fc38a50_videouploaddropzondebranch.PNG'),
(428, 290, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddaacddce27_videouploaddropzondebranch.PNG'),
(429, 291, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddaadf63f1f_videouploaddropzondebranch.PNG'),
(430, 292, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddab1b1b56b_videouploaddropzondebranch.PNG'),
(431, 293, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddab8b212de_search.PNG'),
(432, 294, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddabb650e10_videouploaddropzondebranch.PNG'),
(433, 295, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddabd10b15c_videouploaddropzondebranch.PNG'),
(434, 296, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddabe298c36_search.PNG'),
(435, 297, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddac08a3bf4_videouploaddropzondebranch.PNG'),
(436, 298, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddac2f02538_videouploaddropzondebranch.PNG'),
(437, 299, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddac43e4de6_videouploaddropzondebranch.PNG'),
(438, 300, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddac9bc25ab_videouploaddropzondebranch.PNG'),
(439, 301, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddacfb71079_search.PNG'),
(440, 302, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddad24c2458_search.PNG'),
(441, 303, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddad3de0571_videouploaddropzondebranch.PNG'),
(442, 304, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddad50d140c_videouploaddropzondebranch.PNG'),
(443, 305, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddad68241fd_search.PNG'),
(444, 306, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddae0483bf1_videouploaddropzondebranch.PNG'),
(445, 307, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddae2c4d0b7_videouploaddropzondebranch.PNG'),
(446, 308, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddaea387e16_videouploaddropzondebranch.PNG'),
(447, 309, 'thumbnail_path', '/uploads/thumbnails/thumb_67ddb36a4c99e_videouploaddropzondebranch.PNG'),
(457, 313, 'thumbnail_path', '/uploads/thumbnails/thumb_67de099718b38_background.png'),
(458, 314, 'thumbnail_path', '/uploads/thumbnails/thumb_67de099f9d4ed_cover.png'),
(459, 315, 'thumbnail_path', '/uploads/thumbnails/thumb_67de09a4292d3_background.png'),
(460, 316, 'discount_percentage', '50%'),
(461, 316, 'offer_ends', '2025-03-22'),
(462, 316, 'redirect_url', '/Test'),
(463, 316, 'cover_image_id', '314'),
(464, 316, 'background_image_id', '315'),
(465, 316, 'button_text', 'Shop Sales'),
(466, 316, 'subtitle', 'Don’t Miss The Window to Saves!'),
(467, 317, 'thumbnail_path', '/uploads/thumbnails/thumb_67de1e1ff007c_IMG_0871.JPG'),
(468, 318, 'featured_image_id', '317'),
(469, 319, 'thumbnail_path', '/uploads/thumbnails/thumb_67de1fbb42a29_totalshade-charcoal-54-blackout-pleated-a.jpg'),
(470, 320, 'thumbnail_path', '/uploads/thumbnails/thumb_67de1fea2f769_images.jpeg'),
(471, 321, 'discount_percentage', '50%'),
(472, 321, 'offer_ends', '2025-03-28'),
(473, 321, 'redirect_url', '/'),
(474, 321, 'cover_image_id', '319'),
(475, 321, 'background_image_id', '320'),
(476, 321, 'button_text', 'Shop Sales'),
(477, 321, 'subtitle', 'Benefit from our exclusive offer'),
(478, 323, 'thumbnail_path', '/uploads/thumbnails/thumb_6818d5eb3ec9a_wallpaperflare.com_wallpaper (2).jpg'),
(480, 326, 'thumbnail_path', '/uploads/thumbnails/thumb_6818ecbd960ef_peakpx (1).jpg'),
(483, 329, 'thumbnail_path', '/uploads/thumbnails/thumb_6819c75b0b31d_React Mern Full Stack.jpg'),
(491, 331, 'thumbnail_path', '/uploads/thumbnails/thumb_6819c80ab0814_wallpaperflare.com_wallpaper (1).jpg'),
(492, 332, 'thumbnail_path', '/uploads/thumbnails/thumb_6819ccada5a81_peakpx.jpg'),
(493, 333, 'thumbnail_path', '/uploads/thumbnails/thumb_6819ccb836b4c_React Mern Full Stack.jpg'),
(494, 334, 'discount_percentage', '30%'),
(495, 334, 'offer_ends', '1996-12-01'),
(496, 334, 'redirect_url', 'Aute aperiam non des'),
(497, 334, 'cover_image_id', '336'),
(498, 334, 'background_image_id', '335'),
(499, 334, 'button_text', 'Buy Now'),
(500, 334, 'subtitle', 'Hurry Up!'),
(501, 335, 'thumbnail_path', '/uploads/thumbnails/thumb_681a1d27199d0_rollershadekf.jpg'),
(502, 336, 'thumbnail_path', '/uploads/thumbnails/thumb_681a1d462a4b0_draperykf.jpg'),
(503, 337, 'thumbnail_path', '/uploads/thumbnails/thumb_681ca7a571f4d_award2.png'),
(504, 338, 'thumbnail_path', '/uploads/thumbnails/thumb_681ca865212ec_award2.png'),
(505, 339, 'thumbnail_path', '/uploads/thumbnails/thumb_681ca871a037b_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg'),
(506, 340, 'thumbnail_path', '/uploads/thumbnails/thumb_681ca879ca87c_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg'),
(516, 344, 'thumbnail_path', '/uploads/thumbnails/thumb_681cab112346f_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg'),
(517, 345, 'thumbnail_path', '/uploads/thumbnails/thumb_681cab160ef90_WhatsApp Image 2025-04-25 at 5.49.16 AM.jpeg'),
(527, 349, 'thumbnail_path', '/uploads/thumbnails/thumb_681e4a9e61118_peter.png'),
(529, 351, 'thumbnail_path', '/uploads/thumbnails/thumb_681e4e7e3a968_image 622.png'),
(530, 355, 'thumbnail_path', '/uploads/thumbnails/thumb_681ffffd1dba2_React Mern.jpg'),
(531, 354, 'featured_image_id', '355'),
(532, 356, 'thumbnail_path', '/uploads/thumbnails/thumb_6820006cafd54_Capture1.JPG'),
(533, 357, 'thumbnail_path', '/uploads/thumbnails/thumb_6820007985624_React Mern Full Stack 3.jpg'),
(543, 363, 'thumbnail_path', '/uploads/thumbnails/thumb_6820016f2fbc9_Full Stack Mern React Custom Development.jpg'),
(544, 362, 'featured_image_id', '363'),
(545, 364, 'thumbnail_path', '/uploads/thumbnails/thumb_6820018e1d99d_React Mern Full Stack.jpg'),
(546, 365, 'featured_image_id', '364'),
(547, 366, 'thumbnail_path', '/uploads/thumbnails/thumb_682001b39ac9b_pngaaa.com-7365622.png'),
(548, 367, 'featured_image_id', '366'),
(549, 368, 'thumbnail_path', '/uploads/thumbnails/thumb_682001ecdc98a_React Mern Full Stack Developer Website.jpg'),
(550, 369, 'featured_image_id', '368'),
(551, 370, 'thumbnail_path', '/uploads/thumbnails/thumb_6820cfb5f0595_parrot.jpg'),
(552, 371, 'thumbnail_path', '/uploads/thumbnails/thumb_6820cff83e05e_body-skeleton-scan.jpg'),
(553, 372, 'thumbnail_path', '/uploads/thumbnails/thumb_6820d001d5bda_fabric_car.jpeg'),
(554, 373, 'thumbnail_path', '/uploads/thumbnails/thumb_68226a581fb81_Pink and Black Gradient TikTok Profile Picture.png'),
(555, 374, 'thumbnail_path', '/uploads/thumbnails/thumb_68226aa567de5_peakpx.jpg'),
(556, 375, 'thumbnail_path', '/uploads/thumbnails/thumb_68226ae62d7ea_peakpx (1).jpg'),
(557, 376, 'thumbnail_path', '/uploads/thumbnails/thumb_68226af17a8e4_wallpaperflare.com_wallpaper.jpg'),
(558, 377, 'discount_percentage', 'Sed et sint eiusmod '),
(559, 377, 'offer_ends', '1985-08-06'),
(560, 377, 'redirect_url', 'Cumque incidunt off'),
(561, 377, 'cover_image_id', '375'),
(562, 377, 'background_image_id', '376'),
(563, 377, 'button_text', 'Magnam culpa cumque'),
(564, 377, 'subtitle', 'Reiciendis rerum rep'),
(565, 378, 'thumbnail_path', '/uploads/thumbnails/thumb_6827aec9e750c_hqdefault.jpg'),
(566, 379, 'thumbnail_path', '/uploads/thumbnails/thumb_6827aed1a6151_Screenshot 2025-05-16 205335.jpg'),
(567, 380, 'discount_percentage', 'Rem labore veniam a'),
(568, 380, 'offer_ends', '2016-03-25'),
(569, 380, 'redirect_url', 'Et ad nemo consequat'),
(570, 380, 'cover_image_id', '381'),
(571, 380, 'background_image_id', '382'),
(572, 380, 'button_text', 'Doloribus illo vero '),
(573, 380, 'subtitle', 'Nisi deserunt ex neq'),
(574, 381, 'thumbnail_path', '/uploads/thumbnails/thumb_6827fcb355052_67de1fea2f769_images.jpeg'),
(575, 382, 'thumbnail_path', '/uploads/thumbnails/thumb_6827fcb903d8e_67de1fea2f769_images.jpeg');

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
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `tag_id` int(11) NOT NULL,
  `tag` text NOT NULL,
  `business_id` bigint(20) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tag_id`, `tag`, `business_id`, `branch_id`, `created_at`) VALUES
(81, 'Blinds', 0, 0, '2025-02-11 12:22:24'),
(82, 'Fabric Blinds', 0, 0, '2025-02-14 16:34:06'),
(83, 'Control System', 0, 0, '2025-02-18 03:29:36'),
(84, 'Machines', 0, 0, '2025-02-18 04:21:28'),
(85, 'Lounge', 0, 0, '2025-03-02 06:11:49'),
(86, 'Bedroom', 0, 0, '2025-03-02 06:12:17'),
(87, 'Dining', 0, 0, '2025-03-02 14:55:34'),
(103, 'Drawing Room', 0, 0, '2025-03-12 15:30:34');

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
  `business_id` bigint(20) NOT NULL COMMENT 'Business id to refer the business in case of multi-tenancy within one database',
  `branch_id` bigint(20) NOT NULL COMMENT 'Branch id to refer the branch of business in case of multi-tenancy within one database',
  `phone_verified` tinyint(1) DEFAULT 0 COMMENT 'Phone number verified',
  `email_verified` tinyint(1) DEFAULT 0 COMMENT 'Email address verified',
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `phone`, `password`, `role`, `business_id`, `branch_id`, `phone_verified`, `email_verified`, `createdAt`) VALUES
(1, 'admin@scoopnation.com', '+923001234567', '$2y$10$dummyadminhash', 'administrator', 0, 0, 1, 1, '2025-09-19 10:20:00'),
(2, 'visitor@scoopnation.com', '+923001234568', NULL, 'visitor', 0, 0, 0, 0, '2025-09-19 10:25:00'),
(3, 'customer@scoopnation.com', '+923001234569', '$2y$10$dummycustomerhash', 'customer', 0, 0, 1, 1, '2025-09-19 10:30:00'),
(4, 'rider@scoopnation.com', '+923001234570', '$2y$10$dummyriderhash', 'delivery_rider', 0, 0, 1, 1, '2025-09-19 10:35:00'),
(5, 'customer2@scoopnation.com', '+923001234571', '$2y$10$dummyhash2', 'customer', 0, 0, 0, 0, '2025-09-19 10:40:00');

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
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_branch_id` (`branch_id`),
  ADD KEY `idx_email` (`email_address`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorite_products`
--
ALTER TABLE `favorite_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`imageID`);

--
-- Indexes for table `media_meta`
--
ALTER TABLE `media_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `idx_post_type_status` (`post_type`,`post_status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_post_type` (`post_type`);

--
-- Indexes for table `post_meta`
--
ALTER TABLE `post_meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_meta_key` (`meta_key`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promocode`
--
ALTER TABLE `promocode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rider`
--
ALTER TABLE `rider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variant`
--
ALTER TABLE `variant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banner_campaign`
--
ALTER TABLE `banner_campaign`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branch_product`
--
ALTER TABLE `branch_product`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `branch_special_days`
--
ALTER TABLE `branch_special_days`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branch_timings`
--
ALTER TABLE `branch_timings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bundle`
--
ALTER TABLE `bundle`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bundle_product`
--
ALTER TABLE `bundle_product`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `business`
--
ALTER TABLE `business`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `favorite_products`
--
ALTER TABLE `favorite_products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `imageID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `media_meta`
--
ALTER TABLE `media_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;

--
-- AUTO_INCREMENT for table `post_meta`
--
ALTER TABLE `post_meta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=576;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `promocode`
--
ALTER TABLE `promocode`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rider`
--
ALTER TABLE `rider`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `variant`
--
ALTER TABLE `variant`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD CONSTRAINT `fk_contact_branch` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contact_business` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`post_parent`) REFERENCES `posts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `post_meta`
--
ALTER TABLE `post_meta`
  ADD CONSTRAINT `post_meta_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
