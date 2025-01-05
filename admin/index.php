<?php
session_start();
require_once '../config/Database.php';
// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Log the visit
    $database = new Database();
    $db = $database->getConnection();
    $query = "INSERT INTO visitors (visit_time) VALUES (CURRENT_TIMESTAMP)";
    $db->prepare($query)->execute();
    
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
