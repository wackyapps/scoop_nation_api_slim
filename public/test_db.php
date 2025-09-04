<?php
require dirname(__DIR__) . '/src/App/Constants.php';
require dirname(__DIR__) . '/src/App/meekrodb/db.class.php';
require dirname(__DIR__) . '/src/App/DatabaseMeekro.php';

use App\DatabaseMeekro;

header('Content-Type: application/json');

try {
    // Initialize the database connection
    DatabaseMeekro::initialize();
    
    // Test the connection with a simple query using static call
    $result = \DB::queryFirstField("SELECT COUNT(*) FROM product");
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'product_count' => $result
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'details' => [
            'host' => DB_HOST,
            'database' => DB_NAME,
            'user' => DB_USER
        ]
    ]);
}