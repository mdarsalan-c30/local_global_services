<?php
/**
 * Dynamic Navbar Hierarchy API
 * Fetches menus and nested submenus from the SQLite database and returns a structured JSON.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/db_connect.php';

try {
    $db = getDatabaseConnection();
    
    // Fetch all parent menus sorted by sort_order
    $menusQuery = $db->query("SELECT * FROM menus ORDER BY sort_order ASC, id ASC");
    $menus = $menusQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $menuTree = [];
    
    // Prepare dynamic nested structure
    foreach ($menus as $menu) {
        $menuId = $menu['id'];
        
        // Fetch matching submenus
        $subQuery = $db->prepare("SELECT id, name, service_key, icon, image_url FROM submenus WHERE menu_id = :menu_id ORDER BY sort_order ASC, id ASC");
        $subQuery->bindParam(':menu_id', $menuId, PDO::PARAM_INT);
        $subQuery->execute();
        $submenus = $subQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $menuTree[] = [
            'id' => (int)$menuId,
            'name' => $menu['name'],
            'submenus' => $submenus
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $menuTree
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
