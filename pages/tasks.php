<?php
$pageTitle = 'Görevler';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();

$project_id = $_GET['project'] ?? null;

// Görevleri getir
if ($project_id) {
    $stmt = $pdo->prepare('SELECT t.*, p.project_name, u.name as assigned_name FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id WHERE t.project_id = ? ORDER BY t.created_at DESC');
    $stmt->execute([$project_id]);
} else {
    $stmt = $pdo->query('SELECT t.*, p.project_name, u.name as assigned_name FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id ORDER BY t.created_at DESC');
}
$tasks = $stmt->fetchAll();

// Projeler listesi
$stmt = $pdo->query('SELECT id, project_name FROM projects');
$projects = $stmt->fetchAll();
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">✓ Görevler</h2>
    <button onclick="openModal('taskModal')" class="btn-primary">+ Yeni Görev</button>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Proje</th>
                <th>Durum</th>
                <th>Öncelik</th>
                <th>Atanan</th>
                <th>İlerleme</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                    <td><?php echo htmlspecialchars($task['project_name'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="badge-medium status-<?php echo $task['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge-<?php echo strtolower($task['priority']); ?>">
                            <?php echo ucfirst($task['priority']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($task['assigned_name'] ?? 'Atanmadı'); ?></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $task['progress']; ?>%"></div>
                        </div>
                        <small><?php echo $task['progress']; ?>%</small>
                    </td>
                    <td>
                        <button class="btn-secondary text-sm" onclick="openModal('editTaskModal')" data-task-id="<?php echo $task['id']; ?>">Düzenle</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Görev Modal -->
<div id="taskModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('taskModal')">&times;</span>
        <div class="modal-header">Yeni Görev Oluştur</div>
        
        <form action="/api/tasks.php" method="POST">
            <div class="form-group">
                <label>Başlık</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label>Proje</label>
                <select name="project_id" class="form-control" required>
                    <option value="">Seçiniz</option>
                    <?php foreach ($projects as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php if ($project_id == $p['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($p['project_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Öncelik</label>
                <select name="priority" class="form-control">
                    <option value="low">Düşük</option>
                    <option value="medium" selected>Orta</option>
                    <option value="high">Yüksek</option>
                    <option value="critical">Kritik</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary w-full">Görev Oluştur</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>