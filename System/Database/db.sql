CREATE TABLE orders (
    canteen VARCHAR(255) NULL,
    email VARCHAR(255) NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    note TEXT NULL,
    order_date DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    order_status ENUM('pending', 'in progress', 'completed', 'cancelled') NOT NULL,
    section VARCHAR(255) NULL,
    total_price DECIMAL(10,2) NOT NULL,
    track VARCHAR(255) NULL,
    username VARCHAR(255) NOT NULL
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(255) NOT NULL,
    order_id INT NOT NULL,
    price DECIMAL(10,2) NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

CREATE TABLE order_notes (
    note TEXT NULL,
    note_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE SET NULL
);

CREATE TABLE users (
    contact_number VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) UNIQUE NULL,
    grade_level VARCHAR(255) NOT NULL,
    id INT PRIMARY KEY AUTO_INCREMENT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    school_valid TINYINT(1) NOT NULL,
    section VARCHAR(255) NOT NULL,
    total_orders INT NULL,
    total_users INT NULL,
    track VARCHAR(255) NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    usertype VARCHAR(255) NOT NULL DEFAULT 'user'
);

CREATE TABLE activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(255),
    action VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read', 'done') DEFAULT 'unread'
);

CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    stock_quantity INT NOT NULL
);