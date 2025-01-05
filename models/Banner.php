<?php
class Banner {
    private $conn;
    private $table_name = 'banners';

    public $id;
    public $title;
    public $image_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY title";
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


    // Create a new banner
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (title, image_url) VALUES (:title, :image_url)";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->title = htmlspecialchars(strip_tags($data['title']));
        $this->image_url = htmlspecialchars(strip_tags($data['image_url']));

        // bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':image_url', $this->image_url);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update an existing banner
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, image_url = :image_url WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->title = htmlspecialchars(strip_tags($data['title']));
        $this->image_url = htmlspecialchars(strip_tags($data['image_url']));
        $this->id = htmlspecialchars(strip_tags($data['id']));

        // bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':id', $this->id);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    
    public function updateImage($data) {
        $query = "UPDATE " . $this->table_name . " SET image_url = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['image_url'], $data['id']]);
    }



    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
