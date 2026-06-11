<?php
/**
 * Unified Database Connection Helper
 * Supports both SQLite (local development) and MySQL (production deployment).
 */

// Load external config file if it exists (e.g. on Hostinger production server)
if (file_exists(__DIR__ . '/db_config.php')) {
    require_once __DIR__ . '/db_config.php';
}

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
    
    if (DB_MODE === 'mysql') {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $db = new PDO($dsn, DB_USER, DB_PASS);
    } else {
        $db = new PDO("sqlite:" . $dbPath);
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
?>
