<?php
session_start(); // Start session
?>
<head>
  <!-- Bootstrap CDN -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="aboutus.css" />
</head>

<body>
<!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.php">Ensils</a>

      <!-- ✅ Auth Buttons / Profile -->
      <div class="ms-auto d-flex align-items-center">
        <?php if(isset($_SESSION['user'])): ?>
          
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
            <a class="nav-link" href="cart.php">Cart</a>
        </li>
        <?php if (isset($_SESSION['user'])): ?>
          <div class="dropdown ms-3">
            <div class="profile-icon"></div>
            <div class="dropdown-content">
              <a href="profile.php">My Profile</a>
              <a href="orders.php">My Orders</a>
              <a href="logout.php">Logout</a>
            </div>
          </div>
        <?php endif; ?>

            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<?php
// Backend variables for easy customization
$companyName = "Ensils";
$tagline = "Crafting Nature into Every Meal";
$aboutText = "At Ensils, we believe in blending tradition with sustainability. 
Our clay utensils are handcrafted with care, preserving the art of pottery while 
bringing the warmth of nature into your home. Every piece tells a story â€” of soil, 
fire, and the loving hands that shape it.";

$quotes = [
    "Clay is not just earth, its the soul of creation.",
    "From earth we come, and to earth we return” why not dine with it too?",
    "Handcrafted clayware” where tradition meets your table.â€"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ensils - <?php echo "clay utensils"; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="aboutus.css">
</head>
<body>

<header>
    <h1><?php echo $companyName; ?></h1>
    <p><?php echo $tagline; ?></p>
</header>

<div class="container">
    <h2>About Us</h2>
    <p class="about-text"><?php echo $aboutText; ?></p>

    <h2>Our Philosophy</h2>
    <div class="quotes">
        <?php foreach($quotes as $quote): ?>
            <p class="quote"><?php echo $quote; ?></p>
        <?php endforeach; ?>
    </div>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> <?php echo $companyName; ?>. All Rights Reserved.
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>