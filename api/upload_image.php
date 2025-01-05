<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No image uploaded']);
    exit;
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

// Get file extension
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Allowed extensions
$allowed = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($fileExt, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, JPEG, PNG, WEBP']);
    exit;
}

// Max file size (5MB)
if ($fileSize > 5000000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large. Max size: 5MB']);
    exit;
}

// Create uploads directory if it doesn't exist
$uploadDir = '../uploads/destinations/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$newFileName = uniqid('destination_') . '.' . $fileExt;
$uploadPath = $uploadDir . $newFileName;

// Move uploaded file
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'fileName' => $newFileName,
        'imageUrl' => '/uploads/destinations/' . $newFileName
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
}
