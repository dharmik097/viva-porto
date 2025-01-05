<?php
class Message {
    private $conn;
    private $table_name = "messages";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (name, email, subject, message) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message']
        ]);
    }

    public function readAll() {
        $query = "SELECT id, name, email, subject, message, created_at, is_read FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all messages as an associative array
    }

    public function getUnreadCount() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function markAsRead($id) {
        $query = "UPDATE " . $this->table_name . " SET is_read = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
