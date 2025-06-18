<?php
session_start();
require_once '../api/config.php';

// Проверка авторизации и роли логиста
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'logist') {
    header("Location: login.php");
    exit();
}

// Получение ID заказа из GET-параметра
$id = $_GET['id'];

// Запрос для получения данных заказа по ID
// Измените 'orders' или имена полей, если структура таблицы изменится
$stmt = $conn->prepare("
    SELECT o.id, o.order_number, o.order_date, o.client_id, o.product_id, o.quantity
    FROM orders o WHERE o.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Запросы для получения списков клиентов и продуктов
// Измените 'clients' или 'products', если имена таблиц изменятся
$clients = $conn->query("SELECT id, name FROM clients");
$products = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать заказ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Редактировать заказ</h2>
        <form method="post" action="../api/crud.php?action=edit_order">
            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
            <div class="form-group">
                <label for="order_number" class="form-label">Номер заказа</label>
                <input type="text" class="form-control" id="order_number" name="order_number" value="<?php echo $order['order_number']; ?>" required>
            </div>
            <div class="form-group">
                <label for="order_date" class="form-label">Дата заказа</label>
                <input type="date" class="form-control" id="order_date" name="order_date" value="<?php echo $order['order_date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="client_id" class="form-label">Клиент</label>
                <select class="form-control" id="client_id" name="client_id" required>
                    <?php while ($client = $clients->fetch_assoc()): ?>
                        <option value="<?php echo $client['id']; ?>" <?php if ($client['id'] == $order['client_id']) echo 'selected'; ?>><?php echo $client['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="product_id" class="form-label">Продукт</label>
                <select class="form-control" id="product_id" name="product_id" required>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <option value="<?php echo $product['id']; ?>" <?php if ($product['id'] == $order['product_id']) echo 'selected'; ?>><?php echo $product['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity" class="form-label">Количество</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $order['quantity']; ?>" required>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-primary">Сохранить</button>
                <a href="view_user.php" class="btn-secondary">Назад</a>
            </div>
        </form>
    </div>
</body>
</html>