<?php
// app/Config/config.php

define('APP_NAME', 'Megawin QMS');
// Calculate BASE_URL dynamically
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = rtrim(dirname($scriptName), '/\\');
define('BASE_URL', $baseUrl);

define('UPLOAD_DIR', __DIR__ . '/../../storage/uploads/');
define('LOG_DIR', __DIR__ . '/../../storage/logs/');
define('QR_DIR', __DIR__ . '/../../storage/qr/');

// Roles constant
define('ROLES', [
    'super_admin' => 'Super Admin',
    'admin' => 'Admin',
    'quality_manager' => 'Quality Manager',
    'quality_engineer' => 'Quality Engineer',
    'production_engineer' => 'Production Engineer',
    'store' => 'Store',
    'purchase' => 'Purchase',
    'customer' => 'Customer',
    'viewer' => 'Viewer'
]);
