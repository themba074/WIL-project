<?php
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP
$dbname = "realhome_db"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : null;
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;

    if (isset($_POST['login'])) {
        // Login logic
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password (assuming passwords are hashed)
            if (password_verify($password, $user['password'])) {
                echo "Login successful! Welcome back, " . $user['username'];
                // Redirect to the index page or dashboard
                header("Location: index.html");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No user found with that username.";
        }
    } elseif (isset($_POST['register'])) {
        // Registration logic
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);

        if ($stmt->execute()) {
            echo "Registration successful! You can now log in.";
            // Redirect to the login page or index page
            header("Location: index.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Close connection
$conn->close();
?>