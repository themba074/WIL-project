<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "realhome_db");

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if registration or login form
    if (isset($_POST['register'])) {
        // Registration logic
        $email = $_POST['email'];
        $role = $_POST['role'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare SQL statement to insert user data
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);

        // Execute statement and check if successful
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            header("Location: index.html");
            exit();
        } else {
            echo "Registration failed: " . $conn->error;
        }
        $stmt->close();

    } elseif (isset($_POST['login'])) {
        // Login logic
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashedPassword, $role);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['username'] = $username;
                header("Location: index.html");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No user found with that username.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
