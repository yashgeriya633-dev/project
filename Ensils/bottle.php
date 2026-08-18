<?php
session_start();

// Water Bottle products / varieties
$water_bottles = [
    ["id"=>1, "name"=>"Small Clay Bottle", "price"=>100, "image"=>"images/bottle_small.png", "desc"=>"Small clay water bottle, ideal for personal use."],
    ["id"=>2, "name"=>"Medium Clay Bottle", "price"=>150, "image"=>"images/bottle_medium.png", "desc"=>"Medium clay water bottle, perfect for office or school."],
    ["id"=>3, "name"=>"Large Clay Bottle", "price"=>200, "image"=>"images/bottle_large.png", "desc"=>"Large clay water bottle, great for home use."],
    ["id"=>4, "name"=>"Colored Clay Bottle", "price"=>180, "image"=>"images/bottle_colored.png", "desc"=>"Beautifully colored clay bottle for daily use or gifting."],
    ["id"=>5, "name"=>"Handmade Clay Bottle", "price"=>220, "image"=>"images/bottle_handmade.png", "desc"=>"Handcrafted clay bottle, eco-friendly and stylish."],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Bottels - Ensils</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="products.css">
</head>
<body>

<div class="container">
    <header class="text-center my-4">
        <h2>Explore Our Water Bottles</h2>
        <a href="shop.php" class="btn btn-outline-secondary me-2 mt-2">
            <i class="fas fa-arrow-left"></i> Back to Shop
        </a>
    </header>

    <div class="row g-4">
        <?php foreach ($water_bottles  as $id => $item): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card product-card">
                    <img src="<?= $item['image'] ?>" class="card-img-top product-img" alt="<?= $item['name'] ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= $item['name'] ?></h5>
                        <p class="card-text"><?= $item['desc'] ?></p>
                        <h6>₹ <?= $item['price'] ?></h6>

                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="add_to_cart" value="1">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="name" value="<?= $item['name'] ?>">
                            <input type="hidden" name="price" value="<?= $item['price'] ?>">
                            <input type="hidden" name="image" value="<?= $item['image'] ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary mt-2">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>