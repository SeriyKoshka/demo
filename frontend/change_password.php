<?php
session_start();
require_once '../api/config.php';

// Проверка авторизации пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Обработка формы смены пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['skip'])) {
        // Пропуск смены пароля: обновляем флаг must_change_password
        // Измените 'users' или 'must_change_password', если структура таблицы изменится
        $stmt = $conn->prepare("UPDATE users SET must_change_password = 0 WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        unset($_SESSION['success']); // Удаляем сообщение об успешном входе
        redirectByRole($_SESSION['role']);
    } elseif (isset($_POST['change'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Проверка заполненности всех полей
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = "Все поля должны быть заполнены для смены пароля";
        } else {
            // Получение текущего пароля пользователя
            // Измените 'users' или 'password', если структура таблицы изменится
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Проверка корректности введённых данных
            if ($current_password === $user['password'] && $new_password === $confirm_password) {
                // Обновление пароля в базе данных
                // Измените 'users' или 'password', если структура таблицы изменится
                $stmt = $conn->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
                $stmt->bind_param("si", $new_password, $_SESSION['user_id']);
                $stmt->execute();
                unset($_SESSION['success']); // Удаляем сообщение об успешном входе
                redirectByRole($_SESSION['role']);
            } else {
                $_SESSION['error'] = "Ошибка: неверный текущий пароль или пароли не совпадают";
            }
        }
    }
}

// Функция для перенаправления в зависимости от роли
function redirectByRole($role) {
    switch ($role) {
        case 'admin':
            header("Location: view_admin.php");
            break;
        case 'logist':
            header("Location: view_user.php");
            break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Смена пароля</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Смена пароля</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success"><?php echo $_SESSION['success']; ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="current_password" class="form-label">Текущий пароль</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
            </div>
            <div class="form-group">
                <label for="new_password" class="form-label">Новый пароль</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            <div class="form-group">
                <label for="confirm_password" class="form-label">Подтвердите новый пароль</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            <div class="button-group">
                <button type="submit" name="change" class="btn-primary">Сменить пароль</button>
                <button type="submit" name="skip" class="btn-secondary">Пропустить</button>
            </div>
        </form>
    </div>
</body>
</html>