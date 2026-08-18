<?php
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['user'])) {
    header("Location: admin_login.php");
    exit();
}

$host = "localhost";
$dbname = "yashproject";
$dbuser = "root";
$dbpass = "";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user's orders
$user_id = $_SESSION['user']['id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders - Clay Utensils</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="theme.css">
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="orders.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="home.php">Ensils</a>
    <div class="ms-auto d-flex align-items-center">
      <a href="cart.php" class="btn btn-outline-primary me-2">
        <i class="fas fa-shopping-cart"></i> Cart
      </a>
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

<header>
  <h1>📦 My Orders</h1>
  <p>Track your order history</p>
</header>

<div class="container">
  <table class="orders-table">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total Price</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td>#<?= $order['id']; ?></td>
          <td><?= htmlspecialchars($order['product_name']); ?></td>
          <td><?= $order['quantity']; ?></td>
          <td>₹<?= number_format($order['total_amount'], 2); ?></td>
          <td><span class="status <?= strtolower($order['status']); ?>"><?= ucfirst($order['status']); ?></span></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
  <?php if (empty($orders)): ?>
    <div class="text-center py-5">
      <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
      <h4 class="text-muted">No Orders Yet</h4>
      <p class="text-muted">Your order history will appear here when you place orders.</p>
      <a href="shop.php" class="btn btn-primary">
        <i class="fas fa-shopping-cart"></i> Start Shopping
      </a>
    </div>
  <?php endif; ?>
</div>

<footer>
  <p>© <?= date("Y"); ?> Clay Utensils Company – Rajkot, Gujarat</p>
</footer>

</body>
</html>
