<?php
// declare(strict_types=1);

// use App\Repository\CategoryRepository;
// use App\Repository\ProductRepository;
// use App\Repository\UserRepository;
// use App\Repository\OrderRepository;
// use App\Repository\OrderItemRepository;
// use App\Repository\ImageRepository;
// use App\Repository\WishlistRepository;
// use App\Repository\CartItemRepository;
// use App\Repository\VariantRepository;
// use App\Repository\BundleRepository;
// use App\Repository\PromoCodeRepository;
// use Psr\Container\ContainerInterface;

// return [
//     // Repository dependencies
//     CategoryRepository::class => function (ContainerInterface $container) {
//         return new CategoryRepository();
//     },

//     ProductRepository::class => function (ContainerInterface $container) {
//         return new ProductRepository();
//     },

//     UserRepository::class => function (ContainerInterface $container) {
//         return new UserRepository();
//     },

//     OrderRepository::class => function (ContainerInterface $container) {
//         return new OrderRepository();
//     },

//     OrderItemRepository::class => function (ContainerInterface $container) {
//         return new OrderItemRepository();
//     },

//     ImageRepository::class => function (ContainerInterface $container) {
//         return new ImageRepository();
//     },

//     WishlistRepository::class => function (ContainerInterface $container) {
//         return new WishlistRepository();
//     },

//     CartItemRepository::class => function (ContainerInterface $container) {
//         return new CartItemRepository();
//     },

//     VariantRepository::class => function (ContainerInterface $container) {
//         return new VariantRepository();
//     },

//     BundleRepository::class => function (ContainerInterface $container) {
//         return new BundleRepository();
//     },

//     PromoCodeRepository::class => function (ContainerInterface $container) {
//         return new PromoCodeRepository();
//     },

//     // You can also add aliases for easier access
//     'category_repository' => function (ContainerInterface $container) {
//         return $container->get(CategoryRepository::class);
//     },

//     'product_repository' => function (ContainerInterface $container) {
//         return $container->get(ProductRepository::class);
//     },

//     'user_repository' => function (ContainerInterface $container) {
//         return $container->get(UserRepository::class);
//     },

//     // Add more aliases as needed...
// ];