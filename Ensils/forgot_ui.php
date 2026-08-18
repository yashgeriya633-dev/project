<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password</title>
  <link href="forgot.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <form class="glass-form" action="forgot.php" method="POST">
      <h2>Forgot Password</h2>
      <p>Enter your registered email or phone number</p>
      <input type="text" name="contact" placeholder="Email or Phone Number" required>
      <button type="submit">Send Reset Link</button>
      <a href="admin_login.php" class="back">← Back to login</a>
    </form>
  </div>
</body>
</html>
