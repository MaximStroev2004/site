<?php
session_start();

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_favorites') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($product_id) {
        foreach ($_SESSION['favorites'] as $key => $item) {
            if ($item['id'] == $product_id) {
                unset($_SESSION['favorites'][$key]);
                $_SESSION['favorites'] = array_values($_SESSION['favorites']);
                
                header("Location: favorites.php");
                exit();
            }
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
    <title>Избранное</title>
    <link href="https://fonts.googleapis.com/css2?family=Smooch&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="sneaker-shop">Избранное</h1>
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
                <form class="search-form" action="index.php" method="get">
                    <input type="text" name="search" placeholder="Поиск товара">
                    <button type="submit">Найти</button>
                </form>
            </nav>
        </header>
        <div class="list">
            <?php
            if (!empty($_SESSION['favorites'])) {
                foreach ($_SESSION['favorites'] as $product) {
                    echo '<div class="item">
                        <a href="' . str_replace(' ', '', strtolower($product['name'])) . '.php">
                            <img src="image/' . $product['image'] . '" alt="' . $product['name'] . '">
                            <div class="title">' . $product['name'] . '</div>
                            <div class="price">' . number_format($product['price'], 0, '', ' ') . ' руб</div>
                        </a>
                        <form class="add-to-cart-form" action="index.php" method="post">
                            <input type="hidden" name="action" value="add_to_cart">
                            <input type="hidden" name="product_id" value="' . $product['id'] . '">
                            <button type="submit">Добавить в корзину</button>
                        </form>
                        <form class="remove-from-favorites-form" action="favorites.php" method="post">
                            <input type="hidden" name="action" value="remove_from_favorites">
                            <input type="hidden" name="product_id" value="' . $product['id'] . '">
                            <button type="submit">Удалить из избранного</button>
                        </form>
                    </div>';
                }
            } else {
                echo "Ваш список избранного пуст.";
            }
            ?>
        </div>
    </div>
</body>
</html>