<?php
include 'db.php'; // Include the database connection

$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h5>" . $row['product_name'] . "</h5>";
        echo "<p>Ksh " . $row['price'] . "</p>";
        echo "<button>Add To Cart</button>";
        echo "</div>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>