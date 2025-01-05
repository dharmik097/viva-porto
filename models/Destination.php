<?php
class Destination {
    private $conn;
    private $table_name = "destinations";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readFeatured() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_featured = 1 ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function read($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (name, description, short_description, category, image_url, latitude, longitude, is_featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['short_description'],
            $data['category'],
            $data['image_url'],
            $data['latitude'],
            $data['longitude'],
            isset($data['is_featured']) ? 1 : 0
        ]);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                SET name = ?, description = ?, short_description = ?, category = ?, 
                    latitude = ?, longitude = ?, is_featured = ?";
        
        $params = [
            $data['name'],
            $data['description'],
            $data['short_description'],
            $data['category'],
            $data['latitude'],
            $data['longitude'],
            isset($data['is_featured']) ? 1 : 0
        ];

        if (!empty($data['image_url'])) {
            $query .= ", image_url = ?";
            $params[] = $data['image_url'];
        }

        $query .= " WHERE id = ?";
        $params[] = $data['id'];

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function updateFeatured($id, $featured) {
        $query = "UPDATE " . $this->table_name . " SET is_featured = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$featured ? 1 : 0, $id]);
    }

    public function updateImage($data) {
        $query = "UPDATE " . $this->table_name . " SET image_url = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['image_url'], $data['id']]);
    }

    public function getFeaturedCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE is_featured = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['count'];
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table_name; // Adjust the query as necessary
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch distinct categories
    }
}
