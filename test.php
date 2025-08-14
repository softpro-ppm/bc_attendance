<?php
/**
 * BC Attendance System - Test File
 * Use this to verify PHP and basic functionality before installation
 */

echo "<h1>BC Attendance System - Test Page</h1>";

// Check PHP version
echo "<h2>PHP Information</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Required:</strong> 8.2+</p>";

if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    echo "<p style='color: green;'>✓ PHP version is compatible</p>";
} else {
    echo "<p style='color: red;'>✗ PHP version is too old</p>";
}

// Check required extensions
echo "<h2>Required Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'openssl'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ {$ext} extension is loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ {$ext} extension is missing</p>";
    }
}

// Check directory permissions
echo "<h2>Directory Permissions</h2>";
$directories = ['config', 'storage', 'storage/uploads', 'storage/logs'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✓ {$dir} directory is writable</p>";
        } else {
            echo "<p style='color: orange;'>⚠ {$dir} directory exists but is not writable</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ {$dir} directory does not exist</p>";
    }
}

// Check Composer autoloader
echo "<h2>Composer Autoloader</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "<p style='color: green;'>✓ Composer autoloader exists</p>";
    
    // Test autoloader
    try {
        require_once 'vendor/autoload.php';
        echo "<p style='color: green;'>✓ Composer autoloader works</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Composer autoloader error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Composer autoloader not found</p>";
    echo "<p>Run: <code>composer install</code></p>";
}

// Check configuration
echo "<h2>Configuration</h2>";
if (file_exists('config/config.php')) {
    echo "<p style='color: green;'>✓ Configuration file exists</p>";
} else {
    echo "<p style='color: orange;'>⚠ Configuration file not found (will be created during installation)</p>";
}

// Check .htaccess
echo "<h2>Web Server Configuration</h2>";
if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✓ .htaccess file exists</p>";
} else {
    echo "<p style='color: orange;'>⚠ .htaccess file not found</p>";
}

// Test database connection (if config exists)
if (file_exists('config/config.php')) {
    echo "<h2>Database Connection Test</h2>";
    try {
        $config = require_once 'config/config.php';
        $dsn = "mysql:host={$config['database']['host']};port={$config['database']['port']};dbname={$config['database']['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['database']['username'], $config['database']['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        
        // Test basic query
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "<p style='color: green;'>✓ Database query successful (Users: {$userCount})</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Next Steps</h2>";
if (file_exists('config/config.php')) {
    echo "<p>✓ System is installed. <a href='/'>Go to Dashboard</a></p>";
} else {
    echo "<p>⚠ System not installed. <a href='/install.php'>Run Installation</a></p>";
}

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
