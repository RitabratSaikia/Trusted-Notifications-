-- Create database (run once, or create manually)
CREATE DATABASE IF NOT EXISTS trusted_notifications;
USE trusted_notifications;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    password VARCHAR(255) NOT NULL
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    customer_id INT NOT NULL,
    channel ENUM('SMS','Email','App') NOT NULL,
    message TEXT NOT NULL,
    otp_code VARCHAR(10),
    anti_spoof_token VARCHAR(64),
    status ENUM('pending','sent','delivered','failed','phishing_reported') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Delivery logs table (retry attempts etc.)
CREATE TABLE delivery_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    attempt_no INT NOT NULL,
    channel ENUM('SMS','Email','App') NOT NULL,
    status ENUM('pending','sent','delivered','failed') DEFAULT 'pending',
    detail VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
);

-- Insert sample admin and customers (password = 'password')
INSERT INTO users (name, email, phone, role, password) VALUES
('Admin User', 'admin@example.com', '9999999999', 'admin', '$2y$10$ULQk5n9R0ad/iEPrxSxLweQqtOQY3l5kI7sM1dR.P4Ghd2u5ygNwW'),
('Alice Customer', 'alice@example.com', '9000000001', 'customer', '$2y$10$ULQk5n9R0ad/iEPrxSxLweQqtOQY3l5kI7sM1dR.P4Ghd2u5ygNwW'),
('Bob Customer', 'bob@example.com', '9000000002', 'customer', '$2y$10$ULQk5n9R0ad/iEPrxSxLweQqtOQY3l5kI7sM1dR.P4Ghd2u5ygNwW');

-- The bcrypt hash above corresponds to the plaintext password: password
