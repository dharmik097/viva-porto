<?php
require_once '../config/Database.php';
require_once '../models/Banner.php';

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
    echo json_encode(['success' => false, 'error' => 'Missing banner ID']);
    exit();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get banner details before deletion
$banner = new Banner($db);
$bannerData = $banner->read($data['id']);

if (!$bannerData) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Banner not found']);
    exit();
}

// Delete image file if it exists
if (!empty($bannerData['image_url'])) {
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/isla/wp/project' . $bannerData['image_url'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete banner from database
if ($banner->delete($data['id'])) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete banner']);
}
?>