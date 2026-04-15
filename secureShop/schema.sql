CREATE DATABASE IF NOT EXISTS secureshop;
USE secureshop;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_ref VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (username, email, password, role) VALUES
('alice', 'alice@secureshop.local', 'password', 'user'),
('bob', 'bob@secureshop.local', 'password', 'user'),
('admin', 'admin@secureshop.local', 'A7kP3mQ9xT2vL8nR4cY1', 'admin')
ON DUPLICATE KEY UPDATE username = username;

UPDATE users
SET role = 'user'
WHERE username <> 'admin';

UPDATE users
SET role = 'admin'
WHERE username = 'admin';

INSERT INTO products (name, category, description, price, image) VALUES
('Gaming Laptop Pro', 'laptops', 'High-performance gaming laptop with RGB keyboard', 1299.99, 'assets/Gaming Laptop Pro.png'),
('Mechanical Gaming Keyboard', 'accessories', 'RGB mechanical keyboard with cherry MX switches', 149.99, 'assets/Mechanical Gaming Keyboard.png'),
('Gaming Mouse Ultra', 'accessories', 'High-DPI gaming mouse with customizable buttons', 79.99, 'assets/Gaming Mouse Ultra.png'),
('4K Gaming Monitor', 'monitors', '27-inch 4K monitor with 144Hz refresh rate', 599.99, 'assets/4K Gaming Monitor.png'),
('Gaming Headset Pro', 'audio', 'Surround sound gaming headset with noise cancellation', 199.99, 'assets/Gaming Headset Pro.png'),
('Graphics Card RTX', 'components', 'Latest generation graphics card for 4K gaming', 899.99, 'assets/Graphics Card RTX..png');

INSERT INTO invoices (user_id, order_ref, content) VALUES
(1, 'INV-001', 'Invoice INV-001\nCustomer: alice\nItems: Gaming Laptop Pro\nTotal: $1299.99'),
(2, 'INV-002', 'Invoice INV-002\nCustomer: bob\nItems: Gaming Mouse Ultra + Keyboard\nTotal: $229.98'),
(1, 'INV-003', 'Invoice INV-003\nCustomer: alice\nItems: Gaming Headset Pro\nTotal: $199.99');

INSERT INTO invoices (user_id, order_ref, content)
SELECT id, 'INV-004', 'Invoice INV-004\nCustomer: admin\nItems: Secure Admin Toolkit\nTotal: $0.00'
FROM users
WHERE username = 'admin';

