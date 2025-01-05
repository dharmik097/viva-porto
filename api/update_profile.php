<?php
require_once '../config/Database.php';
require_once '../models/User.php';

header('Content-Type: application/json');
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['current_password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Current password is required']);
    exit();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize user
$user = new User($db);

// Verify current password
if (!$user->verifyPassword($_SESSION['user_id'], $data['current_password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
    exit();
}

// Update password if provided
if (!empty($data['new_password'])) {
    if (strlen($data['new_password']) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'New password must be at least 6 characters long']);
        exit();
    }

    if (!$user->updatePassword($_SESSION['user_id'], $data['new_password'])) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update password']);
        exit();
    }
}

echo json_encode(['success' => true]);
