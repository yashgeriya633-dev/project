<?php
session_start();

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $id = intval($_POST['id']);
        $name = $_POST['name'];
        $quantity = intval($_POST['quantity']);
        
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id && $item['name'] == $name) {
                if ($quantity <= 0) {
                    // Remove item if quantity is 0 or negative
                    $item = null;
                } else {
                    $item['quantity'] = $quantity;
                }
                break;
            }
        }
        
        // Remove null items
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) {
            return $item !== null;
        });
        
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        $_SESSION['cart_message'] = "Cart updated successfully!";
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['remove_item'])) {
        $id = intval($_POST['id']);
        $name = $_POST['name'];
        
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $id && $item['name'] == $name) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        $_SESSION['cart_message'] = "Item removed from cart!";
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['cart_message'] = "Cart cleared!";
        header("Location: cart.php");
        exit();
    }
}

// Calculate totals
$total_items = 0;
$total_amount = 0;

foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
    $total_amount += $item['price'] * $item['quantity'];
}

// Display cart message if exists
$cart_message = "";
if (isset($_SESSION['cart_message'])) {
    $cart_message = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Ensils</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="theme.css">
  <link rel="stylesheet" href="home.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .cart-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 1.5rem;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
        }
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-cart i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Ensils</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="shop.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
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

    <div class="cart-container">
        <div class="row">
            <div class="col-md-8">
                <div class="cart-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-shopping-cart"></i> Shopping Cart</h2>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="clear_cart" value="1">
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to clear your cart?')">
                                    <i class="fas fa-trash"></i> Clear Cart
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if ($cart_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= $cart_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($_SESSION['cart'])): ?>
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Your cart is empty</h3>
                            <p class="text-muted">Add some products to get started!</p>
                            <a href="shop.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i> Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="cart-item">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                                    <p class="text-muted mb-0">₹<?= number_format($item['price'], 2) ?> each</p>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <form method="POST" class="d-flex align-items-center me-3">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']) ?>">
                                        <label class="me-2">Qty:</label>
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" class="form-control quantity-input">
                                        <button type="submit" name="update_quantity" class="btn btn-outline-primary btn-sm ms-2">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </form>
                                    
                                    <div class="text-end me-3">
                                        <strong>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                                    </div>
                                    
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']) ?>">
                                        <button type="submit" name="remove_item" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this item from cart?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="summary-card">
                    <h4><i class="fas fa-receipt"></i> Order Summary</h4>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Items (<?= $total_items ?>):</span>
                        <span>₹<?= number_format($total_amount, 2) ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span><?= $total_amount >= 500 ? 'FREE' : '₹50' ?></span>
                    </div>
                    
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total:</h5>
                        <h5>₹<?= number_format($total_amount + ($total_amount >= 500 ? 0 : 50), 2) ?></h5>
                    </div>
                    
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="checkout.php" class="btn btn-light btn-lg w-100 mb-2">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <a href="admin_login.php" class="btn btn-light btn-lg w-100 mb-2">
                                <i class="fas fa-sign-in-alt"></i> Login to Checkout
                            </a>
                        <?php endif; ?>
                        
                        <a href="shop.php" class="btn btn-outline-light w-100">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="cart-card mt-3">
                    <h6><i class="fas fa-shield-alt"></i> Secure Shopping</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-lock text-success"></i> Secure checkout</li>
                        <li><i class="fas fa-truck text-primary"></i> Free shipping on orders above ₹500</li>
                        <li><i class="fas fa-undo text-info"></i> Easy returns</li>
                        <li><i class="fas fa-headset text-warning"></i> 24/7 customer support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>