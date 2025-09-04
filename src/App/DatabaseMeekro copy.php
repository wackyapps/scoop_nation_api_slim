<?php
// declare(strict_types=1);
// namespace App;

// require_once __DIR__ . '/meekrodb/db.class.php';
// require_once __DIR__ . '/constants.php';

// use DB; // Import the MeekroDB class

// class DatabaseMeekro
// {
//     private static $connection = null;
    
//     public static function getConnection()
//     {
//         if (self::$connection === null) {
//             self::initializeConnection();
//         }
        
//         return self::$connection;
//     }
    
//     private static function initializeConnection()
//     {
//         // Set MeekroDB configuration using constants from constants.php
//         DB::$host = DB_HOST;
//         DB::$user = DB_USER;
//         DB::$password = DB_PASS;
//         DB::$dbName = DB_NAME;
//         DB::$port = DB_PORT;
//         DB::$encoding = DB_CHARSET;
        
//         // Set error handling (optional)
//         DB::$error_handler = 'self::handleDatabaseError';
//         DB::$throw_exception_on_error = false;
        
//         self::$connection = new DB();
//     }
    
//     public static function handleDatabaseError($params)
//     {
//         //Log error or handle as needed
//         error_log("Database error: " . $params['error']);
        
//         if ($params['query']) {
//             error_log("Failed query: " . $params['query']);
//         }
        
//         // You might want to throw an exception here depending on your needs
//         if (DB::$throw_exception_on_error) {
//             throw new \Exception("Database error: " . $params['error']);
//         }
//     }
// }