-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 02:23 PM
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
-- Table structure for table `bundle`
--

CREATE TABLE `bundle` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discountedPrice` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bundle_product`
--

CREATE TABLE `bundle_product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bundleId` bigint(20) NOT NULL,
  `productId` bigint(20) DEFAULT NULL,
  `variantId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `BundleProduct_bundleId_fkey` (`bundleId`),
  KEY `BundleProduct_productId_fkey` (`productId`),
  KEY `BundleProduct_variantId_fkey` (`variantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `variantId` bigint(20) NOT NULL,
  `quantity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `createdAt` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updatedAt` datetime(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `CartItem_userId_productId_variantId_key` (`userId`,`productId`,`variantId`),
  KEY `CartItem_productId_fkey` (`productId`),
  KEY `CartItem_variantId_fkey` (`variantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mainImage` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Category_name_key` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `mainImage`) VALUES
(1, 'Con-Ice-Cream', 'Wafer-Ice-Cream-PNG-Picture.png');

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `imageID` bigint(20) NOT NULL AUTO_INCREMENT,
  `productID` bigint(20) NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`imageID`),
  KEY `image_productID_fkey` (`productID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apartment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postalCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateTime` datetime(3) DEFAULT current_timestamp(3),
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int(11) NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderNotice` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `name`, `lastname`, `phone`, `email`, `company`, `address`, `apartment`, `postalCode`, `dateTime`, `status`, `total`, `city`, `country`, `orderNotice`) VALUES
(1, 'Ameer ', 'hamza', '+923254430008', 'ameerarif12348@gmail.com', 'Ameer', 'Post Office Sarhali Kalan Teshil And District Kasur', 'Post Office Sarhali Kalan Teshil And District Kasur', '55110', '2025-05-10 18:37:10.670', 'processing', 8133, 'KASUR', 'Pakistan', 'Extra chocolate dena mujhe');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customerOrderId` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `variantId` bigint(20) DEFAULT NULL,
  `bundleId` bigint(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_order_product_customerOrderId_fkey` (`customerOrderId`),
  KEY `customer_order_product_productId_fkey` (`productId`),
  KEY `CustomerOrderProduct_variantId_fkey` (`variantId`),
  KEY `CustomerOrderProduct_bundleId_fkey` (`bundleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`id`, `customerOrderId`, `productId`, `variantId`, `bundleId`, `quantity`) VALUES
(1, 1, 1, NULL, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mainImage` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `rating` int(11) NOT NULL DEFAULT 0,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inStock` int(11) NOT NULL DEFAULT 1,
  `categoryId` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Product_slug_key` (`slug`),
  KEY `Product_categoryId_fkey` (`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `slug`, `title`, `mainImage`, `price`, `rating`, `description`, `manufacturer`, `inStock`, `categoryId`) VALUES
(1, 'chocolate-con-ice-cream', 'Chocolate Con Ice Cream', 'Wafer-Ice-Cream-PNG-Picture.png', 1000, 5, 'This is a chocolate con icecream', 'Omore', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `promocode`
--

CREATE TABLE `promocode` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discountAmount` int(11) NOT NULL,
  `discountType` enum('FLAT','PERCENTAGE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FLAT',
  `expiryDate` datetime(3) NOT NULL,
  `minimumOrderAmount` int(11) DEFAULT NULL,
  `createdAt` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updatedAt` datetime(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `PromoCode_code_key` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_email_key` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `role`) VALUES
(1, 'wmkhan101@gmail.com', '$2a$05$ODUflk37dIp7bOt4r1W.G.ENSz/A4dCwxen08ic4U3bZmIj.rY54q', 'user'),
(2, 'ameerarif12348@gmail.com', '$2a$05$ODUflk37dIp7bOt4r1W.G.ENSz/A4dCwxen08ic4U3bZmIj.rY54q', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `variant`
--

CREATE TABLE `variant` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `productId` bigint(20) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `inStock` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `Variant_productId_fkey` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `productId` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Wishlist_userId_fkey` (`userId`),
  KEY `Wishlist_productId_fkey` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `productId`, `userId`) VALUES
(1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `_prisma_migrations`
--

CREATE TABLE `_prisma_migrations` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `finished_at` datetime(3) DEFAULT NULL,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logs` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rolled_back_at` datetime(3) DEFAULT NULL,
  `started_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `applied_steps_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `_prisma_migrations`
--

INSERT INTO `_prisma_migrations` (`id`, `checksum`, `finished_at`, `migration_name`, `logs`, `rolled_back_at`, `started_at`, `applied_steps_count`) VALUES
('1d81ad11-5277-450c-b005-3efa054d3d26', '7807c24cf4b8ffe4c72bd8d281d6202a3280134d4166ac5e4f3c4ece701bc428', '2025-05-10 17:58:46.305', '20240414064137_added_category_table_and_added_role_column', NULL, NULL, '2025-05-10 17:58:46.263', 1),
('2e2446fa-219e-40e4-92e1-65f656c91b5d', '2b53d00e7df4a7ff20c09d65808ec4d4fb130227241cce8e159cd47b4651d415', '2025-05-10 17:58:46.498', '20240418151340_added_new_customer_order_table', NULL, NULL, '2025-05-10 17:58:46.459', 1),
('369e5807-576b-4878-bbb0-b9d37fa8dd35', '4e4b5a8e6fdeb303f707d890f5e623e113a4c247a4d1f9d2fe7819609fd584d7', '2025-05-10 17:58:46.931', '20240602092804_added_wishlist_table', NULL, NULL, '2025-05-10 17:58:46.732', 1),
('49a7e737-4842-4967-99d5-a87dc678cffd', 'b7d368998d0531c38e918db97d6307653d3ea437fe30b62c62f5f6ac82bb9ff8', '2025-05-10 17:58:47.047', '20240607074201_added_cascade_delete_in_wishlist_table', NULL, NULL, '2025-05-10 17:58:46.936', 1),
('5c12c87e-ad8a-4639-834c-1354da8ec792', '9f49decbc27794ee0f3de82e79fef2c7f166d31256974b4d22b19ee6ce9cbbde', '2025-05-10 17:58:46.201', '20240320142857_podesavanje_prizme', NULL, NULL, '2025-05-10 17:58:46.077', 1),
('72f85ac1-5f83-4ea1-a194-a93717ce1f84', 'b5c832b170facaf46540c7d44d49b8a078b3f00b58b3e74efef1a1cee816cb3d', '2025-05-10 17:58:46.709', '20240512145715_bojan_update_za_customer_order_product', NULL, NULL, '2025-05-10 17:58:46.506', 1),
('7e44f708-319c-42b0-8492-c958eb00b715', '4dcf21452d785c3763908970d6f8c7da52ad83f378c030d99734514e2632e09b', '2025-05-10 17:58:47.379', '20250510062630_main', NULL, NULL, '2025-05-10 17:58:47.364', 1),
('9fc67f43-5331-47a0-a84e-51ef952875f6', '8966613ffa5250c7d2f3241dd00b64068040878dbd3b4a7ce2907de280e64e2b', '2025-05-10 17:58:46.346', '20240415100000_added_category_id_field_in_product_table', NULL, NULL, '2025-05-10 17:58:46.318', 1),
('a4b809c3-ae32-453f-b87c-195077c5df40', 'd5e4fcb5cfabb218a9ed24280dd0190aa4a9b06727c126e694f30ffbd43f4676', '2025-05-10 17:58:46.455', '20240415130405_added_relationship_between_product_table_and_category_table', NULL, NULL, '2025-05-10 17:58:46.349', 1),
('b39d9db0-eb14-44e5-b5e3-d12d5ab54fef', '7f43f474734934b9f09d36e20e3528e03ef4a63ee806a18a32d2296e33421495', '2025-05-10 17:58:46.259', '20240413064716_added_order_table', NULL, NULL, '2025-05-10 17:58:46.204', 1),
('b7e72f36-7f25-4d93-8ca1-d6b8cff878f0', 'b40a651b6a85c620f98b6956424fe65424d36d1ffc2a0cd741a07b1e654605a1', '2025-05-10 17:58:47.157', '20240607075549_added_cascade_delete_for_categories_in_product_table', NULL, NULL, '2025-05-10 17:58:47.050', 1),
('c5d6f7e2-h6i7-5k9m-n3p4-0q1r2s3t4u5v', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8', '2025-05-11 16:10:09.000', '20250511203000_add_variants_and_bundles', NULL, NULL, '2025-05-11 16:10:09.000', 1),
('c968b499-9ba9-4b87-ab9f-0f765eb3f1a3', '17372f962ba8a320258c9ee28293f5ebb1f0bdf6a87711ac9dc8390a2a43c619', '2025-05-10 17:58:47.361', '20240607111047_added_unique_constraint_to_name_column_in_the_category_table', NULL, NULL, '2025-05-10 17:58:47.321', 1),
('d944f4bc-9f0d-4e1b-a515-84924d31981f', 'b0f5489621f0cbdfb6f5cd7aab54a295952b49ba3f555fb293469466f69d2f26', '2025-05-10 17:58:46.729', '20240515154444_added_necessary_fields_for_customer_order_table', NULL, NULL, '2025-05-10 17:58:46.713', 1),
('ec4d46eb-7596-4340-bfb5-12d902d9d8f7', '6bc5f178eb74f83131e0214946d0cf2790ed3c5c7393a6128294357c3f5f7fbf', '2025-05-10 17:58:47.317', '20240607083528_added_cascade_delete_for_wishlist_in_product_table', NULL, NULL, '2025-05-10 17:58:47.162', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bundle_product`
--
ALTER TABLE `bundle_product`
  ADD CONSTRAINT `BundleProduct_bundleId_fkey` FOREIGN KEY (`bundleId`) REFERENCES `bundle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `BundleProduct_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `BundleProduct_variantId_fkey` FOREIGN KEY (`variantId`) REFERENCES `variant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `CartItem_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `product` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `CartItem_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `CartItem_variantId_fkey` FOREIGN KEY (`variantId`) REFERENCES `variant` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_productID_fkey` FOREIGN KEY (`productID`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `CustomerOrderProduct_bundleId_fkey` FOREIGN KEY (`bundleId`) REFERENCES `bundle` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `CustomerOrderProduct_variantId_fkey` FOREIGN KEY (`variantId`) REFERENCES `variant` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_order_product_customerOrderId_fkey` FOREIGN KEY (`customerOrderId`) REFERENCES `order` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_order_product_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `product` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `Product_categoryId_fkey` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `variant`
--
ALTER TABLE `variant`
  ADD CONSTRAINT `Variant_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `Wishlist_productId_fkey` FOREIGN KEY (`productId`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Wishlist_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;