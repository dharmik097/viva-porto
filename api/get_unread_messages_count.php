<?php
require_once '../config/Database.php';
require_once '../models/Message.php';

$database = new Database();
$db = $database->getConnection();
$message = new Message($db);

// Get the count of unread messages
$unreadCount = $message->getUnreadCount(); // Ensure this method exists in your Message model

echo json_encode(['count' => $unreadCount]);
?>