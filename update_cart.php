<?php
session_start();
if (isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($product_id <= 0 || $quantity < 0) {
        echo "Invalid product or quantity.";
        exit;
    }

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    header('Location: /index.php?page=cart');
    exit;
} else {
    echo "Product ID and quantity are required.";
    exit;
}
?>