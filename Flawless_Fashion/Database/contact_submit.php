<?php
// Database configuration
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "Flawless_Fashion"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO contact_messages (firstname, lastname, country, subject) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstname, $lastname, $country, $subject);

// Set parameters and execute
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$country = $_POST['country'];
$subject = $_POST['subject'];

if ($stmt->execute()) {
    // Success response
    echo json_encode(['status' => 'success', 'message' => 'Your message has been submitted successfully.']);
} else {
    // Error response
    echo json_encode(['status' => 'error', 'message' => 'There was an error submitting your message.']);
}

$stmt->close();
$conn->close();
?>