<?php
/**
 * Database Connection Debug Diagnostic Script
 */

header('Content-Type: text/html; charset=utf-8');

// Load database connection helper
require_once __DIR__ . '/db_connect.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 5px solid #E31B23;'>";
echo "<h2 style='color: #0B356D; margin-top: 0;'>LGS Database Connection Diagnostic</h2>";

echo "<h3>1. Configuration Settings</h3>";
echo "<ul>";
echo "<li><strong>DB_MODE:</strong> <span style='background:#eee; padding:2px 6px; border-radius:4px; font-family:monospace;'>" . htmlspecialchars(DB_MODE) . "</span></li>";
echo "<li><strong>DB_HOST:</strong> " . htmlspecialchars(DB_HOST) . "</li>";
echo "<li><strong>DB_NAME:</strong> " . htmlspecialchars(DB_NAME) . "</li>";
echo "<li><strong>DB_USER:</strong> " . htmlspecialchars(DB_USER) . "</li>";
echo "<li><strong>db_config.php Loaded:</strong> " . (file_exists(__DIR__ . '/db_config.php') ? "<span style='color:green;'>Yes</span>" : "<span style='color:orange;'>No (using db_connect.php defaults)</span>") . "</li>";
echo "</ul>";

echo "<h3>2. Testing Connection</h3>";
try {
    $db = getDatabaseConnection();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p style='color:green; font-weight:bold;'>✓ Successfully connected to the database!</p>";
    echo "<p><strong>Active PDO Driver:</strong> " . htmlspecialchars($driver) . "</p>";
    
    echo "<h3>3. Tables Found in Database</h3>";
    $tables = [];
    if ($driver === 'mysql') {
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    if (empty($tables)) {
        echo "<p style='color:red;'>No tables found in this database. Please run the <a href='init.php'>initializer script</a>.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            // Count rows in the table
            $countStmt = $db->query("SELECT COUNT(*) FROM `$table`");
            $rowCount = $countStmt->fetchColumn();
            echo "<li><strong>$table:</strong> $rowCount rows</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color:red; font-weight:bold;'>✗ Database connection failed!</p>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<br><a href='../index.html' style='display: inline-block; background: #0B356D; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Back to Homepage</a>";
echo "<a href='init.php' style='display: inline-block; background: #E31B23; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Initializer</a>";
echo "</div>";
?>
