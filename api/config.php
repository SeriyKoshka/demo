<?php
// Конфигурация подключения к базе данных
$host = '127.0.0.1'; // Хост базы данных
$db = 'logistics';   // Имя базы данных
$user = 'root';      // Пользователь базы данных
$pass = '';          // Пароль базы данных

// Подключение к базе данных
try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>