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

// Array of products
$products = [
    [
        "id" => 1,
        "name" => "Traditional Handi Set",
        "color" => "Red",
        "shape" => "Round",
        "price" => 1200,
        "image" => "images/handi1.jpg",
        "desc" => "Traditional red handi set, perfect for cooking and serving."
    ],
    [
        "id" => 2,
        "name" => "Modern Handi Set",
        "color" => "Blue",
        "shape" => "Oval",
        "price" => 1500,
        "image" => "images/handi2.jpg",
        "desc" => "Stylish blue handi set with modern design."
    ],
    [
        "id" => 3,
        "name" => "Premium Handi Set",
        "color" => "Green",
        "shape" => "Square",
        "price" => 1800,
        "image" => "images/handi3.jpg",
        "desc" => "Premium green handi set with durable finish."
    ],
    [
        "id" => 4,
        "name" => "Mini Handi Set",
        "color" => "Yellow",
        "shape" => "Round",
        "price" => 800,
        "image" => "images/handi4.jpg",
        "desc" => "Compact yellow handi set, perfect for small servings."
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handi Sets - Ensils</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            <h2><i class="fas fa-utensils"></i> Explore Our Handi Sets</h2>
            <p class="text-muted">Traditional clay handi sets for authentic cooking</p>
            
            <?php if ($cart_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $cart_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </header>

        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card product-card">
                        <img src="<?= $product['image'] ?>" class="card-img-top product-img" alt="<?= $product['name'] ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text"><?= $product['desc'] ?></p>
                            <div class="mb-2">
                                <span class="badge bg-primary me-1"><?= $product['color'] ?></span>
                                <span class="badge bg-secondary"><?= $product['shape'] ?></span>
                            </div>
                            <h6 class="text-primary">₹ <?= number_format($product['price'], 2) ?></h6>

                            <form method="POST" action="add_to_cart.php">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                                <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
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