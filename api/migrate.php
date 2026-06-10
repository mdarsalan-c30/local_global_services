<?php
/**
 * Database Migration Utility
 * Performs necessary schema updates to add phone to submissions, author to blogs, and create testimonials/industries tables.
 */

$dbPath = __DIR__ . '/database.db';

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check submenus image_url column
    $stmt = $db->query("PRAGMA table_info(submenus)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasImageUrl = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'image_url') {
            $hasImageUrl = true;
            break;
        }
    }
    if (!$hasImageUrl) {
        $db->exec("ALTER TABLE submenus ADD COLUMN image_url TEXT DEFAULT NULL");
        echo "Migration Success: Added 'image_url' column to 'submenus' table.<br>\n";
    }

    // 2. Check submissions phone column
    $stmt = $db->query("PRAGMA table_info(submissions)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasPhone = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'phone') {
            $hasPhone = true;
            break;
        }
    }
    if (!$hasPhone) {
        $db->exec("ALTER TABLE submissions ADD COLUMN phone TEXT DEFAULT NULL");
        echo "Migration Success: Added 'phone' column to 'submissions' table.<br>\n";
    }

    // 3. Check blogs author column
    $stmt = $db->query("PRAGMA table_info(blogs)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasAuthor = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'author') {
            $hasAuthor = true;
            break;
        }
    }
    if (!$hasAuthor) {
        $db->exec("ALTER TABLE blogs ADD COLUMN author TEXT DEFAULT NULL");
        echo "Migration Success: Added 'author' column to 'blogs' table.<br>\n";
    }

    // 4. Create testimonials table
    $db->exec("CREATE TABLE IF NOT EXISTS testimonials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        client_name TEXT NOT NULL,
        service_name TEXT NOT NULL,
        testimonial_text TEXT NOT NULL,
        image_url TEXT DEFAULT NULL,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Migration Check: 'testimonials' table is ready.<br>\n";

    // 5. Create industries table
    $db->exec("CREATE TABLE IF NOT EXISTS industries (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        icon TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        features TEXT DEFAULT NULL,
        image_url TEXT DEFAULT NULL
    )");
    echo "Migration Check: 'industries' table is ready.<br>\n";

    // 5b. Check industries features column
    $stmt = $db->query("PRAGMA table_info(industries)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasFeatures = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'features') {
            $hasFeatures = true;
            break;
        }
    }
    if (!$hasFeatures) {
        $db->exec("ALTER TABLE industries ADD COLUMN features TEXT DEFAULT NULL");
        echo "Migration Success: Added 'features' column to 'industries' table.<br>\n";
    }

    // 5c. Check industries image_url column
    $hasIndImageUrl = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'image_url') {
            $hasIndImageUrl = true;
            break;
        }
    }
    if (!$hasIndImageUrl) {
        $db->exec("ALTER TABLE industries ADD COLUMN image_url TEXT DEFAULT NULL");
        echo "Migration Success: Added 'image_url' column to 'industries' table.<br>\n";
    }

    // 6. Seed Settings Table keys
    $db->exec("INSERT OR IGNORE INTO settings (key, value) VALUES 
        ('contact_phone', '+91-9718117270'),
        ('contact_email', 'sales@localglobal.com'),
        ('popup_status', 'hide'),
        ('popup_type', 'both'),
        ('popup_title', 'Welcome to Local Global Services!'),
        ('popup_text', 'Discover our next-generation SAP solutions and cognitive AI integrations.'),
        ('popup_image', ''),
        ('hero_bg_image', 'resources/hero-bg.png'),
        ('hero_bg_opacity', '0.20'),
        ('about_title', 'Who We Are'),
        ('about_subtitle', 'Your Trusted Technology Integrator & Digital Transformation Catalyst.'),
        ('about_desc_1', 'Local Global Services (LGS) bridges the gap between traditional operations and modern digital technology stacks. Backed by twenty years of global consulting experience, we help enterprises design, build, and support their core operational systems.'),
        ('about_desc_2', 'We focus on building customer-centric digital solutions using high-performing tools like SAP ERP, cellular IoT tracking, and cognitive AI tools. Our dedicated team of 500+ certified specialists is focused on driving tangible business outcomes, lower TCO, and unmatched agility.'),
        ('about_image', 'resources/wo-we-are.png')
    ");
    echo "Migration Check: 'settings' keys verified.<br>\n";

    // 7. Seed Testimonials Table if empty
    $testimonialsCount = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    if ($testimonialsCount == 0) {
        $db->exec("INSERT INTO testimonials (client_name, service_name, testimonial_text, sort_order) VALUES 
            ('Sarah Jenkins', 'SAP S/4HANA Cloud', 'LGS executed our global S/4HANA brownfield migration with zero operations downtime. Truly an elite Gold partner.', 10),
            ('Ahmed Al-Sayed', 'RISE with SAP & IoT', 'Our fleet operations OEE increased by 22% within 90 days of deploying LGS cellular tracking telemetry.', 20),
            ('Michael Chen', 'AI AP Invoice Automation', 'The cognitive AP invoice automation has reduced our manual invoice processing times by 80%. Outstanding ROI!', 30)
        ");
        echo "Migration Success: Seeded default 'testimonials'.<br>\n";
    }

    // 8. Seed Industries Table if empty
    $industriesCount = $db->query("SELECT COUNT(*) FROM industries")->fetchColumn();
    if ($industriesCount == 0) {
        $db->exec("INSERT INTO industries (name, title, description, icon, sort_order, features) VALUES 
            ('Manufacturing', 'Manufacturing', 'Optimize shop floor automation, implement advanced OEE dashboards, and track materials using localized SAP PLM modules.', 'fa-industry', 10, 'Optimize shop floor automation\nImplement advanced OEE dashboards\nTrack materials using localized SAP PLM'),
            ('Retail & Consumer', 'Retail & Consumer', 'Integrate unified omni-channel customer experiences, coordinate supply networks, and automate restocking channels.', 'fa-basket-shopping', 20, 'Integrate unified omni-channel CX\nCoordinate global supply networks\nAutomate restocking channels'),
            ('Energy & Utilities', 'Energy & Utilities', 'Ensure smart grid integrations, optimize resource exploration processes, and manage global asset life cycles.', 'fa-bolt', 30, 'Ensure smart grid integrations\nOptimize resource exploration\nManage global asset life cycles'),
            ('Logistics & Supply', 'Logistics & Supply', 'Maximize fleet utilization, integrate real-time cellular tracking telemetry, and lower overall warehousing overheads.', 'fa-truck-fast', 40, 'Maximize fleet utilization\nReal-time cellular tracking telemetry\nLower warehousing overheads')
        ");
        echo "Migration Success: Seeded default 'industries'.<br>\n";
    }

    echo "<strong>All database migrations completed successfully.</strong><br>\n";
    echo "<a href='../admin/dashboard.php'>Back to Admin Dashboard</a>";

} catch (PDOException $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
}
?>
