<?php
require_once '../config/Database.php';
require_once '../models/Banner.php';

header('Content-Type: application/json');
session_start();

// Check for admin session
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

$data = [
    'title' => $input['title'] ?? '',
    'image_url' => $input['imageUrl'] ?? ''
];

// Log input data for debugging
error_log('Input data: ' . print_r($data, true));

// Validate required fields
$required_fields = ['title', 'image_url'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit();
    }
}

$database = new Database();
$db = $database->getConnection();
$banner = new Banner($db);

// Update or Create
if (isset($input['id']) && !empty($input['id'])) {
    $data['id'] = $input['id'];
    $success = $banner->update($data);
    // Log success status for debugging
    error_log('Update success: ' . ($success ? 'true' : 'false'));
    $message = 'Banner updated successfully';
} else {
    if (empty($data['image_url'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Image is required for new banners']);
        exit();
    }
    $success = $banner->create($data);
    // Log success status for debugging
    error_log('Create success: ' . ($success ? 'true' : 'false'));
    $message = 'Banner created successfully';
}

if ($success) {
    echo json_encode(['success' => true, 'message' => $message]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save banner']);
}
