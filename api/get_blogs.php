<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/db_connect.php';

try {
    $db = getDatabaseConnection();
    
    if (isset($_GET['id'])) {
        // Fetch by ID
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blog) {
            echo json_encode(['success' => true, 'data' => $blog]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Article not found.']);
        }
    } elseif (isset($_GET['slug'])) {
        // Fetch by Slug
        $stmt = $db->prepare("SELECT * FROM blogs WHERE slug = :slug");
        $stmt->bindParam(':slug', $_GET['slug'], PDO::PARAM_STR);
        $stmt->execute();
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($blog) {
            echo json_encode(['success' => true, 'data' => $blog]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Article not found.']);
        }
    } else {
        // Fetch list of all blogs ordered by date
        $stmt = $db->query("SELECT id, title, slug, summary, image_url, created_at, seo_title, meta_description, author FROM blogs ORDER BY created_at DESC");
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $blogs]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to query database: ' . $e->getMessage()
    ]);
}
?>
