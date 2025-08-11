<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set Content-Type header to JSON always
header('Content-Type: application/json');

// Include database connection
include 'db.php'; // Ensure this sets $conn and has no output

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are allowed.']);
    exit;
}

// Get and sanitize inputs
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// Validate input
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

// Send email (optional)
$to = 'your_email@example.com'; // Replace this
$subject = 'New Contact Form Submission';
$headers = "From: $email\r\nReply-To: $email\r\n";
$body = "Name: $name\nEmail: $email\nMessage:\n$message\n";

$mailSent = mail($to, $subject, $body, $headers);

// Respond to the client
if ($mailSent) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Your message was sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again.']);
}
