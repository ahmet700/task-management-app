<?php
// Veritabanı Konfigürasyonu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gorev_yonetim');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Veritabanı bağlantısı başarısız: ' . $e->getMessage());
}

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function hasPermission($requiredRole) {
    $userRole = getUserRole();
    $roles = ['user' => 1, 'manager' => 2, 'admin' => 3];
    return isset($roles[$userRole]) && $roles[$userRole] >= $roles[$requiredRole];
}
?>