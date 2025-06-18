<?php
session_start();
require_once '../api/config.php';

// Инициализация счётчика попыток входа
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Обработка формы авторизации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL-запрос для получения пользователя по логину
    // Измените 'users' или имена полей, если структура таблицы изменится
    $stmt = $conn->prepare("SELECT id, username, password, role, is_blocked, must_change_password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Проверка логина, пароля и статуса блокировки
    if ($user && $password === $user['password'] && !$user['is_blocked']) {
        $_SESSION['login_attempts'] = 0; // Сброс попыток при успешном входе
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        // Установка флага успешного входа для change_password.php
        $_SESSION['success'] = "Вы успешно вошли";

        // Перенаправление на страницу смены пароля, если требуется
        if ($user['must_change_password']) {
            header("Location: change_password.php");
            exit();
        } else {
            redirectByRole($user['role']);
        }
    } else {
        $_SESSION['login_attempts']++;
        // Блокировка после 3 неудачных попыток
        if ($_SESSION['login_attempts'] >= 3) {
            if ($user) {
                // SQL-запрос для блокировки пользователя
                // Измените 'users' или 'is_blocked', если структура таблицы изменится
                $stmt = $conn->prepare("UPDATE users SET is_blocked = 1 WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
            }
            $_SESSION['error'] = "Слишком много неудачных попыток, пользователь заблокирован, обратитесь к администратору";
            $_SESSION['login_attempts'] = 0;
        } else {
            $_SESSION['error'] = "Неверный логин или пароль";
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
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Авторизация</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <div class="form-group" style="text-align: center;">
                <p>Осталось попыток до блокировки: <?php echo 3 - $_SESSION['login_attempts']; ?></p>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="username" class="form-label">Логин</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="button-group">
                <button type="submit" name="login" class="btn-primary">Войти</button>
            </div>
        </form>
    </div>
</body>
</html>