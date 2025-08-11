<?php
session_start();
include 'db.php'; // Include the database connection

// Function to fetch cart items based on user ID
function getCartItems($user_id) {
    global $conn;
    $sql = "SELECT c.cart_id, p.product_name, p.price, c.quantity 
            FROM Shopping_Cart c 
            JOIN Products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $cartItems;
}

// Add to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if the item already exists in the cart
    $checkSql = "SELECT * FROM Shopping_Cart WHERE user_id = ? AND product_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $user_id, $product_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Update quantity if the item already exists
        $updateSql = "UPDATE Shopping_Cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("iii", $quantity, $user_id, $product_id);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Insert new item into cart
        $insertSql = "INSERT INTO Shopping_Cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insertStmt->execute();
        $insertStmt->close();
    }
    echo json_encode(['status' => 'success']);
    exit;
}

// View cart
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cartItems = getCartItems($user_id);
    echo json_encode($cartItems);
    exit;
}
?>