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

// Get statistics
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total orders (assuming orders table exists)
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result ? $result->fetch_assoc()['count'] : 0;

// Custom product requests
$result = $conn->query("SELECT COUNT(*) as count FROM custom_products");
$stats['custom_requests'] = $result->fetch_assoc()['count'];

// Recent users (last 5)
$recent_users = $conn->query("SELECT username, email, created_at FROM users ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Recent custom requests (last 5)
$recent_requests = $conn->query("SELECT customer_name, product_name, created_at FROM custom_products ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ensils</title>
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.users { border-left-color: #28a745; }
        .stat-card.orders { border-left-color: #007bff; }
        .stat-card.requests { border-left-color: #ffc107; }
        .stat-card.products { border-left-color: #dc3545; }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .recent-item {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
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
                        <a class="nav-link active" href="admin_dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a class="nav-link" href="admin_users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                        <a class="nav-link" href="admin_products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                        <a class="nav-link" href="admin_orders.php">
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
                        <h2>Welcome back, <?= $_SESSION['admin']['username'] ?>!</h2>
                        <div>
                            <span class="badge bg-primary">Admin</span>
                            <a href="home.php" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-external-link-alt"></i> View Site
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card users">
                            <div class="stat-number text-success"><?= $stats['total_users'] ?></div>
                            <div class="text-muted">Total Users</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card orders">
                            <div class="stat-number text-primary"><?= $stats['total_orders'] ?></div>
                            <div class="text-muted">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card requests">
                            <div class="stat-number text-warning"><?= $stats['custom_requests'] ?></div>
                            <div class="text-muted">Custom Requests</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card products">
                            <div class="stat-number text-danger">6</div>
                            <div class="text-muted">Products</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Users -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-users"></i> Recent Users</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recent_users as $user): ?>
                                    <div class="recent-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            </div>
                                            <small class="text-muted"><?= isset($user['created_at']) ? date('M j', strtotime($user['created_at'])) : 'N/A' ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Custom Requests -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-tools"></i> Recent Custom Requests</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recent_requests as $request): ?>
                                    <div class="recent-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?= htmlspecialchars($request['customer_name']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($request['product_name']) ?></small>
                                            </div>
                                            <small class="text-muted"><?= isset($request['created_at']) ? date('M j', strtotime($request['created_at'])) : 'N/A' ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

