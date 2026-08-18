<?php
session_start(); // Start session

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate cart count
$cart_count = count($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ensils - Clay Utensils</title>
  
  <!-- Bootstrap CDN -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="theme.css">
  <link rel="stylesheet" href="home.css" />
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.php">Ensils</a>

      <!-- ✅ Auth Buttons / Profile -->
      <div class="ms-auto d-flex align-items-center">
        <!-- Admin Link -->
        
        
        <?php if(isset($_SESSION['user'])): ?>
          <!-- Profile Dropdown when logged in -->
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
          <!-- Show Login / Sign Up when not logged in -->
          <div class="auth-buttons">
            <a href="admin_login.php"><button>Login</button></a>
            <a href="signup.html"><button>Sign Up</button></a>
          </div>
        <?php endif; ?>
      </div>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item">
            <a class="nav-link active" href="home.php">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="shop.php">Shop</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="create.php">
              Create
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="aboutus.php">About us</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="cart.php">
              <i class="fas fa-shopping-cart"></i> Cart
              <?php if ($cart_count > 0): ?>
                <span class="badge bg-danger"><?= $cart_count ?></span>
              <?php endif; ?>
            </a>
          </li>

            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <div class="logo-tagline d-flex align-items-left justify-content-left">
      <img src="images/ensils.png" alt="Ensils Logo" class="main-logo" />
      <h1 class="tagline ms-4">"Traditional Elegance in Every Clay Creation"</h1>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2025 Ensils. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
