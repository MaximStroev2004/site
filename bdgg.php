<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    require_once 'db_connection.php';

    $sql = "SELECT * FROM customers WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: /profile.php");
            exit();
        } else {
            $error = "Неправильный логин или пароль";
        }
    } else {
        $error = "Неправильный логин или пароль";
    }

    $stmt->close();
    $conn->close();
}
?>
