<?php
/**
 * Administrator Login Controller & Page
 * Authenticates user credentials against the SQLite database.
 */

session_start();
$dbPath = dirname(__DIR__) . '/api/database.db';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (!file_exists($dbPath)) {
            $error = 'Database not found. Please run the <a href="../api/init.php">initializer</a> first.';
        } else {
            try {
                $db = new PDO("sqlite:" . $dbPath);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $query = "SELECT * FROM admins WHERE username = :username LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin && password_verify($password, $admin['password'])) {
                    // Start Session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Local Global Services</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F9FA;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(11, 53, 109, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            transition: all 0.3s ease;
        }
        
        .brand-logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: #0F172A;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .brand-logo span {
            color: #E31B23;
        }
        
        .btn-brand-primary {
            background-color: #0B58CA;
            color: #FFFFFF;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #0B58CA;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-brand-primary:hover {
            background-color: transparent;
            color: #0B58CA;
        }
        
        .form-control:focus {
            border-color: #0B58CA;
            box-shadow: 0 0 0 4px rgba(11, 88, 202, 0.1);
        }
        
        .btn-back-home {
            display: block;
            text-align: center;
            color: #475569;
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-back-home:hover {
            color: #0B58CA;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">Local<span>Global</span></div>
        <h5 class="text-center font-weight-bold mb-4" style="color: #0F172A;">Admin Portal Sign In</h5>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert" style="font-size: 0.9rem; border-radius: 8px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label font-weight-bold" style="font-size: 0.9rem; color: #475569;">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-regular fa-user"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" id="username" name="username" placeholder="Enter username" required autocomplete="off">
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <label for="password" class="form-label mb-0" style="font-size: 0.9rem; color: #475569;">Password</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control bg-light border-start-0" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-brand-primary">Sign In</button>
        </form>
        
        <a href="../index.html" class="btn-back-home"><i class="fa-solid fa-arrow-left me-1"></i> Return to Homepage</a>
    </div>

</body>
</html>
