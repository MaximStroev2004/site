<?php
session_start();
require_once 'db_connection.php';

$error = "";


if (isset($_SESSION['user_name']) && isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    
    $sql = "SELECT * FROM users WHERE customer_email=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        
        if ($result->num_rows == 1) {
            
            $row = $result->fetch_assoc();
            
            
            if (password_verify($password, $row["password"])) {
                
                
                $_SESSION['user_id'] = $row['id']; 
                $_SESSION['user_name'] = $row['customer_name'];
                $_SESSION['user_email'] = $row['customer_email'];

                
                header("Location: index.php");
                exit(); 
            } else {
                $error = "Неправильный пароль";
            }
        } else {
            $error = "Пользователь с таким email не найден";
        }

        $stmt->close();
    } else {
        $error = "Ошибка в запросе: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="reg.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Вход</h1>
        </header>
        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="input-group">
                    <label for="username">Email</label>
                    <input type="email" id="username" name="username" placeholder="Введите email" required>
                </div>

                <div class="input-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" placeholder="Введите пароль" required>
                </div>

                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <button type="submit">Войти</button>
            </form>
            <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
        </div>
    </div>
</body>
</html>
