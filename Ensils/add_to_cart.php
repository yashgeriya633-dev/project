<?php
session_start();

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $image = $_POST['image'];

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id && $item['name'] == $name) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'image' => $image
        ];
    }
    
    // Redirect back to shop with success message
    $_SESSION['cart_message'] = "Product added to cart successfully!";
    header("Location: shop.php");
    exit();
}

// Handle remove from cart
if (isset($_POST['remove_from_cart'])) {
    $id = intval($_POST['id']);
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
    $_SESSION['cart_message'] = "Product removed from cart!";
    header("Location: shop.php");
    exit();
}

// Redirect to shop page
header("Location: shop.php");
exit();
?>

