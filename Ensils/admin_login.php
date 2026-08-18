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

$error_message = "";
$success_message = "";

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if admin exists
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = [
                "id" => $admin['id'],
                "username" => $admin['username'],
                "email" => $admin['email'],
                "role" => $admin['role']
            ];

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Admin not found.";
    }

    $stmt->close();
}

// Handle user login
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['user_login'])) {
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
            $_SESSION['user'] = [
                "id" => $user['id'],
                "name" => $user['username'],
                "email" => $user['email'],
                "phone" => $user['phone_number'],
                "address" => $user['address'] ?? "Not provided"
            ];

            $success_message = "Login successful! Redirecting...";
            echo "<script>setTimeout(function(){ window.location.href = 'home.php'; }, 1000);</script>";
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ensils</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="theme.css">
    <style>
        body {
            background: radial-gradient(circle at center, #8c5a52 0%, #2c1b18 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 900px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .login-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .login-tabs {
            display: flex;
            margin-bottom: 2rem;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .tab-button {
            flex: 1;
            padding: 1rem;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        .tab-button.active {
            background: linear-gradient(135deg, #a97369 0%, #6b433d 100%);
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #8c5a52;
            box-shadow: 0 0 0 0.2rem rgba(140, 90, 82, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #a97369 0%, #6b433d 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        .back-link a {
            color: #8c5a52;
            text-decoration: none;
            font-weight: 500;
        }
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-store"></i> Ensils</h1>
            <p class="text-muted">Clay Utensils - Traditional Elegance</p>
        </div>

        <?php if ($error_message): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>

        <div class="login-tabs">
            <button class="tab-button active" onclick="switchTab('user')">
                <i class="fas fa-user"></i> User Login
            </button>
            <button class="tab-button" onclick="switchTab('admin')">
                <i class="fas fa-cog"></i> Admin Login
            </button>
        </div>

        <!-- User Login Form -->
        <div id="user-tab" class="tab-content active">
            <form method="POST">
                <input type="hidden" name="user_login" value="1">
                <div class="mb-3">
                    <input type="text" class="form-control" name="username" placeholder="Username, Email or Phone" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> User Login
                </button>
            </form>
            
            <div class="divider">
                <span>Don't have an account?</span>
            </div>
            
            <div class="text-center">
                <a href="signup.html" class="btn btn-outline-dark w-100 mb-2">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
                <a href="forgot.php" class="text-muted">Forgot Password?</a>
            </div>
        </div>

        <!-- Admin Login Form -->
        <div id="admin-tab" class="tab-content">
            <form method="POST">
                <input type="hidden" name="admin_login" value="1">
                <div class="mb-3">
                    <input type="text" class="form-control" name="username" placeholder="Admin Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Admin Password" required>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-shield-alt"></i> Admin Login
                </button>
            </form>
            
            <div class="divider">
                <span>Admin Access</span>
            </div>
            
            <div class="text-center">
                <small class="text-muted">
                    Default: admin / admin123<br>
                    <em>Change password after first login</em>
                </small>
            </div>
        </div>

        <div class="back-link">
            <a href="home.php"><i class="fas fa-home"></i> Back to Website</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchTab(tab) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            event.target.classList.add('active');
            document.getElementById(tab + '-tab').classList.add('active');
        }
    </script>
</body>
</html>
