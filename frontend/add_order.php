<?php
session_start();
require_once '../api/config.php';

// Проверка авторизации и роли логиста
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'logist') {
    header("Location: login.php");
    exit();
}

// Запросы для получения списков клиентов и продуктов
// Измените 'clients' или 'products', если имена таблиц изменятся
$clients = $conn->query("SELECT id, name FROM clients");
$products = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить заказ</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Добавить заказ</h2>
        <form method="post" action="../api/crud.php?action=add_order">
            <div class="form-group">
                <label for="order_number" class="form-label">Номер заказа</label>
                <input type="text" class="form-control" id="order_number" name="order_number" required>
            </div>
            <div class="form-group">
                <label for="order_date" class="form-label">Дата заказа</label>
                <input type="date" class="form-control" id="order_date" name="order_date" required>
            </div>
            <div class="form-group">
                <label for="client_id" class="form-label">Клиент</label>
                <select class="form-control" id="client_id" name="client_id" required>
                    <?php while ($client = $clients->fetch_assoc()): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo $client['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="product_id" class="form-label">Продукт</label>
                <select class="form-control" id="product_id" name="product_id" required>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity" class="form-label">Количество</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-primary">Добавить</button>
                <a href="view_user.php" class="btn-secondary">Назад</a>
            </div>
        </form>
    </div>
</body>
</html>