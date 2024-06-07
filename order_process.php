<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connection.php';

session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}


$_SESSION['total_price'] = $total_price;


$user_id = $_SESSION['user_id'];
$date_time = date('Y-m-d H:i:s');


$sql = "INSERT INTO orders (user_id, date_time, total_price) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("iss", $user_id, $date_time, $total_price);


if ($stmt->execute()) {
    echo "Order inserted successfully.<br>";

    
    $order_id = $stmt->insert_id;
    
    
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql);
    if (!$stmt_item) {
        die("Error preparing statement for items: " . $conn->error);
    }
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        
        $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        if ($stmt_item->execute()) {
            echo "Item inserted: Order ID $order_id, Product ID $product_id, Quantity $quantity, Price $price.<br>";
        } else {
            echo "Error inserting item: " . $stmt_item->error . "<br>";
        }
    }
    $stmt_item->close();
    
    
    unset($_SESSION['cart']);
    unset($_SESSION['total_price']);
    
    
    header("Location: profile.php");
    exit();
} else {
    
    echo "Ошибка при добавлении заказа в базу данных: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
