-- ============================================
-- AL-NAAZ FOOD - Complete Database
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS al_naaz_food;
USE al_naaz_food;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255),
    google_id VARCHAR(100),
    phone VARCHAR(15),
    role ENUM('customer', 'owner') DEFAULT 'customer',
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 2. OTP VERIFICATION TABLE
-- ============================================
CREATE TABLE otp_verification (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 3. PRODUCTS TABLE
-- ============================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category ENUM('masala', 'product', 'raw_material', 'dryfruit', 'seasonal') NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    discount_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    image VARCHAR(255),
    is_top_ranked BOOLEAN DEFAULT FALSE,
    is_best_selling BOOLEAN DEFAULT FALSE,
    is_combo BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 4. ORDERS TABLE
-- ============================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    advance_paid DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('pending', 'advance', 'partial', 'full') DEFAULT 'pending',
    delivery_type ENUM('cod', 'prepaid') DEFAULT 'cod',
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- 5. ORDER ITEMS TABLE
-- ============================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- 6. REVIEWS TABLE
-- ============================================
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- 7. ERP EXPENSES TABLE
-- ============================================
CREATE TABLE erp_expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('staff_salary', 'raw_spent', 'bills', 'other') NOT NULL,
    description TEXT,
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 8. VISITOR ANALYTICS TABLE
-- ============================================
CREATE TABLE visitor_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45),
    session_id VARCHAR(100),
    page_visited VARCHAR(255),
    product_viewed INT,
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_viewed) REFERENCES products(id) ON DELETE SET NULL
);

-- ============================================
-- 9. ABANDONED ORDERS TABLE
-- ============================================
CREATE TABLE abandoned_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    amount DECIMAL(10,2),
    status ENUM('viewed', 'added_to_cart', 'payment_started', 'payment_failed') DEFAULT 'viewed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ============================================
-- 10. CONTACT ENQUIRIES TABLE
-- ============================================
CREATE TABLE contact_enquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    message TEXT NOT NULL,
    type ENUM('wholesale', 'catering', 'general', 'review') DEFAULT 'general',
    status ENUM('pending', 'read', 'replied') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 11. CATERING BOOKINGS TABLE
-- ============================================
CREATE TABLE catering_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    event_type VARCHAR(100),
    event_date DATE NOT NULL,
    guest_count INT,
    special_requirements TEXT,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 12. OWNER DETAILS TABLE
-- ============================================
CREATE TABLE owner_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    designation VARCHAR(100),
    bio TEXT,
    email VARCHAR(100),
    phone VARCHAR(15),
    address TEXT,
    image VARCHAR(255),
    social_facebook VARCHAR(255),
    social_instagram VARCHAR(255),
    social_youtube VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 13. WEBSITE SETTINGS TABLE (For Admin)
-- ============================================
CREATE TABLE website_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 14. SEASONAL OFFERS TABLE
-- ============================================
CREATE TABLE seasonal_offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    discount_percent INT,
    start_date DATE,
    end_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 15. HERO SECTION TABLE
-- ============================================
CREATE TABLE hero_section (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('offer', 'achievement') NOT NULL,
    title VARCHAR(200),
    description TEXT,
    image VARCHAR(255),
    button_text VARCHAR(50),
    button_link VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);

-- ============================================
-- Insert Default Owner
-- ============================================
INSERT INTO users (name, email, role, is_verified) 
VALUES ('AL-NAAZ Owner', 'owner@alnaazfood.com', 'owner', TRUE);

-- ============================================
-- Insert Default Owner Details
-- ============================================
INSERT INTO owner_details (name, designation, bio, email, phone, address) 
VALUES (
    'Mohammed Ali',
    'Founder & CEO',
    'Passionate about bringing authentic flavors to every kitchen. With over 15 years of experience in the food industry, AL-NAAZ FOOD is committed to quality and taste.',
    'owner@alnaazfood.com',
    '+91 98765 43210',
    '123, Food Street, Mumbai, Maharashtra, India - 400001'
);

-- ============================================
-- Insert Sample Products
-- ============================================
INSERT INTO products (category, name, description, price, discount_price, stock, image, is_top_ranked, is_best_selling) VALUES
('masala', 'AL-NAAZ Special Biryani Masala', 'Premium blend of 20 authentic spices for perfect biryani', 299.00, 249.00, 100, 'biryani-masala.jpg', TRUE, TRUE),
('masala', 'Royal Chicken Masala', 'Signature masala for rich and flavorful chicken curries', 199.00, 169.00, 150, 'chicken-masala.jpg', TRUE, FALSE),
('masala', 'Tandoori Masala', 'Authentic tandoori spice blend for grilling and roasting', 249.00, 199.00, 80, 'tandoori-masala.jpg', FALSE, TRUE),
('product', 'AL-NAAZ Ready-to-Cook Biryani Kit', 'Complete kit with masala, rice, and instructions', 499.00, 449.00, 50, 'biryani-kit.jpg', FALSE, TRUE),
('product', 'Chicken Curry Kit', 'Everything you need for perfect chicken curry', 399.00, 349.00, 60, 'chicken-kit.jpg', FALSE, FALSE),
('raw_material', 'Premium Basmati Rice', 'Aged basmati rice for perfect biryani and pulao', 899.00, 799.00, 200, 'basmati-rice.jpg', FALSE, TRUE),
('raw_material', 'Pure Ghee', 'Traditional clarified butter for authentic flavors', 599.00, 549.00, 100, 'pure-ghee.jpg', FALSE, FALSE),
('dryfruit', 'Premium Almonds', 'California almonds - rich in nutrients', 699.00, 649.00, 150, 'almonds.jpg', FALSE, TRUE),
('dryfruit', 'Royal Cashews', 'Premium cashew nuts - creamy and rich', 799.00, 749.00, 120, 'cashews.jpg', TRUE, FALSE),
('seasonal', 'Eid Special Gift Pack', 'Exclusive Eid collection with premium dry fruits and masala', 1499.00, 1299.00, 30, 'eid-gift.jpg', FALSE, FALSE);

-- ============================================
-- Insert Sample Hero Section
-- ============================================
INSERT INTO hero_section (type, title, description, image, button_text, button_link, display_order, is_active) VALUES
('offer', 'Festive Special Offer!', 'Get 20% off on all premium masala blends. Limited time offer!', 'offer-banner.jpg', 'Shop Now', '#products', 1, TRUE),
('achievement', '15+ Years of Excellence', 'Serving authentic flavors since 2010', 'achievement-1.jpg', 'Learn More', '#about', 2, TRUE),
('achievement', '10,000+ Happy Customers', 'Trusted by chefs and home cooks across India', 'achievement-2.jpg', 'See Reviews', '#reviews', 3, TRUE),
('achievement', '100+ Authentic Products', 'Wide range of premium spices and ingredients', 'achievement-3.jpg', 'Explore', '#products', 4, TRUE);

-- ============================================
-- Insert Website Settings
-- ============================================
INSERT INTO website_settings (setting_key, setting_value) VALUES
('site_name', 'AL-NAAZ FOOD'),
('site_tagline', 'Premium Spices & Food Essentials'),
('site_logo', 'logo.png'),
('contact_email', 'info@alnaazfood.com'),
('contact_phone', '+91 98765 43210'),
('contact_address', '123, Food Street, Mumbai, Maharashtra, India - 400001'),
('map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3770.000000!2d72.000000!3d19.000000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTnCsDQ1JzAwLjAiTiA3MsKwMDAnMDAuMCJF!5e0!3m2!1sen!2sin!4v1234567890'),
('whatsapp_number', '919876543210'),
('facebook_url', 'https://facebook.com/alnaazfood'),
('instagram_url', 'https://instagram.com/alnaazfood'),
('youtube_url', 'https://youtube.com/alnaazfood');

-- ============================================
-- Insert Sample Seasonal Offers
-- ============================================
INSERT INTO seasonal_offers (title, description, image, discount_percent, start_date, end_date, is_active) VALUES
('Eid Special Masala Collection', 'Premium masala gift packs with 25% off', 'eid-offer.jpg', 25, '2026-01-01', '2026-01-31', TRUE),
('Ramadan Special Combos', 'Special combos for iftar and sehri', 'ramadan-offer.jpg', 20, '2026-02-01', '2026-03-15', TRUE),
('Summer Sale', 'Cooling spices and refreshing blends', 'summer-offer.jpg', 15, '2026-04-01', '2026-05-31', FALSE);