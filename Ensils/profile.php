<?php
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['user'])) {
    header("Location: admin_login.php");
    exit();
}

// Example user info from session
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - Ensils</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="home.php">Ensils</a>
    <div class="ms-auto d-flex align-items-center">
      <a href="cart.php" class="btn btn-outline-primary me-2">
        <i class="fas fa-shopping-cart"></i> Cart
      </a>

      <!-- Profile Dropdown -->
      <div class="dropdown">
        <div class="profile-icon" data-bs-toggle="dropdown"></div>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
          <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
          <li><a class="dropdown-item" href="cart.php">My Cart</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Profile Header -->
<div class="profile-header">
    <h2>Welcome, <?= htmlspecialchars($user['name']); ?>!</h2>
    <p>Manage your account and orders</p>
</div>

<!-- Profile Card -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile-card">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="profile-img-container">
                            <div class="profile-img mb-3">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                            <h4><?= htmlspecialchars($user['name']); ?></h4>
                            <p class="text-muted">Member since <?= date('M Y') ?></p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5>Account Information</h5>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <p><strong>Email:</strong><br><?= htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="col-sm-6">
                                <p><strong>Phone:</strong><br><?= htmlspecialchars($user['phone']); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Address:</strong><br><?= htmlspecialchars($user['address']); ?></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="orders.php" class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-bag"></i> My Orders
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="cart.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-shopping-cart"></i> My Cart
                                </a>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="home.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-home"></i> Continue Shopping
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="logout.php" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center py-4 mt-5 bg-light">
  <p>© <?= date("Y"); ?> Ensils – Rajkot, Gujarat</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
