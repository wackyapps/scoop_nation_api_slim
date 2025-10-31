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
use App\Repository\CustomerRepository;
use App\Repository\EmailSubscriptionRepository;
use App\Repository\AddressRepository;
use App\Repository\SessionRepository;
use App\Services\EmailService;
use App\Services\OtpService;
use App\Services\FirebaseService;
use App\Controller\CategoryController; // ADD THIS LINE
use App\Controller\ProductController;
use App\Controller\BundleController;
use App\Controller\CustomerController;
use App\Controller\UserController;
use App\Controller\EmailSubscriptionController;
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

    CustomerRepository::class => function (Container $container) {
        return new CustomerRepository();
    },

    EmailSubscriptionRepository::class => function (Container $container) {
        return new EmailSubscriptionRepository();
    },

    AddressRepository::class => function (Container $container) {
        return new AddressRepository();
    },

    SessionRepository::class => function (Container $container) {
        return new SessionRepository();
    },

    // Service dependencies
    EmailService::class => function (Container $container) {
        return new EmailService();
    },

    OtpService::class => function (Container $container) {
        return new OtpService();
    },

    FirebaseService::class => function (Container $container) {
        return new FirebaseService();
    },

    // Controller dependencies
    CategoryController::class => function (Container $container) { // ADD THIS ENTRY
        $categoryRepository = $container->get(CategoryRepository::class);
        return new CategoryController($categoryRepository);
    },

    ProductController::class => function (Container $container) {
        $productRepository = $container->get(ProductRepository::class);
        return new ProductController($productRepository);
    },

    BundleController::class => function (Container $container) {
        $bundleRepository = $container->get(BundleRepository::class);
        return new BundleController($bundleRepository);
    },

    CustomerController::class => function (Container $container) {
        $customerRepository = $container->get(CustomerRepository::class);
        return new CustomerController($customerRepository);
    },

    UserController::class => function (Container $container) {
        $userRepository = $container->get(UserRepository::class);
        $wishlistRepository = $container->get(WishlistRepository::class);
        $addressRepository = $container->get(AddressRepository::class);
        $sessionRepository = $container->get(SessionRepository::class);
        $emailService = $container->get(EmailService::class);
        $otpService = $container->get(OtpService::class);
        $orderRepository = $container->get(OrderRepository::class);
        $customerRepository = $container->get(CustomerRepository::class);
        $orderItemRepository = $container->get(OrderItemRepository::class);
        $productRepository = $container->get(ProductRepository::class);
        $firebaseService = $container->get(FirebaseService::class);
        
        return new UserController(
            $userRepository,
            $wishlistRepository,
            $addressRepository,
            $sessionRepository,
            $emailService,
            $otpService,
            $orderRepository,
            $customerRepository,
            $orderItemRepository,
            $productRepository,
            $firebaseService
        );
    },

    EmailSubscriptionController::class => function (Container $container) {
        $subscriptionRepository = $container->get(EmailSubscriptionRepository::class);
        return new EmailSubscriptionController($subscriptionRepository);
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

    'customer_repository' => function (Container $container) {
        return $container->get(CustomerRepository::class);
    },

    'user_repository' => function (Container $container) {
        return $container->get(UserRepository::class);
    },

    'category_controller' => function (Container $container) { // ADD THIS ENTRY
        return $container->get(CategoryController::class);
    },

    'product_controller' => function (Container $container) {
        return $container->get(ProductController::class);
    },

    'bundle_controller' => function (Container $container) {
        return $container->get(BundleController::class);
    },

    'customer_controller' => function (Container $container) {
        return $container->get(CustomerController::class);
    },

    'user_controller' => function (Container $container) {
        return $container->get(UserController::class);
    },
];