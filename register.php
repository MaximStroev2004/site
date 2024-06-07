<?php
include 'db_connection.php';

$error = $success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST["customer_name"];
    $customer_email = $_POST["customer_email"];
    $customer_phone = $_POST["customer_phone"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    
    if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Пожалуйста, введите корректный адрес электронной почты.";
    } elseif (!preg_match('/\.com$|\.ru$/', $customer_email)) {
        $error = "Пожалуйста, используйте только адреса электронной почты с доменами .com или .ru.";
    } else {
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE customer_email = ?");
        $stmt->bind_param("s", $customer_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Этот email уже занят";
        } else {
            if ($password !== $confirm_password) {
                $error = "Пароли не совпадают";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users (customer_name, customer_email, customer_phone, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $customer_name, $customer_email, $customer_phone, $hashed_password);

                if ($stmt->execute()) {
                    $success_message = "Вы успешно зарегистрированы!";
                } else {
                    $error = "Ошибка при регистрации: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="reg.css">
</head>
  <body>
    <div class="container">
        <header>
            <h1>Регистрация</h1>
        </header>
        <div class="form-container">
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <p class="success"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <form method="post" action="">
                <div class="input-group">
                    <label for="customer_name">Имя</label>
                    <input type="text" id="customer_name" name="customer_name" placeholder="Введите имя" required>
                </div>

                <div class="input-group">
                    <label for="customer_email">Email</label>
                    <input type="email" id="customer_email" name="customer_email" placeholder="Введите email" required>
                </div>

                <div class="input-group">
                    <label for="customer_phone">Телефон</label>
                    <input type="text" id="customer_phone" name="customer_phone" placeholder="Введите телефон" required>
                </div>

                <div class="input-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" placeholder="Введите пароль" required>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Подтвердите Пароль</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Подтвердите пароль" required>
                </div>

                <button type="submit">Зарегистрироваться</button>
            </form>
            <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
        </div>
    </div>
</body>
</html>

