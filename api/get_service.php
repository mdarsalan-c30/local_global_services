<?php
/**
 * Dynamic Service Content API
 * Fetches specific submenu/service detail fields from the SQLite database using a key or comma-separated keys query.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$serviceKey = filter_input(INPUT_GET, 'key', FILTER_DEFAULT);
$serviceKeys = filter_input(INPUT_GET, 'keys', FILTER_DEFAULT);

if (!$serviceKey && !$serviceKeys) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid service key or keys parameter.']);
    exit;
}

$dbPath = __DIR__ . '/database.db';

if (!file_exists($dbPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not found.']);
    exit;
}

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($serviceKeys) {
        $keys = array_filter(array_map('trim', explode(',', $serviceKeys)));
        if (empty($keys)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No valid keys provided.']);
            exit;
        }
        
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $query = "SELECT * FROM submenus WHERE service_key IN ($placeholders) ORDER BY sort_order ASC, id ASC";
        $stmt = $db->prepare($query);
        $stmt->execute(array_values($keys));
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $results = [];
        foreach ($services as $service) {
            $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $service['features']))));
            $results[] = [
                'id' => (int)$service['id'],
                'menu_id' => (int)$service['menu_id'],
                'name' => $service['name'],
                'service_key' => $service['service_key'],
                'tagline' => $service['tagline'],
                'icon' => $service['icon'],
                'desc1' => $service['desc1'],
                'desc2' => $service['desc2'],
                'features' => $featuresArray,
                'banner_grad' => $service['banner_grad'],
                'sort_order' => (int)$service['sort_order'],
                'image_url' => $service['image_url']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $results
        ]);
        exit;
    } else {
        // Fetch target service details (single mode)
        $query = "SELECT * FROM submenus WHERE service_key = :service_key LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':service_key', $serviceKey);
        $stmt->execute();
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$service) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Service capability not found.']);
            exit;
        }
        
        $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $service['features']))));
        
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => (int)$service['id'],
                'menu_id' => (int)$service['menu_id'],
                'name' => $service['name'],
                'service_key' => $service['service_key'],
                'tagline' => $service['tagline'],
                'icon' => $service['icon'],
                'desc1' => $service['desc1'],
                'desc2' => $service['desc2'],
                'features' => $featuresArray,
                'banner_grad' => $service['banner_grad'],
                'sort_order' => (int)$service['sort_order'],
                'image_url' => $service['image_url']
            ]
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
