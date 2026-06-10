<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dbPath = __DIR__ . '/database.db';

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $db->query("SELECT key, value FROM settings");
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
