-- ============================================
-- CITY STAY HOTEL - Complete Database Schema
-- ============================================

DROP DATABASE IF EXISTS citystay;
CREATE DATABASE citystay;
USE citystay;

-- ============================================
-- 1. ADMINS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 2. ROOMS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    amenities TEXT,
    description TEXT,
    image VARCHAR(255),
    status ENUM('available', 'occupied', 'maintenance', 'reserved') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 3. GUESTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    id_proof_type VARCHAR(50),
    id_proof_number VARCHAR(50),
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 4. BOOKINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE,
    room_id INT NOT NULL,
    guest_id INT,
    guest_name VARCHAR(100),
    guest_email VARCHAR(100),
    guest_phone VARCHAR(20),
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    num_adults INT DEFAULT 1,
    num_children INT DEFAULT 0,
    total_amount DECIMAL(10,2),
    paid_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE SET NULL
);

-- ============================================
-- 5. STAFF TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    salary DECIMAL(10,2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 6. FACILITIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    icon VARCHAR(100),
    description TEXT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 7. CAROUSEL TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS carousel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    title VARCHAR(100),
    description TEXT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 8. CONTACT DETAILS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS contactdetails (
    id INT PRIMARY KEY DEFAULT 1,
    address TEXT,
    phone1 VARCHAR(20),
    phone2 VARCHAR(20),
    email VARCHAR(100),
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    map_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 9. TESTIMONIALS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    rating INT DEFAULT 5,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 10. USERS TABLE (for user panel)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Default Admin (username: admin, password: admin123)
INSERT INTO admins (username, password, full_name, email, role, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@citystay.com', 'admin', 'active');

-- Sample Rooms
INSERT INTO rooms (room_number, room_type, price, capacity, amenities, description, status) VALUES 
('101', 'Deluxe', 3500.00, 2, 'WiFi, AC, TV, Mini Bar', 'A comfortable deluxe room with all modern amenities', 'available'),
('102', 'Deluxe', 3500.00, 2, 'WiFi, AC, TV, Mini Bar', 'A comfortable deluxe room with all modern amenities', 'available'),
('103', 'Suite', 6500.00, 4, 'WiFi, AC, TV, Balcony, Mini Bar', 'Spacious suite with a beautiful balcony view', 'available'),
('104', 'Standard', 2000.00, 2, 'WiFi, AC, TV', 'Basic room with all essential amenities', 'available'),
('105', 'Standard', 2000.00, 2, 'WiFi, AC, TV', 'Basic room with all essential amenities', 'occupied'),
('106', 'Deluxe', 3500.00, 2, 'WiFi, AC, TV, Mini Bar', 'A comfortable deluxe room with all modern amenities', 'available'),
('107', 'Suite', 6500.00, 4, 'WiFi, AC, TV, Balcony, Mini Bar', 'Spacious suite with a beautiful balcony view', 'maintenance');

-- Sample Facilities
INSERT INTO facilities (name, icon, description) VALUES 
('Free WiFi', 'wifi.svg', 'High-speed internet access throughout the hotel'),
('Swimming Pool', 'pool.svg', 'Outdoor swimming pool with sun loungers'),
('Fitness Center', 'gym.svg', 'Modern gym equipment for your workout'),
('Parking', 'parking.svg', 'Complimentary valet parking service'),
('Spa', 'spa.svg', 'Relaxing spa and massage services'),
('Restaurant', 'restaurant.svg', 'Multi-cuisine restaurant with buffet'),
('Bar', 'bar.svg', 'Exclusive bar with premium beverages'),
('Conference Room', 'conference.svg', 'Well-equipped meeting and conference rooms');

-- Sample Carousel Images
INSERT INTO carousel (image, title, description) VALUES 
('1.png', 'Welcome to City Stay', 'Experience luxury like never before'),
('2.png', 'Luxury Rooms', 'Designed for your comfort'),
('3.png', 'Amazing Facilities', 'Everything you need for a perfect stay'),
('4.png', 'Great Location', 'In the heart of the city');

-- Contact Details
INSERT INTO contactdetails (id, address, phone1, phone2, email, facebook, twitter, instagram, map_url) VALUES 
(1, '123 Luxury Avenue, Downtown, City - 400001', '917778889991', '918888999922', 'info@citystay.com', '#', '#', '#', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3719.9886443657786!2d72.84024807490332!3d21.19266858055627!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39540b90d238b771%3A0x6b8d42d32e92c24c!2sSurat%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin');

-- Sample Testimonials
INSERT INTO testimonials (name, email, message, rating) VALUES 
('John Doe', 'john@example.com', 'Amazing hotel! Great service and comfortable rooms. Highly recommend!', 5),
('Jane Smith', 'jane@example.com', 'Wonderful experience. The staff was very friendly and helpful.', 4),
('Mike Johnson', 'mike@example.com', 'Beautiful property with excellent amenities. Will visit again.', 5);