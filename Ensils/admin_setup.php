<?php
// Database setup script for admin panel
// Run this script once to set up the necessary tables and admin user

$host = "localhost";
$dbname = "yashproject";
$dbuser = "root";
$dbpass = "";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Setting up Admin Panel Database...</h2>";

// Create admins table
$create_admins_table = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_admins_table)) {
    echo "<p>✅ Admins table created successfully!</p>";
} else {
    echo "<p>❌ Error creating admins table: " . $conn->error . "</p>";
}

// Create products table
$create_products_table = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_products_table)) {
    echo "<p>✅ Products table created successfully!</p>";
} else {
    echo "<p>❌ Error creating products table: " . $conn->error . "</p>";
}

// Create orders table
$create_orders_table = "CREATE TABLE IF NOT EXISTS orders (
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

if ($conn->query($create_orders_table)) {
    echo "<p>✅ Orders table created successfully!</p>";
} else {
    echo "<p>❌ Error creating orders table: " . $conn->error . "</p>";
}

// Add status column to custom_products table if it doesn't exist
$add_status_column = "ALTER TABLE custom_products ADD COLUMN IF NOT EXISTS status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending'";

if ($conn->query($add_status_column)) {
    echo "<p>✅ Status column added to custom_products table!</p>";
} else {
    echo "<p>⚠️ Status column may already exist or error: " . $conn->error . "</p>";
}

// Create default admin user
$admin_username = "admin";
$admin_email = "admin@ensils.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);

// Check if admin already exists
$check_admin = $conn->query("SELECT id FROM admins WHERE username = '$admin_username'");

if ($check_admin->num_rows == 0) {
    $insert_admin = "INSERT INTO admins (username, email, password, role) VALUES ('$admin_username', '$admin_email', '$admin_password', 'super_admin')";
    
    if ($conn->query($insert_admin)) {
        echo "<p>✅ Default admin user created successfully!</p>";
        echo "<p><strong>Admin Login Credentials:</strong></p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
        echo "<p><em>Please change the password after first login!</em></p>";
    } else {
        echo "<p>❌ Error creating admin user: " . $conn->error . "</p>";
    }
} else {
    echo "<p>⚠️ Admin user already exists!</p>";
}

// Insert sample products if table is empty
$check_products = $conn->query("SELECT COUNT(*) as count FROM products");
$product_count = $check_products->fetch_assoc()['count'];

if ($product_count == 0) {
    $sample_products = [
        ["Clay Pot", 200, "Pure handmade clay utensil. Ideal for healthy cooking.", "images/pot.png", "Cooking"],
        ["Handi Set", 500, "Traditional clay handi set for authentic cooking.", "images/handi set.png", "Cooking"],
        ["Clay Jug", 150, "Beautiful clay jug for water storage.", "images/jug.png", "Storage"],
        ["Cooking Pan", 350, "Clay cooking pan for healthy meals.", "images/cooking pan.png", "Cooking"],
        ["Serving Bowl", 250, "Elegant serving bowl for your dining table.", "images/servingbowl.png", "Serving"],
        ["Water Bottle", 100, "Eco-friendly clay water bottle.", "images/bottle.png", "Storage"]
    ];
    
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, category) VALUES (?, ?, ?, ?, ?)");
    $inserted_count = 0;
    
    foreach ($sample_products as $product) {
        $stmt->bind_param("sdsss", $product[0], $product[1], $product[2], $product[3], $product[4]);
        if ($stmt->execute()) {
            $inserted_count++;
        }
    }
    $stmt->close();
    
    echo "<p>✅ $inserted_count sample products inserted successfully!</p>";
} else {
    echo "<p>⚠️ Products table already contains $product_count products!</p>";
}

echo "<h3>Setup Complete!</h3>";
echo "<p><a href='admin_login.php' class='btn btn-primary'>Go to Admin Login</a></p>";
echo "<p><a href='home.php' class='btn btn-secondary'>Back to Website</a></p>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content is generated by PHP above -->
    </div>
</body>
</html>

