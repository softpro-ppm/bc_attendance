<?php
echo "=== BC Attendance System - Comprehensive Test ===\n\n";

// Test 1: Check if all required files exist
echo "1. Checking required files...\n";
$requiredFiles = [
    'config/config.php',
    'app/Core/DB.php',
    'app/Core/Controller.php',
    'app/Core/Router.php',
    'app/Core/CSRF.php',
    'app/Controllers/AuthController.php',
    'app/Controllers/DashboardController.php',
    'public/index.php',
    'views/auth/login.php',
    'views/layout.php'
];

$allFilesExist = true;
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file (MISSING)\n";
        $allFilesExist = false;
    }
}

if (!$allFilesExist) {
    echo "\n❌ Some required files are missing!\n";
    exit(1);
}

echo "\n2. Testing configuration loading...\n";
try {
    $config = require_once 'config/config.php';
    if (is_array($config) && isset($config['database'])) {
        echo "   ✓ Configuration loaded successfully\n";
        echo "   ✓ Database driver: " . $config['database']['driver'] . "\n";
    } else {
        echo "   ✗ Configuration failed to load properly\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ✗ Configuration error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n3. Testing database connection...\n";
try {
    require_once 'vendor/autoload.php';
    $db = \App\Core\DB::getInstance($config);
    $pdo = $db->getConnection();
    
    // Test a simple query
    $result = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "   ✓ Database connection successful\n";
    echo "   ✓ Users table accessible (count: $result)\n";
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n4. Testing autoloader...\n";
try {
    $router = new \App\Core\Router();
    echo "   ✓ Router class loaded successfully\n";
    
    $controller = new \App\Core\Controller();
    echo "   ✓ Controller class loaded successfully\n";
    
    $csrf = new \App\Core\CSRF();
    echo "   ✓ CSRF class loaded successfully\n";
} catch (Exception $e) {
    echo "   ✗ Autoloader error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n5. Testing view rendering...\n";
try {
    // Create a test controller that can access protected methods
    class TestController extends \App\Core\Controller {
        public function testRender($view, $data = []) {
            return $this->render($view, $data);
        }
    }
    
    $testController = new TestController();
    $content = $testController->testRender('auth/login', ['errors' => [], 'oldInput' => []]);
    if (strlen($content) > 100) {
        echo "   ✓ View rendering successful\n";
        echo "   ✓ Login view loaded (" . strlen($content) . " characters)\n";
    } else {
        echo "   ✗ View rendering failed - content too short\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ✗ View rendering error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n6. Testing session functionality...\n";
try {
    session_start();
    $_SESSION['test'] = 'test_value';
    if (isset($_SESSION['test']) && $_SESSION['test'] === 'test_value') {
        echo "   ✓ Session functionality working\n";
    } else {
        echo "   ✗ Session functionality failed\n";
        exit(1);
    }
    session_destroy();
} catch (Exception $e) {
    echo "   ✗ Session error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== ALL TESTS PASSED! ===\n";
echo "\n✅ Your BC Attendance System is working correctly!\n";
echo "🌐 Access it at: http://localhost:8000\n";
echo "🔑 Login with: admin / admin123\n";
echo "\n📁 You can start the server with:\n";
echo "   - Windows: double-click start.bat\n";
echo "   - PowerShell: .\\start.ps1\n";
echo "   - Manual: php -S localhost:8000 -t public\n";
?>
