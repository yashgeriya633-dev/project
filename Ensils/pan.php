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

// Cooking Pan products / varieties
$cooking_pans = [
    ["id"=>1, "name"=>"Small Non-Stick Pan", "price"=>350, "image"=>"images/pan_small.png", "desc"=>"Small non-stick pan, perfect for quick cooking."],
    ["id"=>2, "name"=>"Medium Non-Stick Pan", "price"=>450, "image"=>"images/pan_medium.png", "desc"=>"Medium non-stick pan, ideal for family meals."],
    ["id"=>3, "name"=>"Large Frying Pan", "price"=>550, "image"=>"images/pan_large.png", "desc"=>"Large frying pan with ergonomic handle, great for daily cooking."],
    ["id"=>4, "name"=>"Cast Iron Pan", "price"=>700, "image"=>"images/pan_castiron.png", "desc"=>"Durable cast iron pan, perfect for traditional cooking."],
    ["id"=>5, "name"=>"Non-Stick Griddle Pan", "price"=>500, "image"=>"images/pan_griddle.png", "desc"=>"Non-stick griddle pan for pancakes, dosas, and more."],
    ["id"=>6, "name"=>"Ceramic Coated Pan", "price"=>650, "image"=>"images/pan_ceramic.png", "desc"=>"Eco-friendly ceramic coated pan, ideal for healthy cooking."],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cooking Pans - Ensils</title>
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
            <h2><i class="fas fa-fire"></i> Explore Our Cooking Pans</h2>
            <p class="text-muted">High-quality clay cooking pans for healthy meals</p>
            
            <?php if ($cart_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $cart_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </header>

        <div class="row g-4">
            <?php foreach ($cooking_pans as $pan): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card product-card">
                        <img src="<?= $pan['image'] ?>" class="card-img-top product-img" alt="<?= $pan['name'] ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= $pan['name'] ?></h5>
                            <p class="card-text"><?= $pan['desc'] ?></p>
                            <h6 class="text-primary">₹ <?= number_format($pan['price'], 2) ?></h6>

                            <form method="POST" action="add_to_cart.php">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="id" value="<?= $pan['id'] ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($pan['name']) ?>">
                                <input type="hidden" name="price" value="<?= $pan['price'] ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($pan['image']) ?>">
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