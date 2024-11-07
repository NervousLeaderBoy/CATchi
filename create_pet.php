<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_name = $_POST['pet_name'];

    $stmt = $pdo->prepare("SELECT * FROM pets WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existing_pet = $stmt->fetch();

    if (!$existing_pet) {
        $stmt = $pdo->prepare("INSERT INTO pets (user_id, name, happiness, cleanliness) VALUES (?, ?, 100, 100)");
        $stmt->execute([$user_id, $pet_name]);
        header('Location: pet.php');
        exit();
    } else {
        header('Location: pet.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание питомца - Catchi</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="container">
        <h2>Создание питомца</h2>
        <form method="post">
            <label>Имя питомца: <input type="text" name="pet_name" required></label><br>
            <button type="submit">Продолжить</button>
        </form>
    </div>
</body>
</html>

