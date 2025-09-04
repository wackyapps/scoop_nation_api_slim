<?php
require dirname(__DIR__) . '/src/App/Constants.php';
require dirname(__DIR__) . '/src/App/meekrodb/db.class.php';

header('Content-Type: application/json');

try {
    // Configure MeekroDB
    DB::$host = DB_HOST;
    DB::$user = DB_USER;
    DB::$password = DB_PASS;
    DB::$dbName = DB_NAME;
    DB::$port = DB_PORT;
    DB::$encoding = DB_CHARSET;
    
    // Test connection
    $result = DB::query("SELECT COUNT(*) as count FROM product");
    
    echo json_encode([
        'success' => true,
        'message' => 'Direct MeekroDB connection successful',
        'product_count' => $result[0]['count']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'constants_used' => [
            'host' => DB_HOST,
            'user' => DB_USER,
            'db' => DB_NAME,
            'port' => DB_PORT
        ]
    ]);
}