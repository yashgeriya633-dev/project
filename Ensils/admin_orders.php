<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
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

$message = "";

// Handle order status update
if (isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Order status updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error updating order status: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Handle order deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $order_id = $_GET['delete'];
    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Order deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting order: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Create orders table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shipping_address TEXT,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20)
)";
$conn->query($create_table);

// Get all orders
$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// If no orders exist, create some sample data
if (empty($orders)) {
    $sample_orders = [
        [1, "Clay Pot", 1, 200, 200, "delivered", "123 Main St, City", "John Doe", "john@example.com", "1234567890"],
        [2, "Handi Set", 2, 500, 1000, "shipped", "456 Oak Ave, Town", "Jane Smith", "jane@example.com", "0987654321"],
        [3, "Clay Jug", 1, 150, 150, "processing", "789 Pine Rd, Village", "Bob Johnson", "bob@example.com", "1122334455"]
    ];
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, quantity, price, total_amount, status, shipping_address, customer_name, customer_email, customer_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($sample_orders as $order) {
        $stmt->bind_param("isisddssss", $order[0], $order[1], $order[2], $order[3], $order[4], $order[5], $order[6], $order[7], $order[8], $order[9]);
        $stmt->execute();
    }
    $stmt->close();
    
    // Refresh orders list
    $orders = $conn->query("SELECT * FROM orders ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .main-content {
            padding: 2rem;
        }
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .order-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .order-body {
            padding: 1.5rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .table th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
        }
        .table td {
            border: none;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h4><i class="fas fa-tachometer-alt"></i> Admin Panel</h4>
                    <hr>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="admin_dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a class="nav-link" href="admin_users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                        <a class="nav-link" href="admin_products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                        <a class="nav-link active" href="admin_orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                        <a class="nav-link" href="admin_custom_requests.php">
                            <i class="fas fa-tools"></i> Custom Requests
                        </a>
                        <a class="nav-link" href="admin_logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="admin-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i class="fas fa-shopping-cart"></i> Order Management</h2>
                        <div>
                            <span class="badge bg-primary"><?= count($orders) ?> Orders</span>
                        </div>
                    </div>
                </div>

                <?= $message ?>

                <!-- Orders Table -->
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?= $order['id'] ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($order['customer_name']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></small>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                        <td><?= $order['quantity'] ?></td>
                                        <td><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                                        <td>
                                            <span class="status-badge status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewOrder(<?= htmlspecialchars(json_encode($order)) ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="updateOrderStatus(<?= $order['id'] ?>, '<?= $order['status'] ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteOrder(<?= $order['id'] ?>, '<?= htmlspecialchars($order['customer_name']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Orders Yet</h4>
                        <p class="text-muted">Customer orders will appear here when they place them.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order View Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderModalBody">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="order_id" id="status_order_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select New Status:</label>
                            <select name="status" class="form-control" id="status_select" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_order_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrder(order) {
            const modalBody = document.getElementById('orderModalBody');
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Order Information:</strong></h6>
                        <p><strong>Order ID:</strong> #${order.id}</p>
                        <p><strong>Product:</strong> ${order.product_name}</p>
                        <p><strong>Quantity:</strong> ${order.quantity}</p>
                        <p><strong>Price per unit:</strong> ₹${order.price}</p>
                        <p><strong>Total Amount:</strong> ₹${order.total_amount}</p>
                        <p><strong>Status:</strong> <span class="badge bg-primary">${order.status}</span></p>
                        <p><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Customer Information:</strong></h6>
                        <p><strong>Name:</strong> ${order.customer_name}</p>
                        <p><strong>Email:</strong> ${order.customer_email}</p>
                        <p><strong>Phone:</strong> ${order.customer_phone}</p>
                        <h6 class="mt-3"><strong>Shipping Address:</strong></h6>
                        <p>${order.shipping_address}</p>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        }

        function updateOrderStatus(orderId, currentStatus) {
            document.getElementById('status_order_id').value = orderId;
            document.getElementById('status_select').value = currentStatus;
            new bootstrap.Modal(document.getElementById('statusModal')).show();
        }

        function deleteOrder(orderId, customerName) {
            if (confirm('Are you sure you want to delete the order from "' + customerName + '"? This action cannot be undone.')) {
                window.location.href = 'admin_orders.php?delete=' + orderId;
            }
        }
    </script>
</body>
</html>

