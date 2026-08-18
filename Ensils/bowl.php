<?php
session_start();

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Display cart message if exists
$cart_message = "";
if (isset($_SESSION['cart_message'])) {
    $cart_message = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']);
}

// Calculate cart count
$cart_count = count($_SESSION['cart']);

// Serving Bowl products
$serving_bowls = [
    ["id"=>1, "name"=>"Small Serving Bowl", "price"=>250, "image"=>"images/bowl_small.png", "desc"=>"Small serving bowl, perfect for individual portions."],
    ["id"=>2, "name"=>"Medium Serving Bowl", "price"=>350, "image"=>"images/bowl_medium.png", "desc"=>"Medium serving bowl, ideal for family meals."],
    ["id"=>3, "name"=>"Large Serving Bowl", "price"=>450, "image"=>"images/bowl_large.png", "desc"=>"Large serving bowl, great for parties and gatherings."],
    ["id"=>4, "name"=>"Decorative Bowl Set", "price"=>600, "image"=>"images/bowl_set.png", "desc"=>"Set of decorative bowls, perfect for special occasions."],
    ["id"=>5, "name"=>"Traditional Bowl", "price"=>300, "image"=>"images/bowl_traditional.png", "desc"=>"Traditional clay bowl with authentic design."],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serving Bowls - Ensils</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="products.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Ensils</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="cart.php" class="btn btn-outline-primary me-2 position-relative">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="shop.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Shop
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
                    <div class="auth-buttons">
                        <a href="admin_login.php"><button>Login</button></a>
                        <a href="signup.html"><button>Sign Up</button></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <header class="text-center my-4">
            <h2><i class="fas fa-bowl-food"></i> Explore Our Serving Bowls</h2>
            <p class="text-muted">Elegant clay serving bowls for your dining table</p>
            
            <?php if ($cart_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $cart_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </header>

        <div class="row g-4">
            <?php foreach ($serving_bowls as $bowl): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card product-card">
                        <img src="<?= $bowl['image'] ?>" class="card-img-top product-img" alt="<?= $bowl['name'] ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= $bowl['name'] ?></h5>
                            <p class="card-text"><?= $bowl['desc'] ?></p>
                            <h6 class="text-primary">₹ <?= number_format($bowl['price'], 2) ?></h6>

                            <form method="POST" action="add_to_cart.php">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="id" value="<?= $bowl['id'] ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($bowl['name']) ?>">
                                <input type="hidden" name="price" value="<?= $bowl['price'] ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($bowl['image']) ?>">
                                <button type="submit" class="btn btn-primary mt-2">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Footer -->
        <footer class="text-center py-4 mt-5 bg-light">
            <p>© <?= date("Y"); ?> Ensils – Rajkot, Gujarat</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>