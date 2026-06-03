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

// Kullanıcılar listesi (görev atama için)
$stmt = $pdo->query('SELECT id, name FROM users WHERE status = 1');
$users = $stmt->fetchAll();
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
                        <button class="btn-secondary text-sm" onclick="editTask(<?php echo $task['id']; ?>, '<?php echo htmlspecialchars($task['title'], ENT_QUOTES); ?>', <?php echo $task['project_id']; ?>, '<?php echo $task['status']; ?>', '<?php echo $task['priority']; ?>', <?php echo $task['assigned_to'] ?? 'null'; ?>, <?php echo $task['progress']; ?>)">Düzenle</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Yeni Görev Modal -->
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
            
            <div class="form-group">
                <label>Atanan Kişi</label>
                <select name="assigned_to" class="form-control">
                    <option value="">Atanmadı</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-primary w-full">Görev Oluştur</button>
        </form>
    </div>
</div>

<!-- Görev Düzenle Modal -->
<div id="editTaskModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('editTaskModal')">&times;</span>
        <div class="modal-header">Görev Düzenle</div>
        
        <form action="/api/tasks.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="task_id" id="editTaskId">
            
            <div class="form-group">
                <label>Başlık</label>
                <input type="text" name="title" id="editTaskTitle" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Durum</label>
                <select name="status" id="editTaskStatus" class="form-control">
                    <option value="pending">Beklemede</option>
                    <option value="in_progress">Devam Ediyor</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="cancelled">İptal Edildi</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Öncelik</label>
                <select name="priority" id="editTaskPriority" class="form-control">
                    <option value="low">Düşük</option>
                    <option value="medium">Orta</option>
                    <option value="high">Yüksek</option>
                    <option value="critical">Kritik</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>İlerleme (%)</label>
                <input type="number" name="progress" id="editTaskProgress" class="form-control" min="0" max="100" value="0">
            </div>
            
            <div class="form-group">
                <label>Atanan Kişi</label>
                <select name="assigned_to" id="editTaskAssigned" class="form-control">
                    <option value="">Atanmadı</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Kaydet</button>
                <button type="button" class="btn-secondary flex-1" onclick="closeModal('editTaskModal')">İptal</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTask(taskId, title, projectId, status, priority, assignedTo, progress) {
    document.getElementById('editTaskId').value = taskId;
    document.getElementById('editTaskTitle').value = title;
    document.getElementById('editTaskStatus').value = status;
    document.getElementById('editTaskPriority').value = priority;
    document.getElementById('editTaskProgress').value = progress;
    document.getElementById('editTaskAssigned').value = assignedTo || '';
    
    openModal('editTaskModal');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>