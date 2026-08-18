<?php
session_start();

$host = "localhost";
$dbname = "yashproject";
$dbuser = "root";
$dbpass = "";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $input = $_POST['username']; // username/email/phone
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? OR email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $input, $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // ✅ Store complete user info in session
            $_SESSION['user'] = [
                "id"      => $user['id'],
                "name"    => $user['username'],   // adjust if column is 'name'
                "email"   => $user['email'],
                "phone"   => $user['phone_number'],
                "address" => $user['address'] ?? "Not provided"
            ];

            header("Location: home.php");
            exit();
        } else {
            echo "<h2 style='text-align:center; color:red;'>Invalid password.</h2>";
        }
    } else {
        echo "<h2 style='text-align:center; color:red;'>User not found.</h2>";
    }

    $stmt->close();
}
$conn->close();
?>
