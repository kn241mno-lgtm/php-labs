-- Users table (auth module)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) DEFAULT '',
    city VARCHAR(50) DEFAULT '',
    gender VARCHAR(10) DEFAULT '',
    about TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Products table (CRUD module)
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    category VARCHAR(50) DEFAULT '',
    description TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seed products
INSERT INTO products (name, price, category, description) VALUES
    ('Ноутбук Lenovo IdeaPad', 18999.00, 'Електроніка', 'Ноутбук для навчання та роботи, 15.6", 8GB RAM'),
    ('Навушники Sony WH-1000XM5', 8499.00, 'Електроніка', 'Бездротові навушники з активним шумозаглушенням'),
    ('Рюкзак Xiaomi Mi Urban', 1299.00, 'Аксесуари', 'Міський рюкзак для ноутбука до 15.6"'),
    ('Клавіатура Logitech K380', 1599.00, 'Периферія', 'Бездротова клавіатура для 3 пристроїв'),
    ('Мишка Logitech MX Master 3S', 3299.00, 'Периферія', 'Ергономічна бездротова мишка'),
    ('Монітор Samsung 27"', 7999.00, 'Електроніка', 'IPS монітор, 2560x1440, 75Hz'),
    ('USB-хаб Anker 7-в-1', 1199.00, 'Аксесуари', 'USB-C хаб з HDMI, USB 3.0, SD-картрідером'),
    ('Настільна лампа Xiaomi', 899.00, 'Аксесуари', 'LED лампа з регулюванням яскравості та температури'),
    ('Веб-камера Logitech C920', 2499.00, 'Периферія', 'Full HD веб-камера з автофокусом'),
    ('SSD Samsung 970 EVO 500GB', 2199.00, 'Електроніка', 'NVMe SSD для швидкого завантаження системи');
