<?php
require_once '../config/Database.php';
require_once '../models/Banner.php';

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['error' => 'Banner ID is required']);
    exit();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get banner
$banner = new Banner($db);
$bannerData = $banner->read($data['id']);

if (!$bannerData) {
    echo json_encode(['error' => 'Banner not found']);
    exit();
}

// Get the image path
$imagePath = '../' . ltrim($bannerData['image_url'], '/');

// Delete the physical file if it exists
if (file_exists($imagePath)) {
    unlink($imagePath);
}

// Update the database to remove the image reference
$updateData = [
    'id' => $data['id'],
    'image_url' => null
];

if ($banner->updateImage($updateData)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update database']);
}
