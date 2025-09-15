Based on the provided MySQL schema, here is the `sql_table_names.php` file. It defines constants for each table found in the schema, following the pattern established in the example code you provided.

```php
<?php
// Banner and Campaign Tables
define("TABLE_BANNER_CAMPAIGN", "banner_campaign");

// Branch Management Tables
define("TABLE_BRANCH", "branch");
define("TABLE_BRANCH_PRODUCT", "branch_product");
define("TABLE_BRANCH_SPECIAL_DAYS", "branch_special_days");
define("TABLE_BRANCH_TIMINGS", "branch_timings");

// Business Tables
define("TABLE_BUSINESS", "business");

// Cart Tables
define("TABLE_CART", "cart");
define("TABLE_CART_ITEM", "cart_item");

// Product and Inventory Tables
define("TABLE_CATEGORY", "category");
define("TABLE_PRODUCT", "product");
define("TABLE_VARIANT", "variant");
define("TABLE_BUNDLE", "bundle");
define("TABLE_BUNDLE_PRODUCT", "bundle_product");

// Media Tables
define("TABLE_MEDIA", "media");
define("TABLE_MEDIA_META", "media_meta");

// Order Management Tables
define("TABLE_ORDER", "order");
define("TABLE_ORDER_ITEM", "order_item");

// Customer and User Tables
define("TABLE_CUSTOMER", "customer");
define("TABLE_USER", "user"); // Assuming 'user' table exists based on foreign keys
define("TABLE_ADDRESSES", "addresses");
define("TABLE_WISHLIST", "wishlist");
define("TABLE_FAVORITE_PRODUCTS", "favorite_products");

// Delivery Tables
define("TABLE_RIDER", "rider");

// Promotions and Pricing
define("TABLE_PROMOCODE", "promocode");

// System and Location Tables
define("TABLE_CITIES", "cities");
define("TABLE_COUNTRIES", "countries");
define("TABLE_PERMISSIONS", "permissions"); // Included from original example
define("TABLE_ROLES", "roles");
define("TABLE_USER_ROLES", "user_roles");
define("TABLE_SPACE", "space"); // Included from original example
define("TABLE_USERS_LOGINS", "user_logins"); // Included from original example