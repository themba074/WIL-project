<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realhome_db"; // Database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add, edit, or delete actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Capture form data
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $images = $_FILES['images']['name'];

    // Handle image upload if provided
    if (!empty($_FILES['images']['name'][0])) {
        $imagePath = "uploads/";
        $imageFiles = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        foreach ($_FILES['images']['name'] as $key => $image) {
            // Validate file type
            if (!in_array($_FILES['images']['type'][$key], $allowedTypes)) {
                die("Invalid file type: " . $_FILES['images']['type'][$key]);
            }

            $target = $imagePath . basename($image);
            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                $imageFiles[] = $target;
            }
        }
        $images = implode(",", $imageFiles);
    }

    if ($action == 'add') {
        // Add new property
        $sql = "INSERT INTO properties (title, location, price, description, type, images) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsss", $title, $location, $price, $description, $type, $images);
        $stmt->execute();

        echo $stmt->affected_rows > 0 ? "Property added successfully!" : "Error adding property.";
        $stmt->close();
    } elseif ($action == 'edit') {
        // Edit existing property
        $id = $_POST['id'];
        $sql = "UPDATE properties SET title = ?, location = ?, price = ?, description = ?, type = ?, images = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssi", $title, $location, $price, $description, $type, $images, $id);
        $stmt->execute();

        echo $stmt->affected_rows > 0 ? "Property updated successfully!" : "Error updating property.";
        $stmt->close();
    } elseif ($action == 'delete') {
        // Delete property
        $id = $_POST['id'];
        $sql = "DELETE FROM properties WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo $stmt->affected_rows > 0 ? "Property deleted successfully!" : "Error deleting property.";
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>