<?php
session_start();

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contact = trim($_POST["contact"]);

    // Check if contact is a valid phone number
    if (preg_match('/^\d{10}$/', $contact)) {
        // Handle phone number reset
        $message = "A password reset link has been sent to your registered phone number.";
        $message_type = "success";
    } elseif (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
        // Handle email reset
        $message = "A password reset link has been sent to your email.";
        $message_type = "success";
    } else {
        // Invalid input
        $message = "Please enter a valid 10-digit phone number or email address.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password - Ensils</title>
  <link href="forgot.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    .message {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 8px;
      text-align: center;
      font-size: 0.95rem;
    }
    .message.success {
      background: rgba(46, 204, 113, 0.2);
      border: 1px solid rgba(46, 204, 113, 0.5);
      color: #2ecc71;
    }
    .message.error {
      background: rgba(231, 76, 60, 0.2);
      border: 1px solid rgba(231, 76, 60, 0.5);
      color: #ff7675;
    }
  </style>
</head>
<body>
  <div class="container">
    <form class="glass-form" action="forgot.php" method="POST">
      <h2><i class="fas fa-lock"></i> Forgot Password</h2>
      <p>Enter your registered email or phone number</p>
      
      <?php if ($message): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <input type="text" name="contact" placeholder="Email or Phone Number" required>
      <button type="submit"><i class="fas fa-paper-plane"></i> Send Reset Link</button>
      <a href="admin_login.php" class="back"><i class="fas fa-arrow-left"></i> Back to login</a>
    </form>
  </div>
</body>
</html>
