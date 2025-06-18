<?php
session_start();
// Завершение сессии и перенаправление на страницу логина
session_destroy();
header("Location: ../frontend/login.php");
exit();
?>