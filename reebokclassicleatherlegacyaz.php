<?php
session_start();

$product = [
    'id' => 3,
    'name' => 'Reebok Classic Leather Legacy AZ',
    'image' => '3.png',
    'price' => 8370
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_favorites') {
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }
    
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($product_id) {
        
        $already_in_favorites = false;
        foreach ($_SESSION['favorites'] as $item) {
            if ($item['id'] == $product_id) {
                $already_in_favorites = true;
                break;
            }
        }
        
        if (!$already_in_favorites) {
            $_SESSION['favorites'][] = $product;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reebok Classic Leather Legacy AZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Smooch&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
       <header>
            <h1 class="sneaker-shop">SneakerShop</h1>
            <nav>
                 <ul class="nav-links">
                    <li><a href="index.php"><span class="icon"><img src="image/home.svg" alt="Главная"></span> Главная</a></li>
                    <li><a href="favorites.php"><span class="icon"><img src="image/favorite.svg" alt="Избранное"></span> Избранное</a></li>
                    <li><a href="cart.php"><span class="icon"><img src="image/shopping.svg" alt="Корзина"></span> Корзина</a></li>
                    <?php
                    if (isset($_SESSION['user_name']) && isset($_SESSION['user_email'])) {
                        echo '<li><a href="profile.php"><span class="icon"><img src="image/account.svg" alt="Профиль"></span> Профиль</a></li>';
                    } else {
                        echo '<li><a href="login.php"><span class="icon"><img src="image/login.svg" alt="Вход"></span> Вход</a></li>';
                    }
                   ?>
                </ul>
            </nav>
        </header>
        <div class="product-details">
            <h2>Reebok Classic Leather Legacy AZ</h2>
            <img src="image/3.png" alt="Reebok Classic Leather Legacy AZ">
            <p class="product-description">Эти кроссовки сочетают в себе эстетику классического дизайна и современные технологии, обеспечивая удобство и надежность в каждом шаге.</p>
            <p class="product-price">Цена: 8.370 руб</p>
            <form class="add-to-cart-form" action="cart.php" method="post">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="product_id" value="3">
                <button type="submit">Добавить в корзину</button>
            </form>
            <form class="add-to-favorites-form" action="reebok_classic.php" method="post">
                <input type="hidden" name="action" value="add_to_favorites">
                <input type="hidden" name="product_id" value="3">
                <button type="submit">Добавить в избранное</button>
            </form>
        </div>
    </div>
</body>
</html>
