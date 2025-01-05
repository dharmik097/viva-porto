<?php
header('Content-Type: application/json');

require_once '../config/Database.php';
require_once '../models/Message.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$message = new Message($db);

$data = [
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'subject' => $_POST['subject'] ?? '',
    'message' => $_POST['message'] ?? ''
];

if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit();
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit();
}

if ($message->create($data)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving message']);
}
?>
