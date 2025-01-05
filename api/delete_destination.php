<?php
require_once '../config/Database.php';
require_once '../models/Destination.php';

header('Content-Type: application/json');
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing destination ID']);
    exit();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get destination details before deletion
$destination = new Destination($db);
$destinationData = $destination->read($data['id']);

if (!$destinationData) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Destination not found']);
    exit();
}

// Delete image file if it exists
if (!empty($destinationData['image_url'])) {
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/isla/wp/project' . $destinationData['image_url'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete destination from database
if ($destination->delete($data['id'])) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete destination']);
}
?>
