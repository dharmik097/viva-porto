<?php
header('Content-Type: application/json');

require_once '../config/Database.php';
require_once '../models/Destination.php';

$database = new Database();
$db = $database->getConnection();

$destination = new Destination($db);
$locations = $destination->readAll();

if ($locations) {
    echo json_encode($locations);
} else {
    echo json_encode(["message" => "No locations found."]);
}
?>