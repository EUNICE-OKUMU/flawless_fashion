<?php
include 'db.php'; // Include the database connection

$sql = "SELECT * FROM Products"; // Query to fetch all products
$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row; // Store each product in an array
    }
}

$conn->close();

// Set the content type to JSON
header('Content-Type: application/json');
echo json_encode($products); // Return the products as a JSON response
?>