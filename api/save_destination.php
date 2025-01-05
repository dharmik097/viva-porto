<?php
session_start();
header('Content-Type: application/json');

// Check for admin session
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/Database.php';
require_once '../models/Destination.php';

$database = new Database();
$db = $database->getConnection();
$destination = new Destination($db);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

$data = [
    'name' => $input['name'] ?? '',
    'description' => $input['description'] ?? '',
    'short_description' => $input['shortDescription'] ?? '',
    'category' => $input['category'] ?? '',
    'image_url' => $input['imageUrl'] ?? '',
    'latitude' => $input['latitude'] ?? '',
    'longitude' => $input['longitude'] ?? '',
    'is_featured' => $input['is_featured'] ?? false
];

// Validate required fields
$required_fields = ['name', 'description', 'short_description', 'category', 'latitude', 'longitude'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit();
    }
}

// Update or Create
if (isset($input['id']) && !empty($input['id'])) {
    $data['id'] = $input['id'];
    $success = $destination->update($data);
    $message = 'Destination updated successfully';
} else {
    if (empty($data['image_url'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Image is required for new destinations']);
        exit();
    }
    $success = $destination->create($data);
    $message = 'Destination created successfully';
}

if ($success) {
    echo json_encode(['success' => true, 'message' => $message]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save destination']);
}
