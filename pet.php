<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM pets WHERE user_id = ?");
$stmt->execute([$user_id]);
$pet = $stmt->fetch();

if (!$pet) {
    header('Location: create_pet.php');
    exit();
}

// Переименовать
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename'])) {
    $new_name = trim($_POST['new_name']);
    if (!empty($new_name)) {
        $stmt = $pdo->prepare("UPDATE pets SET name = ? WHERE user_id = ? AND id = ?");
        $stmt->execute([$new_name, $user_id, $pet['id']]);
        $pet['name'] = $new_name;
    }
}

// Удалить
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM pets WHERE user_id = ? AND id = ?");
    $stmt->execute([$user_id, $pet['id']]);
    header('Location: create_pet.php');
    exit();
}
// Меняет картинку
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой кот</title>
    <link rel="stylesheet" href="static/style.css">
    <style>
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .pet-card img {
            max-width: 100%;
            height: auto;
        }
        .pet-card {
            text-align: center;
        }
        .buttons {
            margin-top: 20px;
        }
        .buttons form {
            display: inline-block;
            margin: 5px;
        }
    </style>
    <script>
        function updatePetStats(action) {
        fetch('pet_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=' + action
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновление
                document.getElementById('happiness').textContent = data.happiness + '%';
                document.getElementById('cleanliness').textContent = data.cleanliness + '%';
                document.getElementById('status').textContent = data.status;

                document.getElementById('petImage').src = data.image;
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка обновления данных:', error);
        });
    }

    // Понижалка раз в минуту
    function decreaseStatsAutomatically() {
        setInterval(() => {
            fetch('pet_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=decrease'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('happiness').textContent = data.happiness + '%';
                    document.getElementById('cleanliness').textContent = data.cleanliness + '%';
                    document.getElementById('status').textContent = data.status;

                    document.getElementById('petImage').src = data.image;
                } else {
                    console.error('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка изменения параметров:', error);
            });
        }, 60000);  
    }

    decreaseStatsAutomatically();

    function showRenameForm() {
            document.getElementById('rename-form').style.display = 'block';
            document.getElementById('rename-btn').style.display = 'none'; 
        }

        function confirmRename(event) {
            const newName = document.getElementById('new_name').value;
            if (!newName || !confirm('Точно хотите переименовать питомца?')) {
                event.preventDefault(); 
            }
        }
    </script>
</head>
<body>
<div class="container">
<form action="index.php" method="get">
    <!-- Кнопка домой -->
    <button type="submit" class="home-button"> 
        <img src="icons/home.png" alt="Домик" class="home-icon">
    </button>
</form>

    <div class="pet-card">
        <h2>Меня зовут <?= htmlspecialchars($pet['name']) ?>!</h2>
        <img id="petImage" src="<?= $image ?>" alt="Питомец">
        <p>Счастье: <span id="happiness"><?= $pet['happiness'] ?>%</span></p>
        <p>Чистота: <span id="cleanliness"><?= $pet['cleanliness'] ?>%</span></p>
        <p>Статус: <span id="status"><?= htmlspecialchars($pet['status']) ?></span></p>

        <div class="buttons">
            <button onclick="updatePetStats('pet')">Погладить</button>
            <button onclick="updatePetStats('wash')">Помыть</button>

            <button id="rename-btn" onclick="showRenameForm()">Дать другое имя</button>

            <form id="rename-form" method="POST" action="" style="display: none;" onsubmit="confirmRename(event)">
                <input type="text" name="new_name" id="new_name" placeholder="Новое имя" required>
                <button type="submit" name="rename">Переименовать</button>
            </form>
            <form method="POST" action="" onsubmit="return confirm('Вы уверены, что хотите удалить питомца? Вернуть его будет нельзя.');">
                    <button type="submit" name="delete" class="delete-btn">Удалить питомца :[</button>
                </form>
        </div>
    </div>
</div>
</body>
</html>
