-- =============================================
-- UPDATE SCRIPT - New features ke liye
-- Pehle se existing database mein run karein
-- phpMyAdmin mein ecommerce_db select karke run karein
-- =============================================

USE ecommerce_db;

-- Orders table mein extra columns add karo
ALTER TABLE orders 
    ADD COLUMN IF NOT EXISTS customer_name VARCHAR(100) AFTER user_id,
    ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20) AFTER customer_name,
    ADD COLUMN IF NOT EXISTS customer_city VARCHAR(100) AFTER customer_phone,
    ADD COLUMN IF NOT EXISTS notes TEXT AFTER shipping_address;

-- Product Reviews/Ratings table - alag table banao har review ke liye
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    order_id INT,                          -- Kis order ke baad review kiya
    user_id INT,                           -- Kaun ne review kiya (NULL = guest)
    reviewer_name VARCHAR(100) NOT NULL,   -- Review karne wale ka naam
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),  -- 1 to 5 stars
    review_text TEXT,                      -- Optional comment
    is_verified TINYINT(1) DEFAULT 1,      -- Verified purchase
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Ek order sirf ek baar rate ho sake per product
CREATE UNIQUE INDEX IF NOT EXISTS idx_order_product_review 
    ON product_reviews(order_id, product_id);
