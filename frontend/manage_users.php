<?php
session_start();
require_once '../api/config.php';

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Запрос для получения списка всех пользователей
// Измените 'users' или имена полей, если структура таблицы изменится
$result = $conn->query("SELECT id, username, role, is_blocked FROM users");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Управление пользователями</h2>
        <form method="post" action="../api/crud.php?action=add_user" class="mb-4">
            <div class="row">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" name="username" placeholder="Логин" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="password" class="form-control" name="password" placeholder="Пароль" required>
                </div>
                <div class="form-group col-md-2">
                    <select class="form-control" name="role" required>
                        <option value="logist">Логист</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>
                <div class="col-md-2 add-user-btn">
                    <button type="submit" name="add_user" class="btn-primary">Добавить</button>
                </div>
            </div>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <form method="post" action="../api/crud.php?action=toggle_block">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select class="form-control" name="is_blocked" onchange="this.form.submit()">
                                    <option value="0" <?php if (!$user['is_blocked']) echo 'selected'; ?>>Активен</option>
                                    <option value="1" <?php if ($user['is_blocked']) echo 'selected'; ?>>Заблокирован</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="../api/crud.php?action=delete_user&id=<?php echo $user['id']; ?>" class="btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="button-group">
            <a href="view_admin.php" class="btn-secondary">Назад</a>
        </div>
    </div>
</body>
</html>