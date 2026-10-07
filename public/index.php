<?php
// public/index.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config constants
require_once __DIR__ . '/../app/Config/config.php';

// PSR-4 Autoloader implementation
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load routes and dispatch
$router = require_once __DIR__ . '/../app/Routes/web.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = urldecode($_SERVER['REQUEST_URI']);

// Dynamically determine the base directory path from SCRIPT_NAME
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g. "/Projects All/per/mad/public/index.php"
$basePath = rtrim(dirname($scriptName), '/\\');

if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Remove query parameters for matching routes
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}

$requestUri = rtrim($requestUri, '/');
if (empty($requestUri)) {
    $requestUri = '/';
}

$router->dispatch($requestMethod, $requestUri);

