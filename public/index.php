<?php
/**
 * BC Attendance System - Front Controller
 * Routes all requests to appropriate controllers
 */

// Start session
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = require_once __DIR__ . '/../config/config.php';

// Initialize router
$router = new \App\Core\Router();

// Define routes
$router->get('/', 'DashboardController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Attendance
$router->get('/attendance', 'AttendanceController@index');
$router->get('/attendance/mark', 'AttendanceController@markAttendance');
$router->post('/attendance/save', 'AttendanceController@saveAttendance');
$router->get('/attendance/view/{id}', 'AttendanceController@view');
$router->get('/attendance/edit/{id}', 'AttendanceController@edit');
$router->post('/attendance/update/{id}', 'AttendanceController@update');
$router->delete('/attendance/{id}', 'AttendanceController@delete');

// Reports
$router->get('/reports', 'ReportsController@index');
$router->get('/reports/daily', 'ReportsController@daily');
$router->get('/reports/batch', 'ReportsController@batch');
$router->get('/reports/export', 'ReportsController@export');

// Settings
$router->get('/settings', 'SettingsController@index');
$router->post('/settings/update', 'SettingsController@update');

// Master Data Management
$router->get('/constituencies', 'EntitiesController@constituencies');
$router->post('/constituencies', 'EntitiesController@createConstituency');
$router->put('/constituencies/{id}', 'EntitiesController@updateConstituency');
$router->delete('/constituencies/{id}', 'EntitiesController@deleteConstituency');

$router->get('/mandals', 'EntitiesController@mandals');
$router->post('/mandals', 'EntitiesController@createMandal');
$router->put('/mandals/{id}', 'EntitiesController@updateMandal');
$router->delete('/mandals/{id}', 'EntitiesController@deleteMandal');

$router->get('/batches', 'EntitiesController@batches');
$router->post('/batches', 'EntitiesController@createBatch');
$router->put('/batches/{id}', 'EntitiesController@updateBatch');
$router->delete('/batches/{id}', 'EntitiesController@deleteBatch');

$router->get('/candidates', 'EntitiesController@candidates');
$router->post('/candidates', 'EntitiesController@createCandidate');
$router->put('/candidates/{id}', 'EntitiesController@updateCandidate');
$router->delete('/candidates/{id}', 'EntitiesController@deleteCandidate');

// API endpoints for dynamic dropdowns
$router->get('/api/mandals', 'ApiController@getMandals');
$router->get('/api/batches', 'ApiController@getBatches');
$router->get('/api/candidates', 'ApiController@getCandidates');
$router->get('/api/attendance', 'ApiController@getAttendance');

// Import/Export
$router->get('/import', 'ImportExportController@showImport');
$router->post('/import', 'ImportExportController@import');
$router->get('/export', 'ImportExportController@showExport');
$router->post('/export', 'ImportExportController@export');

// 404 handler
$router->notFound(function() {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>404 - Page Not Found</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            .error { color: #666; font-size: 72px; margin-bottom: 20px; }
            .message { color: #333; font-size: 24px; margin-bottom: 20px; }
            .back { color: #0066cc; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="error">404</div>
        <div class="message">Page Not Found</div>
        <a href="/" class="back">Go Back Home</a>
    </body>
    </html>';
});

// Dispatch the request
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    // Handle PUT/DELETE requests from forms
    if ($method === 'POST' && isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }
    
    $router->dispatch($method, $uri);
    
} catch (Exception $e) {
    if ($config['app']['debug']) {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
}
