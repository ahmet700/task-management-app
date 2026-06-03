CREATE DATABASE gorev_yonetim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gorev_yonetim;

-- Kullanıcılar
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','manager','user') DEFAULT 'user',
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projeler
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(200) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('planning','active','completed','cancelled') DEFAULT 'planning',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Proje Üyeleri
CREATE TABLE project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    project_role ENUM('manager','member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Görevler
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    parent_task_id INT NULL,

    title VARCHAR(255) NOT NULL,
    description TEXT,

    priority ENUM('low','medium','high','critical') DEFAULT 'medium',
    status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',

    assigned_to INT NULL,
    created_by INT NOT NULL,

    progress TINYINT DEFAULT 0,

    start_date DATETIME NULL,
    due_date DATETIME NULL,
    completed_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_task_id) REFERENCES tasks(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Görev Yorumları
CREATE TABLE task_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Dosyalar
CREATE TABLE task_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Etiketler
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(50) NOT NULL,
    color VARCHAR(20) DEFAULT '#3498db'
);

-- Görev Etiketleri
CREATE TABLE task_tags (
    task_id INT NOT NULL,
    tag_id INT NOT NULL,

    PRIMARY KEY (task_id, tag_id),

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Görev Geçmişi
CREATE TABLE task_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,

    action VARCHAR(100) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================================
-- DEMO KULLANICIları EKLE
-- ========================================
-- Şifre: password (bcrypt hash)

INSERT INTO users (name, username, email, password, role, status) VALUES 
(
    'Admin Kullanıcı',
    'admin',
    'admin@gorevyonetim.local',
    '$2y$10$YIj7P0h4.W8Gy.jiYYdPOOmxW0cvFB.Ko0qMKH5cKLmKDtkJoZ5G6', -- password
    'admin',
    1
),
(
    'Ahmet Yılmaz',
    'user',
    'user@gorevyonetim.local',
    '$2y$10$YIj7P0h4.W8Gy.jiYYdPOOmxW0cvFB.Ko0qMKH5cKLmKDtkJoZ5G6', -- password
    'user',
    1
),
(
    'Proje Yöneticisi',
    'manager',
    'manager@gorevyonetim.local',
    '$2y$10$YIj7P0h4.W8Gy.jiYYdPOOmxW0cvFB.Ko0qMKH5cKLmKDtkJoZ5G6', -- password
    'manager',
    1
);

-- ========================================
-- DEMO PROJESİ EKLE
-- ========================================

INSERT INTO projects (project_name, description, start_date, end_date, status, created_by) VALUES 
(
    'Web Sitesi Geliştirme',
    'Modern responsive web sitesi geliştirme projesi',
    '2026-01-01',
    '2026-06-30',
    'active',
    1
),
(
    'Mobil Uygulama',
    'iOS ve Android mobil uygulaması geliştirme',
    '2026-02-01',
    '2026-08-31',
    'active',
    3
),
(
    'Veritabanı Optimizasyonu',
    'Mevcut veritabanı yapısının optimizasyonu',
    '2026-03-15',
    '2026-04-30',
    'planning',
    1
);

-- ========================================
-- DEMO GÖREVLER EKLE
-- ========================================

INSERT INTO tasks (project_id, title, description, priority, status, assigned_to, created_by, progress, start_date, due_date) VALUES 
(
    1,
    'Frontend Tasarımı',
    'Web sitesinin frontend tasarımı ve prototip oluşturma',
    'high',
    'in_progress',
    2,
    1,
    60,
    '2026-01-10',
    '2026-02-15'
),
(
    1,
    'Backend API Geliştirme',
    'RESTful API geliştirme ve veri tabanı entegrasyonu',
    'high',
    'pending',
    3,
    1,
    0,
    '2026-02-01',
    '2026-03-31'
),
(
    2,
    'iOS Uygulaması Geliştirme',
    'Swift ile iOS uygulaması geliştirme',
    'critical',
    'pending',
    2,
    3,
    10,
    '2026-02-15',
    '2026-07-31'
),
(
    2,
    'Android Uygulaması Geliştirme',
    'Kotlin ile Android uygulaması geliştirme',
    'critical',
    'pending',
    3,
    3,
    5,
    '2026-02-20',
    '2026-07-31'
),
(
    3,
    'Sorgu Optimizasyonu',
    'Yavaş çalışan SQL sorgularının optimize edilmesi',
    'medium',
    'pending',
    1,
    1,
    0,
    '2026-03-20',
    '2026-04-15'
),
(
    1,
    'Testes ve QA',
    'Uygulamanın test edilmesi ve kalite kontrol',
    'high',
    'pending',
    2,
    1,
    0,
    '2026-04-01',
    '2026-05-31'
);

-- ========================================
-- DEMO ETİKETLER EKLE
-- ========================================

INSERT INTO tags (tag_name, color) VALUES 
('Bug', '#ef4444'),
('Feature', '#3b82f6'),
('Documentation', '#8b5cf6'),
('Enhancement', '#10b981'),
('Performance', '#f59e0b');

-- ========================================
-- DEMO PROJE ÜYELERİ EKLE
-- ========================================

INSERT INTO project_members (project_id, user_id, project_role) VALUES 
(1, 1, 'manager'),
(1, 2, 'member'),
(1, 3, 'member'),
(2, 3, 'manager'),
(2, 2, 'member'),
(3, 1, 'manager');
