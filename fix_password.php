<?php
header('Content-Type: text/plain');

try {
    // Generate new password hash
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Generated new hash for testing:\n";
    echo "Password: " . $password . "\n";
    echo "Hash: " . $hash . "\n";
    
    // Verify the hash works
    $verified = password_verify($password, $hash);
    echo "\nVerification test: " . ($verified ? 'PASSED' : 'FAILED') . "\n";
    
    if ($verified) {
        // Connect to database
        $pdo = new PDO(
            "mysql:host=localhost;dbname=vivaporto",
            "root",
            "",
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        
        // Update the admin password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->execute([$hash]);
        
        echo "\nDatabase updated successfully\n";
        
        // Verify the update
        $stmt = $pdo->query("SELECT password FROM users WHERE username = 'admin'");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $verifyAgain = password_verify($password, $user['password']);
            echo "Final verification test: " . ($verifyAgain ? 'PASSED' : 'FAILED') . "\n";
            
            if ($verifyAgain) {
                echo "\nLogin credentials are now:\n";
                echo "Username: admin\n";
                echo "Password: admin123\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
