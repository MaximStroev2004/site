<?php
include 'db_connection.php';

$error = $success_message = "";

if ($conn === false) {
    $error = "Ошибка соединения с базой данных: " . mysqli_connect_error();
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_name = $_POST["customer_name"];
        $customer_email = $_POST["customer_email"];
        $customer_phone = $_POST["customer_phone"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        
        if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Пожалуйста, введите корректный адрес электронной почты.";
        } else {
            
            $check_email_sql = "SELECT * FROM users WHERE customer_email = '$customer_email'";
            $result = mysqli_query($conn, $check_email_sql);

            if (mysqli_num_rows($result) > 0) {
                $error = "Этот email уже занят";
            } else {
                
                if (!preg_match('/\.(com|ru)$/', $customer_email)) {
                    $error = "Пожалуйста, используйте только адреса электронной почты с доменами .com или .ru без дополнительных символов после них.";
                } else {
                    if ($password !== $confirm_password) {
                        $error = "Пароли не совпадают";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $hashed_confirmed_password = password_hash($confirm_password, PASSWORD_DEFAULT);
                        
                        $sql = "INSERT INTO users (customer_name, customer_email, customer_phone, password, confirmed_password)
                                VALUES ('$customer_name', '$customer_email', '$customer_phone', '$hashed_password', '$hashed_confirmed_password')";

                        if (mysqli_query($conn, $sql)) {
                            $success_message = "Вы успешно зарегистрированы!";
                        } else {
                            $error = "Ошибка при регистрации: " . mysqli_error($conn);
                        }
                    }
                }
            }
        }
    }
}

mysqli_close($conn);
?>
