<?php
session_start();
require_once '../api/config.php';

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Админ-панель</h2>
        <div class="button-group">
            <a href="view_user.php" class="btn-primary">Главная страница</a>
            <a href="manage_users.php" class="btn-primary">Управление пользователями</a>
            <a href="../api/logout.php" class="btn-secondary">Выйти</a>
        </div>
    </div>
</body>
</html>