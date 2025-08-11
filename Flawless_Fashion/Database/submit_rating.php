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
?><?php
// submit_rating.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Flawless_Fashion";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit();
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }
    
    // Get and validate input data
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 1;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating must be between 1 and 5');
    }
    
    // Get user's IP address if no user_id provided
    $user_ip = null;
    if (!$user_id) {
        $user_ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Check if user has already rated this product (prevent duplicate ratings)
    if ($user_id) {
        $checkStmt = $conn->prepare("SELECT id FROM ratings WHERE product_id = ? AND user_id = ?");
        $checkStmt->bind_param("ii", $product_id, $user_id);
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM ratings WHERE product_id = ? AND user_ip = ?");
        $checkStmt->bind_param("is", $product_id, $user_ip);
    }
    
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing rating
        if ($user_id) {
            $updateStmt = $conn->prepare("UPDATE ratings SET rating = ?, created_at = NOW() WHERE product_id = ? AND user_id = ?");
            $updateStmt->bind_param("iii", $rating, $product_id, $user_id);
        } else {
            $updateStmt = $conn->prepare("UPDATE ratings SET rating = ?, created_at = NOW() WHERE product_id = ? AND user_ip = ?");
            $updateStmt->bind_param("iis", $rating, $product_id, $user_ip);
        }
        $updateStmt->execute();
        $message = "Your rating has been updated successfully!";
        $updateStmt->close();
    } else {
        // Insert new rating
        $insertStmt = $conn->prepare("INSERT INTO ratings (product_id, user_id, user_ip, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("iisi", $product_id, $user_id, $user_ip, $rating);
        $insertStmt->execute();
        $message = "Thank you for your rating!";
        $insertStmt->close();
    }
    
    $checkStmt->close();
    
    // Get updated average rating for this product
    $avgStmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings WHERE product_id = ?");
    $avgStmt->bind_param("i", $product_id);
    $avgStmt->execute();
    $avgResult = $avgStmt->get_result()->fetch_assoc();
    $avgStmt->close();
    
    $response = [
        'success' => true,
        'message' => $message,
        'average_rating' => round($avgResult['avg_rating'], 1),
        'total_ratings' => $avgResult['total_ratings']
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ];
    echo json_encode($response);
} finally {
    // Close connection
    if ($conn) {
        $conn->close();
    }
}
?>