<?php
session_start();
require_once '../api/config.php';

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['logist', 'admin'])) {
    header("Location: login.php");
    exit();
}

// Запрос для получения данных заказов с объединением таблиц
// Используется LEFT JOIN для связки таблиц orders, clients и products, чтобы отображать заказы даже при отсутствии клиента или продукта
// Измените имена таблиц или полей, если структура базы данных изменится
$result = $conn->query("
    SELECT o.id, o.order_number, o.order_date, c.name AS client_name, p.name AS product_name, o.quantity
    FROM orders o
    LEFT JOIN clients c ON o.client_id = c.id
    LEFT JOIN products p ON o.product_id = p.id
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Заказы</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Номер заказа</th>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Продукт</th>
                    <th>Количество</th>
                    <?php if ($_SESSION['role'] === 'logist'): ?>
                        <th>Действия</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['client_name'] ?? 'Не указан'); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name'] ?? 'Не указан'); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <?php if ($_SESSION['role'] === 'logist'): ?>
                            <td>
                                <!-- Ссылки для редактирования и удаления -->
                                <a href="edit_order.php?id=<?php echo $row['id']; ?>" class="btn-primary">Редактировать</a>
                                <a href="../api/crud.php?action=delete_order&id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Вы уверены, что хотите удалить заказ?')">Удалить</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="button-group">
            <?php if ($_SESSION['role'] === 'logist'): ?>
                <a href="add_order.php" class="btn-primary">Добавить запись</a>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="view_admin.php" class="btn-secondary">Админ-панель</a>
            <?php endif; ?>
            <a href="../api/logout.php" class="btn-secondary">Выйти</a>
        </div>
    </div>
</body>
</html>