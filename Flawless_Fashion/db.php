<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Flawless_Fashion";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    $conn = null;
}
