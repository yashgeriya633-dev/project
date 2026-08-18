<?php
// Database connection
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$database = "yashproject";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$contact = $_POST['contact'];
$password = $_POST['password'];

// Check if contact is email or phone
if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
    $email = $contact;
    $phone = null;
} elseif (preg_match("/^\d{10}$/", $contact)) {
    $phone = $contact;
    $email = null;
} else {
    die("Invalid email or phone number.");
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$stmt = $conn->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);

if ($stmt->execute()) {
    header("Location: admin_login.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
