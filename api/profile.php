<?php
require_once __DIR__ . '/../config/database.php';

requireIfNotLoggedIn();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $old_password = $_POST['old_password'] ?? null;
        $new_password = $_POST['new_password'] ?? null;
        $confirm_password = $_POST['confirm_password'] ?? null;
        
        // Kullanıcı bilgilerini getir
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        // Eski şifreyi kontrol et
        if (!password_verify($old_password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Eski şifre yanlış']);
            exit;
        }
        
        // Şifreler eşleşiyor mu?
        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Yeni şifreler eşleşmiyor']);
            exit;
        }
        
        // Şifreyi güncelle
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
        
        echo json_encode(['success' => true, 'message' => 'Şifre başarıyla değiştirildi']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>