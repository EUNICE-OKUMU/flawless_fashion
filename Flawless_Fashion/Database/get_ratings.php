<?php
// get_ratings.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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
    $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 1;
    
    // Get ratings summary
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_ratings,
            AVG(rating) as average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
        FROM ratings 
        WHERE product_id = ?
    ");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $response = [
        'success' => true,
        'product_id' => $product_id,
        'total_ratings' => (int)$result['total_ratings'],
        'average_rating' => $result['average_rating'] ? round($result['average_rating'], 1) : 0,
        'rating_breakdown' => [
            '5_stars' => (int)$result['five_stars'],
            '4_stars' => (int)$result['four_stars'],
            '3_stars' => (int)$result['three_stars'],
            '2_stars' => (int)$result['two_stars'],
            '1_star' => (int)$result['one_star']
        ]
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