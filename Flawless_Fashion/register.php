<?php
include 'db.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL statement
    $sql = "INSERT INTO Users (password_hash, email) VALUES (?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $password_hash, $email); // Changed to "ss" for two string parameters
        
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>