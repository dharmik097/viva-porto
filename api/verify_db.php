<?php
header('Content-Type: application/json');

require_once '../config/Database.php';

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    $response = ["status" => "checking"];
    
    // Check users table
    $stmt = $db->query("SELECT * FROM users WHERE username = 'admin'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $response["found_user"] = true;
        $response["user_info"] = [
            "username" => $user['username'],
            "is_admin" => (bool)$user['is_admin'],
            "current_hash" => $user['password']
        ];
        
        // Test password verification
        $password = 'admin123';
        $verified = password_verify($password, $user['password']);
        $response["password_verify"] = $verified;
        
        if (!$verified) {
            // Create new password hash
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $response["new_hash"] = $newHash;
            
            // Verify new hash works
            $verifyNew = password_verify($password, $newHash);
            $response["new_hash_verify"] = $verifyNew;
            
            if ($verifyNew) {
                // Update database with new hash
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
                $stmt->execute([$newHash]);
                $response["database_updated"] = true;
                
                // Verify update
                $stmt = $db->query("SELECT password FROM users WHERE username = 'admin'");
                $updated = $stmt->fetch(PDO::FETCH_ASSOC);
                $finalVerify = password_verify($password, $updated['password']);
                $response["final_verify"] = $finalVerify;
            }
        }
    } else {
        $response["found_user"] = false;
        
        // Create admin user
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute(['admin', $hash, 'admin@vivaporto.com']);
        $response["created_user"] = true;
        $response["new_hash"] = $hash;
    }
    
    $response["success"] = true;
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
