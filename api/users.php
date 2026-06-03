<?php
require_once __DIR__ . '/../config/database.php';

requireIfNotLoggedIn();

if (!hasPermission('admin')) {
    http_response_code(403);
    exit('Yetkisiz erişim');
}

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? null;
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $role = $_POST['role'] ?? 'user';
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $name,
            $username,
            $email,
            $hashedPassword,
            $role
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Kullanıcı oluşturuldu']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>