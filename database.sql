-- Создание базы данных
CREATE DATABASE logistics;

USE logistics;

-- Таблица пользователей
-- Поля: id (уникальный идентификатор), username (логин), password (пароль в открытом виде), 
-- role (роль: 'admin' или 'logist'), is_blocked (статус блокировки), must_change_password (флаг смены пароля)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('logist', 'admin') NOT NULL,
    is_blocked BOOLEAN DEFAULT FALSE,
    must_change_password BOOLEAN DEFAULT TRUE
);

-- Таблица клиентов
-- Поля: id (уникальный идентификатор), name (имя клиента)
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Таблица продуктов
-- Поля: id (уникальный идентификатор), name (название продукта)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Таблица заказов
-- Поля: id (уникальный идентификатор), order_number (номер заказа), order_date (дата заказа),
-- client_id (ID клиента, внешний ключ), product_id (ID продукта, внешний ключ), quantity (количество)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL,
    order_date DATE NOT NULL,
    client_id INT,
    product_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Тестовые данные для таблицы users
-- Пароли хранятся в открытом виде ("password")
INSERT INTO users (username, password, role, is_blocked, must_change_password) VALUES
('admin', 'password', 'admin', FALSE, FALSE),
('logist', 'password', 'logist', FALSE, FALSE);

-- Тестовые данные для таблицы clients
INSERT INTO clients (name) VALUES
('Клиент 1'),
('Клиент 2'),
('Клиент 3');

-- Тестовые данные для таблицы products
INSERT INTO products (name) VALUES
('Продукт A'),
('Продукт B'),
('Продукт C');

-- Тестовые данные для таблицы orders
INSERT INTO orders (order_number, order_date, client_id, product_id, quantity) VALUES
('ORD001', '2025-06-01', 1, 1, 10),
('ORD002', '2025-06-02', 2, 2, 20),
('ORD003', '2025-06-03', 3, 3, 15);