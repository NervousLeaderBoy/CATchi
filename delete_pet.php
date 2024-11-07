<?php
// Подключение к базе данных
require_once 'config.php'; 


session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM pets WHERE user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$pet = $stmt->fetch();

// Удаленеи питомца и переход на страницу создания нового
if ($pet) {

    $deleteQuery = "DELETE FROM pets WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([$pet['id']]);

    header('Location: create_pet.php');
    exit;
} else {

    header('Location: create_pet.php');
    exit;
}
?>
