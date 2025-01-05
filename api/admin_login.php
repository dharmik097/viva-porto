<?php
// Start session at the beginning
session_start();


// Check if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

require_once '../config/Database.php';
require_once '../models/User.php';
$url = require_once '../config/config.php';

$debug_info = [];

try {
    // Get posted data
    if ($isAjax) {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username ?? '';
        $password = $data->password ?? '';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
    }
    
    if (empty($username) || empty($password)) {
        throw new Exception('Username and password are required');
    }

    $debug_info[] = "Starting authentication for user: " . $username;
    
    // Initialize database
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize user
    $user = new User($db);
    
    // Query to check user
    $query = "SELECT id, username, password, is_admin FROM users 
                     WHERE username = ? AND is_admin = 1 LIMIT 1";
    
    $debug_info[] = "Preparing query: " . $query;
    
    $stmt = $db->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Query preparation failed');
    }
    
    $debug_info[] = "Executing query with username: " . $username;
    
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $debug_info[] = "User found, verifying password";
        
        $verified = password_verify($password, $row['password']);
        $debug_info[] = "Password verification result: " . ($verified ? "true" : "false");
        
        if ($verified) {
            $debug_info[] = "Password verified successfully";
            
            // Store user data in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['is_admin'] = $row['is_admin'];
            $_SESSION['logged_in'] = true;
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'is_admin' => (bool)$row['is_admin']
                    ]
                ]);
            } else {
                // Redirect to dashboard for regular form submission
                header('Location: '.$url.'/admin/dashboard.php');
                exit;
            }
            exit;
        }
    }
    
    // If we get here, authentication failed
    throw new Exception('Invalid username or password');
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage() . "\nDebug info: " . implode("\n", $debug_info));
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } else {
        // For regular form, redirect back to login with error
        header('Location: '.$url.'/admin/login.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
