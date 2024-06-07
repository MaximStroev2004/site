<?php
session_start();

if (!isset($_SESSION['user_name'], $_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

require_once 'db_connection.php'; 

$sql = "SELECT * FROM order_history WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$_SESSION['order_history'] = [];

while ($row = $result->fetch_assoc()) {
    $_SESSION['order_history'][] = [
        'order_id' => $row['order_id'],
        'order_date' => $row['date_time'],
        'total_price' => $row['total_price']
    ];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Профиль пользователя</h1>
            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="index.php">
                            <span class="icon">
                                <img src="image/home.svg" alt="Главная">
                            </span> 
                            Главная
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <span class="icon">
                                <img src="image/logout.svg" alt="Выход">
                            </span>
                            Выйти
                        </a>
                    </li>
                </ul>
            </nav>
        </header>
        <div class="profile-info">
            <div class="user-details">
                <h2>Данные пользователя</h2>
                <p>Имя: <?php echo htmlspecialchars($user_name); ?></p>
                <p>Email: <?php echo htmlspecialchars($user_email); ?></p>
            </div>
            <div class="order-history">
                <h2>История заказов</h2>
                <?php if (!empty($_SESSION['order_history'])): ?>
                    <?php foreach ($_SESSION['order_history'] as $order): ?>
                        <div class="order">
                            <p>Номер заказа: <?php echo htmlspecialchars($order['order_id']); ?></p>
                            <p>Дата: <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p>Сумма: <?php echo number_format($order['total_price'], 0, '', ' '); ?> руб</p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>У вас пока нет заказов.</p>
                <?php endif; ?>
            </div>
        </div>



   
