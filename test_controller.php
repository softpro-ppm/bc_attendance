<?php
// Simulate Controller context
chdir(__DIR__ . '/app/Core');

echo "Current directory: " . getcwd() . "\n";

$configPath = __DIR__ . '/../../config/config.php';
echo "Config path: $configPath\n";
echo "File exists: " . (file_exists($configPath) ? 'Yes' : 'No') . "\n";

if (file_exists($configPath)) {
    echo "File size: " . filesize($configPath) . " bytes\n";
    
    // Try to read the file content
    $content = file_get_contents($configPath);
    echo "Content length: " . strlen($content) . " bytes\n";
    echo "First 100 chars: " . substr($content, 0, 100) . "\n";
    
    // Try to require it
    $config = require_once $configPath;
    echo "Config loaded: " . (is_array($config) ? 'Yes' : 'No') . "\n";
    if (is_array($config)) {
        echo "Config keys: " . implode(', ', array_keys($config)) . "\n";
    } else {
        echo "Config type: " . gettype($config) . "\n";
        echo "Config value: " . var_export($config, true) . "\n";
    }
}
?>
