<?php
declare(strict_types=1);

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ImageRepository;
use App\Repository\WishlistRepository;
use App\Repository\CartItemRepository;
use App\Repository\VariantRepository;
use App\Repository\BundleRepository;
use App\Repository\PromoCodeRepository;
use App\Repository\CustomerRepository; // ADD THIS LINE
use App\Controller\ProductController;
use App\Controller\BundleController;
use App\Controller\CustomerController; // ADD THIS LINE
use App\Controller\UserController; // ADD THIS LINE IF NOT ALREADY THERE
use Psr\Container\ContainerInterface;
use DI\Container;

return [
    // Repository dependencies
    CategoryRepository::class => function (Container $container) {
        return new CategoryRepository();
    },

    ProductRepository::class => function (Container $container) {
        return new ProductRepository();
    },

    UserRepository::class => function (Container $container) {
        return new UserRepository();
    },

    OrderRepository::class => function (Container $container) {
        return new OrderRepository();
    },

    OrderItemRepository::class => function (Container $container) {
        return new OrderItemRepository();
    },

    ImageRepository::class => function (Container $container) {
        return new ImageRepository();
    },

    WishlistRepository::class => function (Container $container) {
        return new WishlistRepository();
    },

    CartItemRepository::class => function (Container $container) {
        return new CartItemRepository();
    },

    VariantRepository::class => function (Container $container) {
        return new VariantRepository();
    },

    BundleRepository::class => function (Container $container) {
        return new BundleRepository();
    },

    PromoCodeRepository::class => function (Container $container) {
        return new PromoCodeRepository();
    },

    CustomerRepository::class => function (Container $container) { // ADD THIS ENTRY
        return new CustomerRepository();
    },

    // Controller dependencies
    ProductController::class => function (Container $container) {
        $productRepository = $container->get(ProductRepository::class);
        return new ProductController($productRepository);
    },

    BundleController::class => function (Container $container) {
        $bundleRepository = $container->get(BundleRepository::class);
        return new BundleController($bundleRepository);
    },

    CustomerController::class => function (Container $container) { // ADD THIS ENTRY
        $customerRepository = $container->get(CustomerRepository::class);
        return new CustomerController($customerRepository);
    },

    UserController::class => function (Container $container) { // ADD THIS ENTRY IF NOT ALREADY THERE
        $userRepository = $container->get(UserRepository::class);
        return new UserController($userRepository);
    },

    // Aliases
    'category_repository' => function (Container $container) {
        return $container->get(CategoryRepository::class);
    },

    'product_repository' => function (Container $container) {
        return $container->get(ProductRepository::class);
    },

    'bundle_repository' => function (Container $container) {
        return $container->get(BundleRepository::class);
    },

    'customer_repository' => function (Container $container) { // ADD THIS ENTRY
        return $container->get(CustomerRepository::class);
    },

    'user_repository' => function (Container $container) { // ADD THIS ENTRY IF NOT ALREADY THERE
        return $container->get(UserRepository::class);
    },

    'product_controller' => function (Container $container) {
        return $container->get(ProductController::class);
    },

    'bundle_controller' => function (Container $container) {
        return $container->get(BundleController::class);
    },

    'customer_controller' => function (Container $container) { // ADD THIS ENTRY
        return $container->get(CustomerController::class);
    },

    'user_controller' => function (Container $container) { // ADD THIS ENTRY IF NOT ALREADY THERE
        return $container->get(UserController::class);
    },
];