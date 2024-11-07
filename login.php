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

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: main.php');
        exit();
    } else {
        $error = "Упс! Неверный email или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Войти - Catchi</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="form-container">
    <div class="header">
        <p class = "header-text">Войти</p>
    </div>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Электронная почта" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <p>Нет аккаунта? <a href="register.php">Давай создадим!</a></p>
    </div>
</body>
</html>
