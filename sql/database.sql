-- Parliamentary Training Institute (PTI) Database Schema
-- Parliament of Ghana Colors: Red #CE1126, Gold #FCD116, Green #006B3F, Dark #0F1A2E

CREATE DATABASE IF NOT EXISTS pti_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pti_db;

-- Users table (Access Control)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','editor','viewer') NOT NULL DEFAULT 'editor',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (fullname, email, username, password, role) VALUES
('Super Admin', 'admin@pti.gov.gh', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Default password: password (change immediately)

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Pages/CMS content
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content LONGTEXT,
    meta_description VARCHAR(300),
    featured_image VARCHAR(255),
    template ENUM('default','fullwidth','sidebar') NOT NULL DEFAULT 'default',
    status ENUM('published','draft') NOT NULL DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO pages (title, slug, content, meta_description, status, created_by) VALUES
('Home', 'home', '<h2>Welcome to the Parliamentary Training Institute</h2><p>Excellence in Parliamentary Capacity Development</p>', NULL, 'published', 1),
('About Us', 'about', '<h2>About the Institute</h2><p>The Parliamentary Training Institute (PTI) is the premier institution for parliamentary capacity building in Ghana.</p>', 'Learn about the Parliamentary Training Institute', 'published', 1),
('Programs', 'programs', '<h2>Our Programs</h2><p>We offer a range of training programs for Members of Parliament and parliamentary staff.</p>', NULL, 'published', 1),
('Gallery', 'gallery', NULL, 'Moments from our training programs and events', 'published', 1),
('Contact', 'contact', '<h2>Contact Us</h2><p>Get in touch with the Parliamentary Training Institute.</p>', NULL, 'published', 1);

-- Menu items
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    link VARCHAR(255) NOT NULL DEFAULT '#',
    parent_id INT DEFAULT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    target ENUM('_self','_blank') NOT NULL DEFAULT '_self',
    sort_order INT NOT NULL DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO menu_items (label, link, parent_id, sort_order, status) VALUES
('Home', 'index.php', NULL, 1, 'active'),
('About Us', 'about.php', NULL, 2, 'active'),
('Programs', 'programs.php', NULL, 3, 'active'),
('Gallery', 'gallery.php', NULL, 4, 'active'),
('Contact', 'contact.php', NULL, 5, 'active');

-- Banners/Slides
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(300),
    image_url VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    btn_text VARCHAR(50) DEFAULT 'Learn More',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO banners (title, subtitle, image_url, btn_text, sort_order) VALUES
('Parliamentary Training Institute', 'Building Capacity for Democratic Governance', 'assets/images/banner1.jpg', 'Explore', 1),
('Excellence in Legislative Education', 'Empowering Parliamentarians and Staff', 'assets/images/banner2.jpg', 'Learn More', 2);

-- Programs/Courses
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    content LONGTEXT,
    image VARCHAR(255),
    duration VARCHAR(100),
    fee DECIMAL(10,2) DEFAULT 0.00,
    category VARCHAR(100),
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    featured TINYINT(1) DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO programs (title, slug, description, duration, category, featured) VALUES
('Legislative Drafting', 'legislative-drafting', 'Comprehensive training in legislative drafting techniques and procedures.', '2 Weeks', 'Core', 1),
('Parliamentary Procedure', 'parliamentary-procedure', 'Understanding the rules and practices of parliamentary proceedings.', '1 Week', 'Core', 1),
('Committee Operations', 'committee-operations', 'Effective management and operation of parliamentary committees.', '1 Week', 'Core', 1),
('Leadership & Ethics', 'leadership-ethics', 'Developing leadership skills and ethical governance principles.', '3 Days', 'Professional Development', 0);

-- Activity Logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Website settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Parliamentary Training Institute'),
('site_tagline', 'Building Capacity for Democratic Governance'),
('site_email', 'info@pti.gov.gh'),
('site_phone', '+233 302 123 456'),
('site_address', 'Parliament House, Accra, Ghana'),
('footer_text', '&copy; 2026 Parliamentary Training Institute. All Rights Reserved.'),
('site_favicon', 'assets/logos/logo.png'),
('social_facebook', '#'),
('social_twitter', '#'),
('social_instagram', '#'),
('social_linkedin', '#'),
('maintenance_mode', '0'),
('home_stats', '15+|Years of Excellence
500+|Parliamentarians Trained
40+|Expert Facilitators
8|Core Programs'),
('about_leadership', 'Dr. Emmanuel Asante|Director|fa-user-tie
Mrs. Akua Mensah|Deputy Director|fa-user-tie
Mr. Kwame Osei|Head of Programs|fa-chalkboard-teacher'),
('about_mission', 'To enhance parliamentary effectiveness through innovative training, research, and capacity building programs.'),
('about_vision', 'To be a world-class parliamentary training institution that sets the standard for legislative capacity development in Africa.'),
('about_values', 'Excellence, Integrity, Inclusiveness, Innovation, and Professionalism in all our undertakings.'),
('why_choose_pti', 'fa-certificate|Accredited Programs|All programs are accredited by the relevant professional bodies.
fa-chalkboard-teacher|Expert Faculty|Learn from experienced parliamentarians and academics.
fa-hands-helping|Practical Approach|Hands-on training with real-world parliamentary scenarios.
fa-globe-africa|Networking|Connect with peers across the legislative sector.'),
('working_hours', 'Mon-Fri: 8:00 AM - 5:00 PM');

-- Page analytics/visits
CREATE TABLE IF NOT EXISTS page_visits (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    replied TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- News/Announcements
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT,
    image VARCHAR(255),
    status ENUM('published','draft') NOT NULL DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Gallery
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- File store (BLOB backup for uploaded files)
CREATE TABLE IF NOT EXISTS file_store (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_path VARCHAR(500) NOT NULL,
    file_data MEDIUMBLOB NOT NULL,
    file_mime VARCHAR(100) NOT NULL DEFAULT 'application/octet-stream',
    file_size INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY file_path (file_path)
) ENGINE=InnoDB;
