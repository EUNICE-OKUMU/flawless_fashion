<?php
// Database configuration
$servername = "localhost"; // Change if necessary
$username = "root";
$password = "";
$dbname = "Flawless_Fashion";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch team members from database
$sql = "SELECT name, position, email, phone, image_url FROM team_members";
$result = $conn->query($sql);

$team_members = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $team_members[] = $row; // Store each member in an array
    }
}

$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($team_members);
?>