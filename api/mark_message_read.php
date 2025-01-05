<?php
require_once '../config/Database.php';
require_once '../models/Message.php';

$database = new Database();
$db = $database->getConnection();
$message = new Message($db);

// Get the message ID from the request
$data = json_decode(file_get_contents("php://input"));
if (isset($data->id)) {
    $messageId = $data->id;

    // Call the method to mark the message as read
    if ($message->markAsRead($messageId)) {
        echo json_encode(["success" => true, "message" => "Message marked as read."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to mark message as read."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>