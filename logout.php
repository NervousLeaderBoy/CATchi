<?php
// Выход из аккаунта
session_start();
session_unset();
session_destroy();

header('Location: index.php');
exit();
?>
