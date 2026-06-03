<?php
require_once __DIR__ . '/../config/database.php';

requireIfNotLoggedIn();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? null;

try {
    if ($action === 'delete' && $_POST) {
        $id = $_POST['id'] ?? null;
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Görev silindi']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? null;
        $description = $_POST['description'] ?? '';
        $project_id = $_POST['project_id'] ?? null;
        $priority = $_POST['priority'] ?? 'medium';
        
        $stmt = $pdo->prepare(
            'INSERT INTO tasks (project_id, title, description, priority, assigned_to, created_by, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $project_id,
            $title,
            $description,
            $priority,
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            'pending'
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Görev oluşturuldu']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>