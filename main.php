<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="header">
        <p class = "header-text">Домик</p>
    </div>

    <div class="container">
        <nav class="nav-links">
            <a href="pet.php" class="button">О питомце</a>
            <a href="info.php" class="button">Информация</a>
            <a href="logout.php" class="button">Выйти</a>
        </nav>

        <div class="welcome-message">
            <h2>Мяу! Это домик.</h2>
            <p>Здесь вы можете управлять своим питомцем и узнать больше о проекте Catchi!</p>
        </div>
    </div>
</body>
</html>
