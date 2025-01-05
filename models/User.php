<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate($username, $password) {
        try {
            echo "Starting authentication for user: {$username}\n";

            $query = "SELECT id, username, password, is_admin FROM " . $this->table_name . " 
                     WHERE username = ? AND is_admin = 1 LIMIT 1";
            
            echo "Preparing query: {$query}\n";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                echo "Statement preparation failed\n";
                return ['success' => false, 'error' => 'Statement preparation failed'];
            }

            echo "Executing query with username: {$username}\n";
            $stmt->execute([$username]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                echo "No user found with username: {$username}\n";
                return ['success' => false, 'error' => 'User not found'];
            }

            echo "User found, verifying password\n";
            echo "Stored hash: " . $user['password'] . "\n";
            
            // Test password verification
            $verified = password_verify($password, $user['password']);
            echo "Password verification result: " . ($verified ? 'true' : 'false') . "\n";

            if ($verified) {
                echo "Password verified successfully\n";
                return [
                    'success' => true,
                    'user_id' => $user['id'],
                    'username' => $user['username']
                ];
            }

            echo "Password verification failed\n";
            return ['success' => false, 'error' => 'Invalid password'];

        } catch (Exception $e) {
            echo "Authentication error: " . $e->getMessage() . "\n";
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT id, username, email, is_admin FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return false;
        }
    }

    public function verifyPassword($userId, $password) {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return password_verify($password, $row['password']);
        }
        
        return false;
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function getTotalVisitors() {
        $query = "SELECT COUNT(*) as count FROM visitors";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['count'];
    }
}
