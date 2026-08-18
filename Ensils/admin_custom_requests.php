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

// Handle request status update
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE custom_products SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $request_id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Request status updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error updating status: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Handle request deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $request_id = $_GET['delete'];
    $sql = "DELETE FROM custom_products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Request deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting request: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Get all custom requests
$requests = $conn->query("SELECT * FROM custom_products ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Requests - Admin Panel</title>
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
        .request-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .request-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .request-body {
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
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .reference-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="admin_orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                        <a class="nav-link active" href="admin_custom_requests.php">
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
                        <h2><i class="fas fa-tools"></i> Custom Product Requests</h2>
                        <div>
                            <span class="badge bg-primary"><?= count($requests) ?> Requests</span>
                        </div>
                    </div>
                </div>

                <?= $message ?>

                <!-- Custom Requests -->
                <?php foreach ($requests as $request): ?>
                    <div class="request-card">
                        <div class="request-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($request['customer_name']) ?></h5>
                                    <small class="text-muted">Request ID: #<?= $request['id'] ?></small>
                                </div>
                                <div>
                                    <span class="status-badge status-<?= strtolower($request['status'] ?? 'pending') ?>">
                                        <?= ucfirst($request['status'] ?? 'Pending') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="request-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Email:</strong> <?= htmlspecialchars($request['email']) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Phone:</strong> <?= htmlspecialchars($request['phone']) ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Product Name:</strong> <?= htmlspecialchars($request['product_name']) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Material:</strong> <?= htmlspecialchars($request['material_preference']) ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Color Preference:</strong> <?= htmlspecialchars($request['color_preference']) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Size Details:</strong> <?= htmlspecialchars($request['size_details']) ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="mt-1"><?= htmlspecialchars($request['custom_description']) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php if (!empty($request['reference_image'])): ?>
                                        <div class="text-center mb-3">
                                            <strong>Reference Image:</strong>
                                            <div class="mt-2">
                                                <img src="<?= htmlspecialchars($request['reference_image']) ?>" 
                                                     class="reference-image" 
                                                     alt="Reference Image"
                                                     onclick="openImageModal('<?= htmlspecialchars($request['reference_image']) ?>')">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Status Update Form -->
                                    <form method="POST" class="mb-3">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <div class="mb-2">
                                            <label class="form-label"><strong>Update Status:</strong></label>
                                            <select name="status" class="form-control form-control-sm">
                                                <option value="pending" <?= ($request['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="processing" <?= ($request['status'] ?? '') == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                <option value="completed" <?= ($request['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="cancelled" <?= ($request['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="update_status" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-save"></i> Update Status
                                        </button>
                                    </form>
                                    
                                    <!-- Actions -->
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-outline-info btn-sm" onclick="contactCustomer('<?= htmlspecialchars($request['email']) ?>')">
                                            <i class="fas fa-envelope"></i> Contact
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteRequest(<?= $request['id'] ?>, '<?= htmlspecialchars($request['customer_name']) ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($requests)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Custom Requests Yet</h4>
                        <p class="text-muted">Custom product requests will appear here when customers submit them.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reference Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Reference Image">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        function contactCustomer(email) {
            window.location.href = 'mailto:' + email;
        }

        function deleteRequest(requestId, customerName) {
            if (confirm('Are you sure you want to delete the request from "' + customerName + '"? This action cannot be undone.')) {
                window.location.href = 'admin_custom_requests.php?delete=' + requestId;
            }
        }
    </script>
</body>
</html>

