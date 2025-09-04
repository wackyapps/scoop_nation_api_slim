<?php
declare(strict_types=1);
namespace App;

require_once __DIR__ . '/meekrodb/db.class.php';
require_once __DIR__ . '/Constants.php';

class DatabaseMeekro
{
    private static $initialized = false;
    
    public static function initialize()
    {
        if (self::$initialized) {
            return;
        }
        
        try {
            // Set MeekroDB configuration using constants from constants.php
            \DB::$host = DB_HOST;
            \DB::$user = DB_USER;
            \DB::$password = DB_PASS;
            \DB::$dbName = DB_NAME;
            \DB::$port = DB_PORT;
            \DB::$encoding = DB_CHARSET;
            
            // Test the connection with a simple query
            \DB::query("SELECT 1");
            
            self::$initialized = true;
            
        } catch (\Exception $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    // This method is no longer needed as we use static calls directly
    // but keeping it for backward compatibility
    public static function getConnection()
    {
        self::initialize();
        return new \DB(); // Return instance for method chaining if needed
    }
}