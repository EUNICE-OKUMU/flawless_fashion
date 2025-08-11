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
?>

<!-- Now you can use this $products array in your HTML -->