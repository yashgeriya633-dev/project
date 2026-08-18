<?php
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['user'])) {
    header("Location: admin_login.php");
    exit();
}

// Redirect to cart if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
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

$message = "";
$order_success = false;

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    $customer_name = $_SESSION['user']['name'];
    $customer_email = $_SESSION['user']['email'];
    $customer_phone = $_SESSION['user']['phone'];
    
    if (empty($shipping_address)) {
        $message = "<div class='alert alert-danger'>Please enter a shipping address.</div>";
    } else {
        // Calculate totals
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
        
        // Add shipping cost if order is less than ₹500
        $shipping_cost = $total_amount >= 500 ? 0 : 50;
        $final_total = $total_amount + $shipping_cost;
        
        // Start transaction
        $conn->autocommit(false);
        
        try {
            $order_id = null;
            
            // Insert each item as a separate order entry
            foreach ($_SESSION['cart'] as $item) {
                $sql = "INSERT INTO orders (user_id, product_name, quantity, price, total_amount, status, shipping_address, customer_name, customer_email, customer_phone, payment_method, shipping_cost, final_total) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                $item_total = $item['price'] * $item['quantity'];
                $stmt->bind_param("isisddsssssdd", 
                    $_SESSION['user']['id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $item_total,
                    $shipping_address,
                    $customer_name,
                    $customer_email,
                    $customer_phone,
                    $payment_method,
                    $shipping_cost,
                    $final_total
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting order: " . $stmt->error);
                }
                
                if ($order_id === null) {
                    $order_id = $conn->insert_id;
                }
                
                $stmt->close();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart after successful order
            $_SESSION['cart'] = [];
            
            $order_success = true;
            $message = "<div class='alert alert-success'><h4><i class='fas fa-check-circle'></i> Order Placed Successfully!</h4><p>Your order ID is: <strong>#{$order_id}</strong></p><p>You will receive a confirmation email shortly.</p></div>";
            
            // Redirect to orders page after 3 seconds
            echo "<script>setTimeout(function(){ window.location.href = 'orders.php'; }, 5000);</script>";
            
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            $message = "<div class='alert alert-danger'>Error placing order: " . $e->getMessage() . "</div>";
        }
        
        $conn->autocommit(true);
    }
}

// Calculate totals
$total_items = 0;
$total_amount = 0;

foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
    $total_amount += $item['price'] * $item['quantity'];
}

$shipping_cost = $total_amount >= 500 ? 0 : 50;
$final_total = $total_amount + $shipping_cost;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Ensils</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="home.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .checkout-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
        }
        .product-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-checkout {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: transform 0.2s;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            color: white;
        }
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .payment-method.selected {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Ensils</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="cart.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Cart
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

    <div class="checkout-container">
        <?php if ($order_success): ?>
            <div class="text-center py-5">
                <div class="checkout-card">
                    <?= $message ?>
                    <div class="mt-4">
                        <a href="orders.php" class="btn btn-primary me-2">
                            <i class="fas fa-shopping-bag"></i> View Orders
                        </a>
                        <a href="shop.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="checkout-card">
                        <h2><i class="fas fa-credit-card"></i> Checkout</h2>
                        <hr>
                        
                        <?= $message ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user"></i> Customer Information</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']['phone']) ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-truck"></i> Shipping Address</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Delivery Address *</label>
                                        <textarea class="form-control" name="shipping_address" rows="4" required placeholder="Enter your complete delivery address"><?= htmlspecialchars($_SESSION['user']['address']) ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h5><i class="fas fa-credit-card"></i> Payment Method</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="payment-method" onclick="selectPayment('cod')">
                                        <input type="radio" name="payment_method" value="cod" id="cod" checked>
                                        <label for="cod" class="ms-2">
                                            <i class="fas fa-money-bill-wave text-success"></i> Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="payment-method" onclick="selectPayment('card')">
                                        <input type="radio" name="payment_method" value="card" id="card">
                                        <label for="card" class="ms-2">
                                            <i class="fas fa-credit-card text-primary"></i> Credit/Debit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="payment-method" onclick="selectPayment('upi')">
                                        <input type="radio" name="payment_method" value="upi" id="upi">
                                        <label for="upi" class="ms-2">
                                            <i class="fas fa-mobile-alt text-info"></i> UPI Payment
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" name="place_order" class="btn btn-checkout">
                                    <i class="fas fa-lock"></i> Place Order Securely
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="checkout-card">
                        <h5><i class="fas fa-list"></i> Order Summary</h5>
                        <hr>
                        
                        <div class="order-summary">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="product-item">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                        <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₹<?= number_format($total_amount, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span><?= $shipping_cost == 0 ? 'FREE' : '₹' . number_format($shipping_cost, 2) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Total:</h5>
                                <h5 class="text-success">₹<?= number_format($final_total, 2) ?></h5>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checkout-card">
                        <h6><i class="fas fa-shield-alt"></i> Secure Checkout</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-lock text-success"></i> SSL encrypted checkout</li>
                            <li><i class="fas fa-truck text-primary"></i> Free delivery on orders above ₹500</li>
                            <li><i class="fas fa-undo text-info"></i> Easy returns within 7 days</li>
                            <li><i class="fas fa-headset text-warning"></i> 24/7 customer support</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayment(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
        }
        
        // Initialize first payment method as selected
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.payment-method').classList.add('selected');
        });
    </script>
</body>
</html>