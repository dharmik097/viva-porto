<?php
require_once '../config/Database.php';
require_once '../models/Banner.php';

header('Content-Type: application/json');

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get banners
$banner = new Banner($db);
$banners = $banner->readAll();

// Fix image URLs to be absolute paths
foreach ($banners as &$item) {
    if (!empty($item['image_url'])) {
        // Remove leading slash if present
        $item['image_url'] = '../' . ltrim($item['image_url'], '/');
    }
}

echo json_encode([
    'success' => true,
    'banners' => $banners
]);
?>
