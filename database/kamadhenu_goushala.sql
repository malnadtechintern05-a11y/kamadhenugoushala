-- ============================================================
-- Kamadhenu Goushala — Complete Database Schema
-- MySQL/MariaDB | UTF-8MB4 | InnoDB
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `kamadhenu_goushala`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `kamadhenu_goushala`;

-- ─────────────────────────────────────────────────────────────
-- 1. admins
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(60)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
  `email`      VARCHAR(120) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: username=admin, password=Admin@1234
INSERT IGNORE INTO `admins` (`username`, `password`, `email`) VALUES
('admin', '$2y$12$6H0lXBixfFyJJTQ1hS2OJeLi0r78WyI9jIAhkNOh9y5R7sIhNi3vy', 'admin@kamadhenugoushala.org');
-- NOTE: password hash above is for "Admin@1234" — change immediately after first login.

-- ─────────────────────────────────────────────────────────────
-- 2. cows
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `cows` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `breed`       VARCHAR(100) NOT NULL DEFAULT '',
  `age`         TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `gender`      ENUM('Female','Male','Calf') NOT NULL DEFAULT 'Female',
  `color`       VARCHAR(80) NOT NULL DEFAULT '',
  `weight_kg`   DECIMAL(6,2) UNSIGNED DEFAULT NULL,
  `health_status` ENUM('Healthy','Under Treatment','Recovered') NOT NULL DEFAULT 'Healthy',
  `adoption_status` ENUM('Available','Adopted','Not Available') NOT NULL DEFAULT 'Available',
  `description` TEXT DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_cows_adoption_status` (`adoption_status`),
  INDEX `idx_cows_is_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 3. donations
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `donations` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `donor_name`     VARCHAR(120) NOT NULL,
  `donor_email`    VARCHAR(120) NOT NULL,
  `donor_phone`    VARCHAR(20)  NOT NULL DEFAULT '',
  `amount`         DECIMAL(10,2) UNSIGNED NOT NULL,
  `purpose`        ENUM('General','Cow Feed','Medical','Infrastructure','Gau Seva','Other') NOT NULL DEFAULT 'General',
  `message`        TEXT DEFAULT NULL,
  `payment_method` ENUM('UPI','Bank Transfer','Cash','Online','Other') NOT NULL DEFAULT 'UPI',
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `status`         ENUM('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending',
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_donations_status` (`status`),
  INDEX `idx_donations_donor_email` (`donor_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 4. adoptions
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `adoptions` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cow_id`         INT UNSIGNED NOT NULL,
  `adopter_name`   VARCHAR(120) NOT NULL,
  `adopter_email`  VARCHAR(120) NOT NULL,
  `adopter_phone`  VARCHAR(20)  NOT NULL DEFAULT '',
  `adopter_address` TEXT DEFAULT NULL,
  `duration_months` TINYINT UNSIGNED NOT NULL DEFAULT 12,
  `amount_per_month` DECIMAL(8,2) UNSIGNED NOT NULL DEFAULT 1500.00,
  `message`        TEXT DEFAULT NULL,
  `status`         ENUM('Pending','Active','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_adoptions_cow_id` (`cow_id`),
  INDEX `idx_adoptions_status` (`status`),
  CONSTRAINT `fk_adoptions_cow`
    FOREIGN KEY (`cow_id`) REFERENCES `cows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 5. products
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `category`    ENUM('Milk Products','Ghee','Gomutra','Panchamrit','Panchagavya','Organic','Other') NOT NULL DEFAULT 'Other',
  `price`       DECIMAL(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `unit`        VARCHAR(40)  NOT NULL DEFAULT '1 kg',
  `stock_qty`   INT UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_products_category` (`category`),
  INDEX `idx_products_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 6. orders
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_name`    VARCHAR(120) NOT NULL,
  `customer_email`   VARCHAR(120) NOT NULL,
  `customer_phone`   VARCHAR(20)  NOT NULL DEFAULT '',
  `customer_address` TEXT NOT NULL,
  `total_amount`     DECIMAL(10,2) UNSIGNED NOT NULL,
  `payment_method`   ENUM('UPI','Bank Transfer','Cash on Delivery','Online','Other') NOT NULL DEFAULT 'UPI',
  `notes`            TEXT DEFAULT NULL,
  `status`           ENUM('Pending','Confirmed','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_orders_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 6a. order_items
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`   INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity`   INT UNSIGNED NOT NULL DEFAULT 1,
  `price`      DECIMAL(10,2) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_order_items_order_id` (`order_id`),
  CONSTRAINT `fk_order_items_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 7. gallery
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `gallery` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(150) NOT NULL DEFAULT '',
  `description` TEXT DEFAULT NULL,
  `image`       VARCHAR(255) NOT NULL,
  `category`    VARCHAR(80)  NOT NULL DEFAULT 'General',
  `sort_order`  INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_gallery_category` (`category`),
  INDEX `idx_gallery_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 8. messages  (contact form)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(120) NOT NULL,
  `phone`      VARCHAR(20)  NOT NULL DEFAULT '',
  `subject`    VARCHAR(200) NOT NULL DEFAULT '',
  `message`    TEXT NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_messages_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- 9. volunteers
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `volunteers` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(120) NOT NULL,
  `email`        VARCHAR(120) NOT NULL,
  `phone`        VARCHAR(20)  NOT NULL DEFAULT '',
  `age`          TINYINT UNSIGNED DEFAULT NULL,
  `occupation`   VARCHAR(100) NOT NULL DEFAULT '',
  `availability` ENUM('Weekdays','Weekends','Full Time','Part Time','Flexible') NOT NULL DEFAULT 'Flexible',
  `skills`       TEXT DEFAULT NULL,
  `motivation`   TEXT DEFAULT NULL,
  `status`       ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_volunteers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Sample Data
-- ─────────────────────────────────────────────────────────────

-- Sample Cows
INSERT INTO `cows` (`name`,`breed`,`age`,`gender`,`color`,`health_status`,`adoption_status`,`description`,`is_featured`) VALUES
('Ganga','Gir',5,'Female','Golden Brown','Healthy','Available','A beautiful Gir cow known for her calm nature and high milk yield. She loves humans and responds to her name.',1),
('Lakshmi','Sahiwal',4,'Female','Reddish Brown','Healthy','Available','Lakshmi is a gentle and healthy Sahiwal cow. She produces rich milk and is a favourite of all our volunteers.',1),
('Kamadhenu','Tharparkar',6,'Female','White','Healthy','Adopted','Our founding cow and namesake of the Goushala. She represents the divine spirit of Gau Mata.',1),
('Nandini','Hariana',3,'Female','White','Healthy','Available','Young and energetic, Nandini is full of life. She has excellent health and a wonderful temperament.',0),
('Surabhi','Red Sindhi',7,'Female','Dark Red','Under Treatment','Not Available','Surabhi is recovering from a leg injury. Our vets are giving her the best care.',0),
('Krishna','Vechur',2,'Male','Black & White','Healthy','Available','A healthy young bull calf with vibrant markings. He is playful and full of energy.',0);

-- Sample Products
INSERT INTO `products` (`name`,`category`,`price`,`unit`,`stock_qty`,`description`,`is_featured`,`is_active`) VALUES
('Pure A2 Cow Ghee','Ghee',850.00,'500 ml',50,'Traditional hand-churned A2 ghee made from our Gir cows'' milk. Rich in nutrition, golden in colour, and prepared using the ancient Bilona method.',1,1),
('Fresh A2 Milk','Milk Products',80.00,'1 litre',100,'Pure, raw A2 milk from our healthy Gir and Sahiwal cows. No additives, no preservatives. Delivered fresh every morning.',1,1),
('Gomutra Ark','Gomutra',120.00,'500 ml',30,'Purified and distilled Gau-mutra (cow urine) prepared using Ayurvedic methods. Known for its medicinal and purifying properties.',1,1),
('Panchagavya Mix','Panchagavya',350.00,'250 ml',20,'Sacred blend of milk, curd, ghee, gomutra, and cow dung. Used in Ayurvedic rituals and organic farming.',0,1),
('Organic Cow Dung Cake','Organic',30.00,'Pack of 10',200,'Dried cow dung cakes for havan/yagna and mosquito repellent. Naturally processed and eco-friendly.',0,1),
('A2 Curd (Dahi)','Milk Products',60.00,'500 gm',60,'Thick, creamy A2 curd set fresh every morning from our cow milk. No artificial cultures.',0,1);

-- Sample Gallery
INSERT INTO `gallery` (`title`,`category`,`image`,`sort_order`) VALUES
('Morning Seva','Seva','',1),
('Cow Feeding','Cows','',2),
('Goushala Premises','Premises','',3),
('Annual Gau Puja','Events','',4),
('Volunteer Day','Volunteers','',5),
('Calf Care','Cows','',6);

-- ─────────────────────────────────────────────────────────────
-- 10. events
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `events` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `event_date`  DATETIME NOT NULL,
  `location`    VARCHAR(255) NOT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_events_date` (`event_date`),
  INDEX `idx_events_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ─────────────────────────────────────────────────────────────
-- END OF SCHEMA
-- Import with: mysql -u root -p < kamadhenu_goushala.sql
-- ─────────────────────────────────────────────────────────────
