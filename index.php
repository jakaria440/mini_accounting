<?php
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Router function
function route($uri) {
    $uri = trim($uri, '/');
    
    // Routes configuration
    $routes = [
        '' => 'pages/home.php',
        'about' => 'pages/about.php',
        'contact' => 'pages/contact.php',
        'application' => 'pages/application.php',
        'policy' => 'pages/policy.php',
        'applications' => 'pages/application_list.php',
        'login' => 'pages/login.php',
        'deposit' => 'pages/deposit.php',
        'logout' => 'pages/logout.php',
        'profile' => 'pages/profile.php',
    ];
    
    // Check if route exists
    if (array_key_exists($uri, $routes)) {
        require_once BASE_PATH . '/' . $routes[$uri];
    } else {
        http_response_code(404);
        require_once BASE_PATH . '/pages/404.php';
    }
}

// Get current URI
$uri = $_SERVER['REQUEST_URI'];
$uri = strtok($uri, '?'); // Remove query string if any
route($uri);