<?php
// Check if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$url = require_once '../config/config.php';
try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    if ($isAjax) {
        // If it's an AJAX request, return JSON response
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    } else {
        // If it's a regular request, redirect to login page
        header('Location: '.$url.'/admin/login.php');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    
    if ($isAjax) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        echo json_encode([
            'success' => false,
            'message' => 'Error during logout: ' . $e->getMessage()
        ]);
    } else {
        // If it's a regular request, redirect to login page
        header('Location: '.$url.'/admin/login.php');
        exit;
    }
}
