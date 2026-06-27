-- =============================================
-- E-Commerce Database Schema
-- Ye file XAMPP ke phpMyAdmin mein run karein
-- =============================================

CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Users table - customers aur admin dono ke liye
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table - product categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table - saare products yahan hain
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    image_url VARCHAR(500),
    stock INT DEFAULT 0,
    rating DECIMAL(3,1) DEFAULT 4.5,
    reviews_count INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders table - customer orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100),            -- Direct order ka naam (guest/logged-in dono ke liye)
    customer_phone VARCHAR(20),            -- Phone number
    customer_city VARCHAR(100),            -- City
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT,
    notes TEXT,                            -- Special instructions
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table - har order mein kya kya tha
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Cart table - shopping cart data
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100),
    user_id INT NULL,
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Product Reviews table - har review alag row
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    order_id INT,
    user_id INT,
    reviewer_name VARCHAR(100) NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    is_verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Ek order per product sirf ek review
CREATE UNIQUE INDEX IF NOT EXISTS idx_order_product_review 
    ON product_reviews(order_id, product_id);

-- =============================================
-- Sample Data
-- =============================================

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Categories
INSERT INTO categories (name, slug, icon) VALUES
('Electronics', 'electronics', 'fas fa-laptop'),
('Fashion', 'fashion', 'fas fa-tshirt'),
('Home & Living', 'home-living', 'fas fa-couch'),
('Sports', 'sports', 'fas fa-dumbbell'),
('Beauty', 'beauty', 'fas fa-spa'),
('Books', 'books', 'fas fa-book');

-- Products with Unsplash images
INSERT INTO products (category_id, name, slug, description, price, sale_price, image_url, stock, rating, reviews_count, is_featured) VALUES
(1, 'Premium Wireless Headphones', 'premium-wireless-headphones', 'Crystal clear sound with active noise cancellation. 30-hour battery life, premium comfort ear cushions.', 12999.00, 9999.00, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80', 50, 4.8, 2340, 1),
(1, 'Smart Watch Pro', 'smart-watch-pro', 'Advanced fitness tracking, heart rate monitor, GPS, and 7-day battery life.', 24999.00, 19999.00, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80', 30, 4.7, 1890, 1),
(1, 'Laptop Ultra Slim', 'laptop-ultra-slim', '15.6" Full HD display, Intel Core i7, 16GB RAM, 512GB SSD. Perfect for professionals.', 89999.00, 79999.00, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&q=80', 20, 4.9, 567, 1),
(1, 'Wireless Earbuds', 'wireless-earbuds', 'True wireless stereo with 24hr total playtime, IPX5 water resistance, and touch controls.', 4999.00, 3499.00, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600&q=80', 100, 4.6, 3200, 0),
(1, '4K Action Camera', '4k-action-camera', '4K video at 60fps. Waterproof up to 30m, image stabilization, wide-angle lens.', 15999.00, 12999.00, 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=600&q=80', 40, 4.5, 890, 0),
(2, 'Classic Leather Jacket', 'classic-leather-jacket', 'Genuine leather jacket with timeless design. Available in multiple colors and sizes.', 8999.00, 6999.00, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600&q=80', 60, 4.7, 445, 1),
(2, 'Running Sneakers', 'running-sneakers', 'Lightweight and breathable with superior cushioning for all terrains.', 5999.00, 4499.00, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80', 80, 4.6, 1230, 1),
(2, 'Designer Sunglasses', 'designer-sunglasses', 'UV400 polarized lenses. Stylish frame for men and women.', 3499.00, 2499.00, 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80', 120, 4.4, 678, 0),
(3, 'Modern Floor Lamp', 'modern-floor-lamp', 'Contemporary arc floor lamp with adjustable brightness.', 6999.00, 5499.00, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&q=80', 35, 4.5, 234, 0),
(3, 'Ceramic Coffee Mug Set', 'ceramic-coffee-mug-set', 'Set of 4 premium ceramic mugs. Microwave and dishwasher safe.', 1999.00, 1499.00, 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=600&q=80', 200, 4.8, 890, 0),
(4, 'Yoga Mat Premium', 'yoga-mat-premium', 'Extra thick 6mm non-slip mat with alignment lines. Eco-friendly material.', 2499.00, 1999.00, 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=600&q=80', 150, 4.7, 1560, 0),
(4, 'Dumbbell Set', 'dumbbell-set', 'Adjustable 5kg to 25kg set. Space-saving design with anti-roll feature.', 8999.00, 7499.00, 'https://images.unsplash.com/photo-1517963879433-6ad2b056d712?w=600&q=80', 45, 4.6, 445, 1),
(5, 'Luxury Perfume', 'luxury-perfume', 'Long-lasting floral fragrance with rose, jasmine, and sandalwood notes. 100ml.', 4999.00, 3999.00, 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600&q=80', 70, 4.9, 2100, 1),
(6, 'Web Development Guide', 'web-development-guide', 'Complete guide to modern web development. Covers HTML, CSS, JS, PHP, and databases.', 1299.00, 999.00, 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=600&q=80', 500, 4.8, 3400, 0);
