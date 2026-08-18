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

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>User deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
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
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
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
                        <a class="nav-link active" href="admin_users.php">
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
                        <h2><i class="fas fa-users"></i> User Management</h2>
                        <div>
                            <span class="badge bg-primary"><?= count($users) ?> Users</span>
                        </div>
                    </div>
                </div>

                <?= $message ?>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">ID: <?= $user['id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['phone_number'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['address'] ?? 'Not provided') ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" onclick="editUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')">
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
            </div>
        </div>
    </div>

    <!-- User View Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userModalBody">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewUser(userId) {
            // For now, just show a simple alert. In a real application, you'd fetch user details via AJAX
            alert('View user details for ID: ' + userId);
        }

        function editUser(userId) {
            // For now, just show a simple alert. In a real application, you'd open an edit form
            alert('Edit user with ID: ' + userId);
        }

        function deleteUser(userId, username) {
            if (confirm('Are you sure you want to delete user "' + username + '"? This action cannot be undone.')) {
                window.location.href = 'admin_users.php?delete=' + userId;
            }
        }
    </script>
</body>
</html>

