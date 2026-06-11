<?php
/**
 * Dynamic Industries Fetch API
 * Returns list of dynamic industries we empower sorted by sort_order.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/db_connect.php';

try {
    $db = getDatabaseConnection();
    
    $stmt = $db->query("SELECT * FROM industries ORDER BY sort_order ASC, id ASC");
    $industries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $industries
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
