<?php
/**
 * Unified Database Connection Helper
 * Supports both SQLite (local development) and MySQL (production deployment).
 */

// Set to 'mysql' in production, or keep as 'sqlite' for local testing
if (!defined('DB_MODE')) {
    define('DB_MODE', 'sqlite'); 
}

// MySQL Production Credentials (used when DB_MODE is 'mysql')
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'your_hostinger_dbname');
if (!defined('DB_USER')) define('DB_USER', 'your_hostinger_dbuser');
if (!defined('DB_PASS')) define('DB_PASS', 'your_hostinger_dbpass');

/**
 * Returns a configured PDO database connection instance.
 */
function getDatabaseConnection() {
    $dbPath = __DIR__ . '/database.db';
    
    try {
        if (DB_MODE === 'mysql') {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $db = new PDO($dsn, DB_USER, DB_PASS);
        } else {
            $db = new PDO("sqlite:" . $dbPath);
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        // If connection fails, output JSON error to API or throw exception for dashboard
        if (php_sapi_name() !== 'cli' && !headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>
