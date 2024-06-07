<?php
session_start();

require_once 'db_connection.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add_to_cart' || $action === 'add_to_favorites') {
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

            if ($product_id) {
                $sql = "SELECT id, name, price, image FROM products WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();

                    if ($action === 'add_to_cart') {
                        $found = false;
                        foreach ($_SESSION['cart'] as &$item) {
                            if ($item['id'] == $product['id']) {
                                $item['quantity']++;
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $product['quantity'] = 1;
                            $_SESSION['cart'][] = $product;
                        }
                    } elseif ($action === 'add_to_favorites') {
                        $found = false;
                        foreach ($_SESSION['favorites'] as $item) {
                            if ($item['id'] == $product['id']) {
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $_SESSION['favorites'][] = $product;
                        }
                    }

                    header("Location: index.php");
                    exit();
                } else {
                    echo "Товар не найден.";
                }
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
    <title>SneakerHome</title>
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
                <form class="search-form" action="index.php" method="get">
                    <input type="text" name="search" placeholder="Поиск товара">
                    <button type="submit">Найти</button>
                </form>
            </nav>
        </header>
        <main>
            <section class="welcome-message">
                <h2>Добро пожаловать в  SneakerShop!</h2>
                <p>В нашем магазине вы найдете широкий ассортимент кроссовок от ведущих мировых брендов. Мы предлагаем только качественную и стильную обувь, которая подойдет как для спорта, так и для повседневной носки. Откройте для себя лучшие предложения и станьте частью нашей большой семьи любителей кроссовок.</p>
            </section>
            <section class="products">
                <h2>Наш ассортимент кроссовок:</h2>
                <div class="list">
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $sql = "SELECT id, name, image, price FROM products WHERE name LIKE ?";
                    $stmt = $conn->prepare($sql);
                    $search_term = "%$search%";
                    $stmt->bind_param('s', $search_term);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows> 0) {
                        while ($product = $result->fetch_assoc()) {
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
                                <form class="add-to-favorites-form" action="index.php" method="post">
                                    <input type="hidden" name="action" value="add_to_favorites">
                                    <input type="hidden" name="product_id" value="' . $product['id'] . '">
                                    <button type="submit">Добавить в избранное</button>
                                </form>
                            </div>';
                        }
                    } else {
                        echo "Нет результатов для вашего запроса.";
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

