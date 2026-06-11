<?php
/**
 * Contact Submission API Handler
 * Logs AJAX submissions into the SQLite database.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Get POST parameters
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
$service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// Validate parameters
if (!$name || !$email || !$phone || !$service || !$message) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide valid entries for all fields.'
    ]);
    exit;
}

$dbPath = __DIR__ . '/database.db';

// Verify database exists, if not initialize it silently
if (!file_exists($dbPath)) {
    try {
        // Initialize silently
        $db = new PDO("sqlite:" . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("CREATE TABLE IF NOT EXISTS submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT DEFAULT NULL,
            service TEXT NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            status TEXT DEFAULT 'Pending'
        )");
        $db->exec("CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        )");
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failure.']);
        exit;
    }
}

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insert Submission
    $query = "INSERT INTO submissions (name, email, phone, service, message) VALUES (:name, :email, :phone, :service, :message)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':service', $service);
    $stmt->bindParam(':message', $message);
    
    if ($stmt->execute()) {
        // Fetch contact email from settings table dynamically, fallback to sales@globals.com
        $to = 'sales@globals.com';
        try {
            $settingsQuery = $db->query("SELECT value FROM settings WHERE key = 'contact_email' LIMIT 1");
            $dbEmail = $settingsQuery->fetchColumn();
            if ($dbEmail && filter_var($dbEmail, FILTER_VALIDATE_EMAIL)) {
                $to = $dbEmail;
            }
        } catch (PDOException $se) {
            // Use fallback
        }

        $subject = 'New Lead Inquiry: ' . $service;
        
        $msg = '
        <html>
        <head>
            <title>New Lead Inquiry</title>
            <style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                    max-width: 600px;
                    font-family: "Segoe UI", Arial, sans-serif;
                    margin-top: 15px;
                }
                td, th {
                    border: 1px solid #e2e8f0;
                    text-align: left;
                    padding: 12px;
                }
                tr:nth-child(even) {
                    background-color: #f8fafc;
                }
                th {
                    background-color: #0b356d;
                    color: white;
                    font-weight: bold;
                }
                .header-text {
                    font-family: "Segoe UI", Arial, sans-serif;
                    color: #1e293b;
                }
            </style>
        </head>
        <body>
            <h2 class="header-text">New Website Inquiry Received</h2>
            <p class="header-text">A new lead has submitted a contact form on the LGS Web Portal. The details are listed below:</p>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><strong>Name</strong></td>
                    <td>' . htmlspecialchars($name) . '</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>' . htmlspecialchars($email) . '</td>
                </tr>
                <tr>
                    <td><strong>Phone Number</strong></td>
                    <td>' . htmlspecialchars($phone) . '</td>
                </tr>
                <tr>
                    <td><strong>Requested Service</strong></td>
                    <td>' . htmlspecialchars($service) . '</td>
                </tr>
                <tr>
                    <td><strong>Message Details</strong></td>
                    <td>' . nl2br(htmlspecialchars($message)) . '</td>
                </tr>
                <tr>
                    <td><strong>Submission Time</strong></td>
                    <td>' . date('Y-m-d H:i:s') . ' (Local Server Time)</td>
                </tr>
            </table>
            <br>
            <p class="header-text" style="font-size: 0.85rem; color: #64748b;">This inquiry has also been logged into the Admin Dashboard database.</p>
        </body>
        </html>
        ';

        // Set Content-type header for HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // Detect current domain dynamically to ensure matching "From" domain to bypass Hostinger anti-spam rules
        $host = $_SERVER['HTTP_HOST'] ?? 'localglobalservices.com';
        $host = preg_replace('/:\d+$/', '', $host); // Remove port if local
        if (substr($host, 0, 4) === 'www.') {
            $domain = substr($host, 4);
        } else {
            $domain = $host;
        }
        if ($domain === '127.0.0.1' || $domain === 'localhost') {
            $domain = 'localglobalservices.com';
        }

        // Sender details (using existing 'sales@' inbox to ensure high SPF/DKIM deliverability)
        $senderEmail = 'sales@' . $domain;
        $headers .= 'From: LGS Web Portal <' . $senderEmail . '>' . "\r\n";
        
        // Suppress warnings and use envelope sender (-f flag) to guarantee delivery on shared hosts
        $envelope = "-f" . $senderEmail;
        @mail($to, $subject, $msg, $headers, $envelope);

        echo json_encode([
            'success' => true,
            'message' => 'Inquiry registered successfully.'
        ]);
    } else {
        throw new Exception("Execution failed.");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error writing to database: ' . $e->getMessage()
    ]);
}
?>
