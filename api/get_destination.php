<?php
require_once '../config/Database.php';
require_once '../models/Destination.php';

header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID is required']);
    exit();
}

$id = (int)$_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get destination
$destination = new Destination($db);
$data = $destination->read($id);

if ($data) {
    // Format the response to match the expected field names in JavaScript
    $response = [
        'id' => $data['id'],
        'name' => $data['name'],
        'category' => $data['category'],
        'shortDescription' => $data['short_description'],
        'description' => $data['description'],
        'imageUrl' => $data['image_url'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'is_featured' => (bool)$data['is_featured']
    ];
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Destination not found']);
}
