<?php
session_start(); // Start session
?>
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "yashproject";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $product_name = $_POST['product_name'];
    $material_preference = $_POST['material_preference'];
    $color_preference = $_POST['color_preference'];
    $size_details = $_POST['size_details'];
    $custom_description = $_POST['custom_description'];

    // ===== IMAGE UPLOAD FIX START =====
    $target_dir = __DIR__ . "/uploads/"; // absolute path
    $reference_image = "";

    // Create uploads folder if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!empty($_FILES["reference_image"]["name"])) {
        // Remove spaces and special characters
        $filename = preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($_FILES["reference_image"]["name"]));
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["reference_image"]["tmp_name"], $target_file)) {
            // Save relative path for DB
            $reference_image = "uploads/" . $filename;
        } else {
            echo "<script>alert('⚠️ File upload failed! Please check folder permissions or path.');</script>";
        }
    }
    // ===== IMAGE UPLOAD FIX END =====

    // Insert data into database
    $sql = "INSERT INTO custom_products 
            (customer_name, email, phone, product_name, material_preference, color_preference, size_details, custom_description, reference_image)
            VALUES ('$customer_name', '$email', '$phone', '$product_name', '$material_preference', '$color_preference', '$size_details', '$custom_description', '$reference_image')";

    if ($conn->query($sql)) {
        echo "<script>alert('✅ Your custom product request has been submitted successfully!');</script>";
    } else {
        echo "<script>alert('❌ Error: " . $conn->error . "');</script>";
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ensils - Create Custom Order</title>
  
  <!-- Bootstrap CDN -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="theme.css" />
  <link rel="stylesheet" href="home.css" />
  <link rel="stylesheet" href="create.css" />
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.php">Ensils</a>

      <!-- ✅ Auth Buttons / Profile -->
      <div class="ms-auto d-flex align-items-center">
        <?php if(isset($_SESSION['user'])): ?>
          
        <?php else: ?>
          <!-- Show Login / Sign Up when not logged in -->
          <div class="auth-buttons">
            <a href="admin_login.php"><button>Login</button></a>
            <a href="signup.html"><button>Sign Up</button></a>
          </div>
        <?php endif; ?>
      </div>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item">
            <a class="nav-link active" href="home.php">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="shop.php">Shop</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="create.php">
              Create
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="aboutus.php">About us</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="cart.php">Cart</a>
          </li>
              <?php if (isset($_SESSION['user'])): ?>
          <div class="dropdown ms-3">
            <div class="profile-icon"></div>
            <div class="dropdown-content">
              <a href="profile.php">My Profile</a>
              <a href="orders.php">My Orders</a>
              <a href="logout.php">Logout</a>
            </div>
          </div>
        <?php endif; ?>

            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</body>
</html>



<body>
    <div class="container create-page my-5">
        <h2 class="text-center mb-4"> Create Your Own Clay Product</h2>
        <form action="create.php" method="POST" enctype="multipart/form-data" class="p-4 shadow rounded bg-light">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Your Name</label>
                    <input type="text" name="customer_name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Product Name / Idea</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Material Preference</label>
                    <select name="material_preference" class="form-control">
                        <option>Natural Clay</option>
                        <option>Terracotta</option>
                        <option>Earthenware</option>
                        <option>Mix Material</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Color Preference</label>
                    <input type="text" name="color_preference" class="form-control" placeholder="e.g., Brown, Red, Black">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Size Details</label>
                    <input type="text" name="size_details" class="form-control" placeholder="e.g., 10-inch diameter">
                </div>
            </div>

            <div class="mb-3">
                <label>Describe Your Idea</label>
                <textarea name="custom_description" class="form-control" rows="4" placeholder="Explain your concept, design, or customization needs..."></textarea>
            </div>

            <div class="mb-3">
                <label>Upload Reference Image (optional)</label>
                <input type="file" name="reference_image" class="form-control" accept="image/*">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success px-4 py-2">Submit Request</button>
            </div>
        </form>
    </div>
</body>
</html>
