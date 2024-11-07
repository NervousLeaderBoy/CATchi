<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = "Пароли не совпадают.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            $error = "Пользователь с таким email уже существует.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
            $stmt->execute([$email, $hashed_password]);
            header('Location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Catchi</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="form-container">
    <div class="header">
        <p class = "header-text">Зарегистрироваться!</p>
    </div>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Электронная почта" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="password" name="password_confirm" placeholder="Подтвердите пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>
