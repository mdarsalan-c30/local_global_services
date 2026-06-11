<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/db_connect.php';

try {
    $db = getDatabaseConnection();
    
    $stmt = $db->query("SELECT `key`, `value` FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo json_encode([
        'success' => true,
        'data' => $settings
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch settings: ' . $e->getMessage()
    ]);
}
?>
