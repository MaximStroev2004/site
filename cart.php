<?php
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $product_id = $_POST['product_id'];

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity']++;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1 
            ];
        }
    }

    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    $product_id = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity']));

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] = $quantity;
            break;
        }
    }

    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_cart') {
    $product_id = $_POST['product_id'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    header("Location: cart.php");
    exit();
}

if (!isset($_SESSION['user_name'], $_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $total_price = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    $user_email = $_SESSION['user_email'];
    $date_time = date('Y-m-d H:i:s');

    $conn->begin_transaction();

    try {
        $sql_order = "INSERT INTO orders (user_email, date_time, total_price) VALUES (?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("ssd", $user_email, $date_time, $total_price);
        $stmt_order->execute();

        $order_id = $stmt_order->insert_id;

        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);

        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_item->execute();
        }

        $sql_history = "INSERT INTO order_history (user_email, order_id, date_time, total_price) VALUES (?, ?, ?, ?)";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->bind_param("sisd", $user_email, $order_id, $date_time, $total_price);
        $stmt_history->execute();

        $conn->commit();

        $_SESSION['cart'] = [];

        header("Location: profile.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Корзина</h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php"><span class="icon"><img src="image/home.svg" alt="Главная"></span> Главная</a></li>
                </ul>
            </nav>
        </header>
        <div class="cart">
            <ul class="listCard">
                <?php if (empty($_SESSION['cart'])): ?>
                    <li>Ваша корзина пуста.</li>
                <?php else: ?>
                    <?php
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $item):
                        $total_price += $item['price'] * $item['quantity'];
                    ?>
                        <li>
                            <img src="image/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div><?php echo htmlspecialchars($item['name']); ?></div>
                            <div><?php echo number_format($item['price'], 0, '', ' '); ?> руб</div>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="update_quantity">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                                <button type="submit">Обновить</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="remove_from_cart">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <button type="submit">Удалить</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="checkOut">
                    <div class="total">
                        Итого: <?php echo number_format($total_price, 0, '', ' '); ?> руб
                    </div>
                    <form method="post">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="order-button">Оформить заказ</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
