<?php
session_start();
header('Content-Type: application/json');


require_once '../config/Database.php';
require_once '../models/Message.php';

$database = new Database();
$db = $database->getConnection();

$message = new Message($db);
$messages = $message->readAll(); // Assuming this returns an array of messages

// Check if messages were retrieved successfully
if ($messages) {
    echo json_encode(['success' => true, 'messages' => $messages]);
} else {
    echo json_encode(['success' => false, 'message' => 'No messages found']);
}
?>