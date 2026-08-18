<?php
session_start(); // Start session

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Display cart message if exists
$cart_message = "";
if (isset($_SESSION['cart_message'])) {
    $cart_message = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']); // Clear message after displaying
}

// Calculate cart count
$cart_count = count($_SESSION['cart']);

$products = [
  ["id" => 1, "name" => "Clay Pot", "image" => "images/pot.png", "price" => 200],
  ["id" => 2, "name" => "Handi Set", "image" => "images/handi set.png", "price" => 500],
  ["id" => 3, "name" => "Clay Jug", "image" => "images/jug.png", "price" => 150],
  ["id" => 4, "name" => "Cooking Pan", "image" => "images/cooking pan.png", "price" => 350],
  ["id" => 5, "name" => "Serving Bowl", "image" => "images/servingbowl.png", "price" => 250],
  ["id" => 6, "name" => "Water Bottle", "image" => "images/bottle.png", "price" => 100],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ensils - Clay Utensils</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="theme.css">
  <link rel="stylesheet" href="shop.css" />
  
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.php">Ensils</a>
      <a href="cart.php" class="btn btn-outline-primary me-2 position-relative">
        <i class="fas fa-shopping-cart"></i> Cart
        <?php if ($cart_count > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $cart_count ?>
          </span>
        <?php endif; ?>
      </a>
      
      <?php if (isset($_SESSION['user'])): ?>
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
      <?php else: ?>
        <div class="auth-buttons d-none d-lg-flex">
          <a href="admin_login.php"><button>Login</button></a>
          <a href="signup.html"><button>Sign Up</button></a>
        </div>
      <?php endif; ?>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link active" href="home.php">Home</a></li>
          <li class="nav-item"><a class="nav-link shop-link" href="shop.php">Shop</a></li>
          <li class="nav-item"><a class="nav-link" href="create.php">Create</a></li>
          <li class="nav-item"><a class="nav-link" href="aboutus.php">About us</a></li>
          <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="container my-4 text-center">
    <h1>"Crafted by nature, Shaped for you"</h1>
    <p class="lead">Eco-friendly, authentic and beautifully crafted clay products</p>
    
    <?php if ($cart_message): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= $cart_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Product Grid -->
  <div class="container">
    <div class="row g-4">
      <?php foreach ($products as $product): ?>
        <div class="col-md-4 col-sm-6">
          <div class="card product-card">
            <img src="<?= $product['image'] ?>" class="card-img-top product-img" alt="<?= $product['name'] ?>">
            <div class="card-body text-center">
              <h5 class="card-title"><?= $product['name'] ?></h5>
              <p class="card-text">Pure handmade clay utensil. Ideal for healthy cooking.</p>
              
              <?php
                if (strtolower($product['name']) == 'handi set') {
                  $explore_page = 'handi.php';
                } elseif (strtolower($product['name']) == 'clay pot') {
                  $explore_page = 'claypot.php';
                } elseif (strtolower($product['name']) == 'cooking pan') {
                  $explore_page = 'pan.php';
                } elseif (strtolower($product['name']) == 'serving bowl') {
                  $explore_page = 'bowl.php';
                } elseif (strtolower($product['name']) == 'water bottle') {
                  $explore_page = 'bottle.php';
                } elseif (strtolower($product['name']) == 'clay jug') {
                  $explore_page = 'jugs.php';
                } else {
                  $explore_page = 'product_details.php?id='.$product['id'];
                }
              ?>
              <a href="<?= $explore_page ?>" class="btn btn-explore">
                <i class="fas fa-eye"></i> Explore Product
              </a>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer text-center py-3">
    <p>&copy; 2025 Ensils. All rights reserved.</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
