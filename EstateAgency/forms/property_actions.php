<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realhome_db"; // Change to your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add, edit, or delete actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action']; // The action to perform (add, edit, delete)

    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $images = $_FILES['images']['name']; // Store image names

    // Handle image upload if images were provided
    if (!empty($_FILES['images']['name'][0])) {
        $imagePath = "uploads/";
        $imageFiles = [];

        foreach ($_FILES['images']['name'] as $key => $image) {
            $target = $imagePath . basename($image);
            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                $imageFiles[] = $target;
            }
        }
        $images = implode(",", $imageFiles); // Save paths as comma-separated
    }

    if ($action == 'add') {
        // Add new property
        $sql = "INSERT INTO properties (title, location, price, description, type, images) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsss", $title, $location, $price, $description, $type, $images);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Property added successfully!";
        } else {
            echo "Error adding property.";
        }
        $stmt->close();
    } elseif ($action == 'edit') {
        // Edit existing property by ID
        $id = $_POST['id'];
        $sql = "UPDATE properties SET title = ?, location = ?, price = ?, description = ?, type = ?, images = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssi", $title, $location, $price, $description, $type, $images, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Property updated successfully!";
        } else {
            echo "Error updating property.";
        }
        $stmt->close();
    } elseif ($action == 'delete') {
        // Delete property by ID
        $id = $_POST['id'];
        $sql = "DELETE FROM properties WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Property deleted successfully!";
        } else {
            echo "Error deleting property.";
        }
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>
