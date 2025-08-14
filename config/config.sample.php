<?php
/**
 * Configuration file for BC Attendance System
 * Copy this to config.php and update values
 */

return [
    'app' => [
        'name' => 'BC Attendance System',
        'version' => '1.0.0',
        'debug' => false,
        'timezone' => 'Asia/Kolkata',
        'url' => 'http://localhost',
    ],
    
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'bc_attendance',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    
    'session' => [
        'name' => 'BC_ATTENDANCE_SESSION',
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ],
    
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'password_min_length' => 8,
        'login_max_attempts' => 5,
        'login_lockout_time' => 900, // 15 minutes
    ],
    
    'pagination' => [
        'default_per_page' => 20,
        'page_sizes' => [10, 20, 50, 100, 'all']
    ],
    
    'upload' => [
        'max_file_size' => 10485760, // 10MB
        'allowed_types' => ['xlsx', 'xls', 'csv'],
        'upload_path' => 'storage/uploads/imports/',
    ],
    
    'export' => [
        'path' => 'storage/uploads/exports/',
        'max_age' => 86400, // 24 hours
    ]
];
