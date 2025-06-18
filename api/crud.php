<?php
session_start();
require_once 'config.php';

// Проверка авторизации для операций, требующих доступа
$protected_actions = ['add_order', 'edit_order', 'delete_order', 'add_user', 'delete_user', 'toggle_block'];
if (in_array($_REQUEST['action'], $protected_actions) && !isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Обработка CRUD-действий
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'add_order':
        // Проверка роли логиста
        if ($_SESSION['role'] !== 'logist') {
            header("Location: ../frontend/login.php");
            exit();
        }
        // Получение данных из формы
        $order_number = $_POST['order_number'];
        $order_date = $_POST['order_date'];
        $client_id = $_POST['client_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        // SQL-запрос для добавления нового заказа
        // Измените 'orders' или имена полей, если структура таблицы изменится
        $stmt = $conn->prepare("INSERT INTO orders (order_number, order_date, client_id, product_id, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $order_number, $order_date, $client_id, $product_id, $quantity);
        $stmt->execute();
        header("Location: ../frontend/view_user.php");
        break;

    case 'edit_order':
        // Проверка роли логиста
        if ($_SESSION['role'] !== 'logist') {
            header("Location: ../frontend/login.php");
            exit();
        }
        // Получение данных из формы
        $id = $_POST['id'];
        $order_number = $_POST['order_number'];
        $order_date = $_POST['order_date'];
        $client_id = $_POST['client_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        // SQL-запрос для обновления заказа
        // Измените 'orders' или имена полей, если структура таблицы изменится
        $stmt = $conn->prepare("UPDATE orders SET order_number = ?, order_date = ?, client_id = ?, product_id = ?, quantity = ? WHERE id = ?");
        $stmt->bind_param("ssiiii", $order_number, $order_date, $client_id, $product_id, $quantity, $id);
        $stmt->execute();
        header("Location: ../frontend/view_user.php");
        break;

    case 'delete_order':
        // Проверка роли логиста
        if ($_SESSION['role'] !== 'logist') {
            header("Location: ../frontend/login.php");
            exit();
        }
        // Получение ID заказа
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            // Если ID не передан или некорректен, перенаправляем с ошибкой
            $_SESSION['error'] = "Неверный идентификатор заказа";
            header("Location: ../frontend/view_user.php");
            exit();
        }
        // Проверка существования заказа
        $stmt = $conn->prepare("SELECT id FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            // Если заказ не найден, перенаправляем с ошибкой
            $_SESSION['error'] = "Заказ не найден";
            header("Location: ../frontend/view_user.php");
            exit();
        }
        // SQL-запрос для удаления заказа
        // Измените 'orders' или 'id', если структура таблицы изменится
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Заказ успешно удалён";
        } else {
            $_SESSION['error'] = "Ошибка при удалении заказа";
        }
        header("Location: ../frontend/view_user.php");
        break;

    case 'add_user':
        // Проверка роли администратора
        if ($_SESSION['role'] !== 'admin') {
            header("Location: ../frontend/login.php");
            exit();
        }
        // Получение данных из формы
        $username = $_POST['username'];
        $password = $_POST['password']; // Пароль без хеширования
        $role = $_POST['role'];
        // SQL-запрос для добавления нового пользователя
        // Измените 'users' или имена полей, если структура таблицы изменится
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, must_change_password) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        header("Location: ../frontend/manage_users.php");
        break;

    case 'delete_user':
        // Проверка роли администратора
        if ($_SESSION['role'] !== 'admin') {
            header("Location: ../frontend/login.php");
            exit();
        }
        // Получение ID пользователя
        $id = $_GET['id'];
        // SQL-запрос для удаления пользователя
        // Измените 'users' или 'id', если структура таблицы изменится
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: ../frontend/manage_users.php");
        break;

    case 'toggle_block':
        // Проверка роли администратора
        if ($_SESSION['role'] !== 'admin') {
            header("Location: ../frontend/login.php");
            exit();
        }
        // Получение ID пользователя и нового статуса
        $id = $_POST['user_id'];
        $is_blocked = $_POST['is_blocked'];
        // SQL-запрос для изменения статуса блокировки
        // Измените 'users' или 'is_blocked', если структура таблицы изменится
        $stmt = $conn->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_blocked, $id);
        $stmt->execute();
        header("Location: ../frontend/manage_users.php");
        break;

    default:
        // Если действие не распознано, перенаправляем на страницу логина
        header("Location: ../frontend/login.php");
        break;
}
exit();
?>