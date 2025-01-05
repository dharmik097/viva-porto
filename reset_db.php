<?php
header('Content-Type: text/plain');

try {
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    echo "Connected to MySQL successfully\n";

    // Drop existing database
    $pdo->exec("DROP DATABASE IF EXISTS vivaporto");
    echo "Dropped existing database\n";

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database/vivaporto.sql');
    $pdo->exec($sql);
    echo "Database recreated successfully\n";

    // Verify admin user
    $pdo = new PDO(
        "mysql:host=localhost;dbname=vivaporto",
        "root",
        "",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    $stmt = $pdo->query("SELECT * FROM users WHERE username = 'admin'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "\nAdmin user verified:\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Is Admin: " . ($user['is_admin'] ? 'Yes' : 'No') . "\n";
        
        // Test password verification
        $testPassword = 'admin123';
        $verified = password_verify($testPassword, $user['password']);
        echo "\nPassword verification test: " . ($verified ? 'PASSED' : 'FAILED') . "\n";
    } else {
        echo "\nError: Admin user not found\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
