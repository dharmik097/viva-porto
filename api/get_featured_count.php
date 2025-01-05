<?php
require_once '../config/Database.php';
require_once '../models/Destination.php';

header('Content-Type: application/json');

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get featured count
$destination = new Destination($db);
$count = $destination->getFeaturedCount();

echo json_encode([
    'success' => true,
    'count' => $count
]);
