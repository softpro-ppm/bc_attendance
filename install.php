<?php
/**
 * BC Attendance System - Installation Script
 * Run this script to set up the database and create the admin user
 */

// Check if already installed
if (file_exists('config/config.php')) {
    die('System already installed. Remove config/config.php to reinstall.');
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for form data
session_start();

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            // Database configuration
            $dbHost = $_POST['db_host'] ?? '';
            $dbPort = $_POST['db_port'] ?? 3306;
            $dbName = $_POST['db_name'] ?? '';
            $dbUser = $_POST['db_user'] ?? '';
            $dbPass = $_POST['db_password'] ?? '';
            
            if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
                $error = 'Please fill in all required fields.';
            } else {
                // Test database connection
                try {
                    $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbUser, $dbPass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Store in session for next step
                    $_SESSION['db_config'] = [
                        'host' => $dbHost,
                        'port' => $dbPort,
                        'database' => $dbName,
                        'username' => $dbUser,
                        'password' => $dbPass
                    ];
                    
                    header('Location: install.php?step=2');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Database connection failed: ' . $e->getMessage();
                }
            }
            break;
            
        case 2:
            // Admin user creation
            $adminUser = $_POST['admin_username'] ?? '';
            $adminPass = $_POST['admin_password'] ?? '';
            $adminEmail = $_POST['admin_email'] ?? '';
            $adminName = $_POST['admin_full_name'] ?? '';
            
            if (empty($adminUser) || empty($adminPass) || empty($adminEmail)) {
                $error = 'Please fill in all required fields.';
            } elseif (strlen($adminPass) < 8) {
                $error = 'Password must be at least 8 characters long.';
            } else {
                try {
                    $dbConfig = $_SESSION['db_config'];
                    
                    // Create database if it doesn't exist
                    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $pdo->exec("USE `{$dbConfig['database']}`");
                    
                    // Run migrations
                    $migrations = [
                        'database/migrations/001_init.sql',
                        'database/migrations/002_seed_minimal.sql'
                    ];
                    
                    foreach ($migrations as $migration) {
                        if (file_exists($migration)) {
                            $sql = file_get_contents($migration);
                            $pdo->exec($sql);
                        }
                    }
                    
                    // Create admin user
                    $passwordHash = password_hash($adminPass, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (username, password_hash, email, full_name) VALUES (?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$adminUser, $passwordHash, $adminEmail, $adminName]);
                    
                    // Create config file
                    $configContent = generateConfigFile($dbConfig);
                    file_put_contents('config/config.php', $configContent);
                    
                    // Create storage directories
                    $directories = [
                        'storage/uploads/imports',
                        'storage/uploads/exports',
                        'storage/logs'
                    ];
                    
                    foreach ($directories as $dir) {
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }
                    }
                    
                    // Create .htaccess for storage protection
                    $htaccessContent = "Deny from all\n";
                    file_put_contents('storage/.htaccess', $htaccessContent);
                    
                    $success = 'Installation completed successfully!';
                    $step = 3;
                    
                } catch (Exception $e) {
                    $error = 'Installation failed: ' . $e->getMessage();
                }
            }
            break;
    }
}

function generateConfigFile($dbConfig) {
    $config = require_once 'config/config.sample.php';
    $config['database']['host'] = $dbConfig['host'];
    $config['database']['port'] = $dbConfig['port'];
    $config['database']['database'] = $dbConfig['database'];
    $config['database']['username'] = $dbConfig['username'];
    $config['database']['password'] = $dbConfig['password'];
    
    $content = "<?php\n";
    $content .= "/**\n";
    $content .= " * Configuration file for BC Attendance System\n";
    $content .= " * Generated by installer\n";
    $content .= " */\n\n";
    $content .= "return " . var_export($config, true) . ";\n";
    
    return $content;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BC Attendance System - Installation</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #6750A4 0%, #625B71 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .installer {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: #6750A4;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background: #6750A4;
            color: white;
        }
        .step.completed {
            background: #4CAF50;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6750A4;
        }
        .btn {
            background: #6750A4;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5a4a8a;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
        }
        .success {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
        }
        .requirements {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .requirements h3 {
            margin-top: 0;
            color: #333;
        }
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .requirement .status {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .requirement .status.ok {
            background: #4CAF50;
        }
        .requirement .status.error {
            background: #f44336;
        }
        .login-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #bbdefb;
        }
        .login-info h3 {
            margin-top: 0;
            color: #1976d2;
        }
        .login-details {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #bbdefb;
        }
        .login-details strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="installer">
        <div class="header">
            <h1>BC Attendance System</h1>
            <p>Installation Wizard</p>
        </div>
        
        <div class="content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?= $step >= 1 ? 'active' : '' ?>">1</div>
                <div class="step <?= $step >= 2 ? 'active' : '' ?>">2</div>
                <div class="step <?= $step >= 3 ? 'completed' : '' ?>">3</div>
            </div>
            
            <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($step == 1): ?>
            <!-- Step 1: Database Configuration -->
            <h2>Database Configuration</h2>
            <p>Please provide your MySQL database connection details.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Database Host *</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="db_port">Database Port</label>
                    <input type="number" id="db_port" name="db_port" value="3306" min="1" max="65535">
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name *</label>
                    <input type="text" id="db_name" name="db_name" value="bc_attendance" required>
                </div>
                
                <div class="form-group">
                    <label for="db_user">Database Username *</label>
                    <input type="text" id="db_user" name="db_user" required>
                </div>
                
                <div class="form-group">
                    <label for="db_password">Database Password</label>
                    <input type="password" id="db_password" name="db_password">
                </div>
                
                <button type="submit" class="btn">Continue</button>
            </form>
            
            <?php elseif ($step == 2): ?>
            <!-- Step 2: Admin User Creation -->
            <h2>Admin User Creation</h2>
            <p>Create the administrator account for the system.</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="admin_username">Username *</label>
                    <input type="text" id="admin_username" name="admin_username" value="admin" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_password">Password *</label>
                    <input type="password" id="admin_password" name="admin_password" required minlength="8">
                    <small>Password must be at least 8 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Email *</label>
                    <input type="email" id="admin_email" name="admin_email" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_full_name">Full Name</label>
                    <input type="text" id="admin_full_name" name="admin_full_name" value="System Administrator">
                </div>
                
                <button type="submit" class="btn">Install System</button>
            </form>
            
            <?php elseif ($step == 3): ?>
            <!-- Step 3: Installation Complete -->
            <h2>Installation Complete!</h2>
            <p>The BC Attendance System has been successfully installed.</p>
            
            <div class="login-info">
                <h3>Login Information</h3>
                <div class="login-details">
                    <p><strong>Username:</strong> <?= htmlspecialchars($_POST['admin_username'] ?? 'admin') ?></p>
                    <p><strong>Password:</strong> The password you entered during installation</p>
                    <p><strong>Login URL:</strong> <a href="/login"><?= $_SERVER['REQUEST_SCHEME'] ?>://<?= $_SERVER['HTTP_HOST'] ?>/login</a></p>
                </div>
            </div>
            
            <div class="requirements">
                <h3>Next Steps</h3>
                <ul>
                    <li>Delete the <code>install.php</code> file for security</li>
                    <li>Log in to the system using your admin credentials</li>
                    <li>Configure your constituencies, mandals, and batches</li>
                    <li>Add candidates to your batches</li>
                    <li>Start marking attendance!</li>
                </ul>
            </div>
            
            <a href="/login" class="btn">Go to Login</a>
            
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
