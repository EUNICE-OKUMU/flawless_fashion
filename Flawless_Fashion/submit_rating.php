<?php
include 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST['product_id'];
    $user_id = $_POST['user_id']; // Optional: adjust as needed
    $rating = $_POST['rating'];

    // Prepare the SQL statement
    $sql = "INSERT INTO Product_Ratings (product_id, user_id, rating) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $product_id, $user_id, $rating);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your rating!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error submitting rating.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>