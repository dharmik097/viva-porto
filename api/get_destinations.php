<?php
require_once '../config/Database.php';
require_once '../models/Destination.php';

header('Content-Type: application/json');

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get destinations
$destination = new Destination($db);
$destinations = $destination->readAll();

// Fix image URLs to be absolute paths
foreach ($destinations as &$item) {
    if (!empty($item['image_url'])) {
        // Remove leading slash if present
        $item['image_url'] = '../' . ltrim($item['image_url'], '/');
    }
}

echo json_encode([
    'success' => true,
    'destinations' => $destinations
]);
