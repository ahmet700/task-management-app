<?php
require_once __DIR__ . '/../config/database.php';

requireIfNotLoggedIn();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? null;

try {
    if ($action === 'delete' && $_POST) {
        $id = $_POST['id'] ?? null;
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Proje silindi']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_name = $_POST['project_name'] ?? null;
        $description = $_POST['description'] ?? '';
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        
        $stmt = $pdo->prepare(
            'INSERT INTO projects (project_name, description, start_date, end_date, created_by) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $project_name,
            $description,
            $start_date,
            $end_date,
            $_SESSION['user_id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Proje oluşturuldu']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>