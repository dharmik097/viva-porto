<?php
header('Content-Type: application/json');
include('../database/db.php'); // Include database connection

$action = $_GET['action'];

// Upload Banner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $title = $_POST['title'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES['image']['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES['image']['size'] > 500000) {
        echo json_encode(['success' => false, 'message' => 'Sorry, your file is too large.']);
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode(['success' => false, 'message' => 'Sorry, your file was not uploaded.']);
    } else {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Save banner details in the database
            $query = "INSERT INTO banners (image_url, title) VALUES (?, ?);";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $targetFile, $title);
            $stmt->execute();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
        }
    }
} else {
    switch ($action) {
        case 'add':
            $data = json_decode(file_get_contents('php://input'), true);
            $image_url = $data['image_url'];
            $title = $data['title'];
            $query = "INSERT INTO banners (image_url, title) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $image_url, $title);
            $stmt->execute();
            echo json_encode(['status' => 'success']);
            break;
        
        case 'update':
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $image_url = $data['image_url'];
            $title = $data['title'];
            $query = "UPDATE banners SET image_url = ?, title = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssi', $image_url, $title, $id);
            $stmt->execute();
            echo json_encode(['status' => 'success']);
            break;
        
        case 'delete':
            $id = $_GET['id'];
            $query = "DELETE FROM banners WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            echo json_encode(['status' => 'success']);
            break;
        
        case 'list':
            $query = "SELECT * FROM banners";
            $result = $conn->query($query);
            $banners = [];
            while ($row = $result->fetch_assoc()) {
                $banners[] = $row;
            }
            echo json_encode($banners);
            break;
    }
}
?>
