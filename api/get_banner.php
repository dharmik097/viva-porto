<?php
require_once '../config/Database.php';
require_once '../models/Banner.php';

// Set header for JSON response
header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$banner = new Banner($db);

// Get the banner ID from the query parameters
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Banner ID is required.']);
    exit; // Stop execution if no ID is provided
}

// Fetch the banner details
try {
    $bannerData = $banner->read($id); // Assuming readOne method exists in the Banner model

    if ($bannerData) {
        echo json_encode(['success' => true, 'banner' => $bannerData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Banner not found.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching banner details: ' . $e->getMessage()]);
}
?>
