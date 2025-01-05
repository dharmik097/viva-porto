<?php
require_once '../config/Database.php';

header('Content-Type: application/json');
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Fetch all visitors
$query = "SELECT * FROM visitors ORDER BY visit_time DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'visitors' => $visitors
]);
