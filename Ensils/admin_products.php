<?php
session_start();

// Ensure admin authentication
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$host = "localhost";
$dbname = "yashproject";
$dbuser = "root";
$dbpass = "";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create products table if it doesn’t exist
$conn->query("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$message = "";

// Handle form actions (Add / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $category = trim($_POST['category']);

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $name, $price, $description, $image, $category);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Product added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error adding product: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    if ($_POST['action'] === 'update') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, image=?, category=? WHERE id=?");
        $stmt->bind_param("sdsssi", $name, $price, $description, $image, $category, $id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Product updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error updating product: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>🗑️ Product deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error deleting product: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; color: white;
        }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8); border-radius: 8px; margin: 5px 10px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2); color: white;
        }
        .main-content { padding: 2rem; }
        .product-card {
            background: white; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden; transition: transform 0.2s;
        }
        .product-card:hover { transform: translateY(-5px); }
        .product-image { width: 100%; height: 200px; object-fit: cover; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h4><i class="fas fa-tachometer-alt"></i> Admin Panel</h4><hr>
            <nav class="nav flex-column">
                <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Users</a>
                <a class="nav-link active" href="admin_products.php"><i class="fas fa-box"></i> Products</a>
                <a class="nav-link" href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>

        <!-- Main -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> Product Management</h2>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                    <span class="badge bg-primary ms-2"><?= count($products) ?> Products</span>
                </div>
            </div>

            <?= $message ?>

            <!-- Product Cards -->
            <div class="row">
                <?php foreach ($products as $p): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($p['image']) ?>" class="product-image" alt="<?= htmlspecialchars($p['name']) ?>">
                            <div class="p-3">
                                <h5><?= htmlspecialchars($p['name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($p['description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($p['category']) ?></span>
                                    <strong>₹<?= number_format($p['price'], 2) ?></strong>
                                </div>
                                <div class="btn-group w-100">
                                    <button class="btn btn-outline-primary btn-sm"
                                        onclick='editProduct(<?= json_encode($p) ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $p['id'] ?>"
                                       onclick="return confirm('Delete <?= htmlspecialchars($p['name']) ?>?');"
                                       class="btn btn-outline-danger btn-sm">
                                       <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Path</label>
                            <input type="text" name="image" class="form-control" placeholder="images/product.png" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="Cooking">Cooking</option>
                                <option value="Storage">Storage</option>
                                <option value="Serving">Serving</option>
                                <option value="Decorative">Decorative</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" id="edit_price" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Path</label>
                            <input type="text" name="image" id="edit_image" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" id="edit_category" class="form-control" required>
                                <option value="Cooking">Cooking</option>
                                <option value="Storage">Storage</option>
                                <option value="Serving">Serving</option>
                                <option value="Decorative">Decorative</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editProduct(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('edit_image').value = product.image;
    document.getElementById('edit_category').value = product.category;
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}
</script>
</body>
</html>
