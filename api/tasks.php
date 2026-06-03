<?php
require_once __DIR__ . '/../config/database.php';

requireIfNotLoggedIn();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? 'create';

try {
    if ($action === 'delete' && $_POST) {
        $id = $_POST['id'] ?? null;
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Görev silindi']);
    } elseif ($action === 'update' && $_POST) {
        $task_id = $_POST['task_id'] ?? null;
        $title = $_POST['title'] ?? null;
        $status = $_POST['status'] ?? 'pending';
        $priority = $_POST['priority'] ?? 'medium';
        $progress = $_POST['progress'] ?? 0;
        $assigned_to = $_POST['assigned_to'] ?? null;
        
        $stmt = $pdo->prepare(
            'UPDATE tasks SET title = ?, status = ?, priority = ?, progress = ?, assigned_to = ? WHERE id = ?'
        );
        $stmt->execute([
            $title,
            $status,
            $priority,
            $progress,
            $assigned_to,
            $task_id
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Görev güncellendi']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? null;
        $description = $_POST['description'] ?? '';
        $project_id = $_POST['project_id'] ?? null;
        $priority = $_POST['priority'] ?? 'medium';
        $assigned_to = $_POST['assigned_to'] ?? null;
        
        $stmt = $pdo->prepare(
            'INSERT INTO tasks (project_id, title, description, priority, assigned_to, created_by, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $project_id,
            $title,
            $description,
            $priority,
            $assigned_to,
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