<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="header">
    <p class = "header-text">Это CATchi!</p>
    </div>    
    <div class="container">
        <nav class="nav-links">
            <a href="login.php" class="button">Войти</a>
            <a href="register.php" class="button">Зарегистрироваться</a>
        </nav>
        <div class="about-section">
            <h2>Мяу?</h2>
            <p>CATchi - это ваш персональный питомец, за которым нужно ухаживать и следить, чтобы он оставался всегда счастливым и здоровым! </p>
        </div>
    </div>
</body>
</html>
