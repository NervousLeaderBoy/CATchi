<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизованный доступ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM pets WHERE user_id = ?");
$stmt->execute([$user_id]);
$pet = $stmt->fetch();

if (!$pet) {
    echo json_encode(['success' => false, 'message' => 'Питомец не найден']);
    exit();
}

// Обновление состояния питомца
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'pet') {
    // Погладить
    $new_happiness = min($pet['happiness'] + 5, 100);
    $stmt = $pdo->prepare("UPDATE pets SET happiness = ? WHERE user_id = ? AND id = ?");
    $stmt->execute([$new_happiness, $user_id, $pet['id']]);
    $pet['happiness'] = $new_happiness;
} elseif ($action == 'wash') {
    // Помыть
    $new_cleanliness = min($pet['cleanliness'] + 5, 100);
    $stmt = $pdo->prepare("UPDATE pets SET cleanliness = ? WHERE user_id = ? AND id = ?");
    $stmt->execute([$new_cleanliness, $user_id, $pet['id']]);
    $pet['cleanliness'] = $new_cleanliness;
}

// Понижалка параметров
if ($_POST['action'] == 'decrease') {
    
    $new_happiness = max(0, $pet['happiness'] - 1);
    $new_cleanliness = max(0, $pet['cleanliness'] - 1);

    $stmt = $pdo->prepare("UPDATE pets SET happiness = ?, cleanliness = ? WHERE user_id = ?");
    $stmt->execute([$new_happiness, $new_cleanliness, $user_id]);
}

// Меняет картинку в зависимости от параметров питмоца
$image = 'pet/happy.png'; 
if ($pet['happiness'] <= 0 && $pet['cleanliness'] <= 0) {
    $image = 'pet/dead.png';
} elseif ($pet['happiness'] < 25 && $pet['cleanliness'] < 50) {
    $image = 'pet/sad_dirty.png';
} elseif ($pet['happiness'] < 25) {
    $image = 'pet/sad.png';
} elseif ($pet['cleanliness'] < 50) {
    $image = 'pet/happy_dirty.png';
}

echo json_encode([
    'success' => true,
    'happiness' => $pet['happiness'],
    'cleanliness' => $pet['cleanliness'],
    'status' => $pet['status'],
    'image' => $image
]);
