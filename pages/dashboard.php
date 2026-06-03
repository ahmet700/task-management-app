<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();

// İstatistikler
$stats = [];

// Toplam proje sayısı
$stmt = $pdo->query('SELECT COUNT(*) as count FROM projects');
$stats['projects'] = $stmt->fetch()['count'];

// Devam eden görevler
$stmt = $pdo->query('SELECT COUNT(*) as count FROM tasks WHERE status = "in_progress"');
$stats['in_progress'] = $stmt->fetch()['count'];

// Tamamlanan görevler
$stmt = $pdo->query('SELECT COUNT(*) as count FROM tasks WHERE status = "completed"');
$stats['completed'] = $stmt->fetch()['count'];

// Beklemede olan görevler
$stmt = $pdo->query('SELECT COUNT(*) as count FROM tasks WHERE status = "pending"');
$stats['pending'] = $stmt->fetch()['count'];

// Son görevler
$stmt = $pdo->query('SELECT t.*, p.project_name, u.name as assigned_name FROM tasks t LEFT JOIN projects p ON t.project_id = p.id LEFT JOIN users u ON t.assigned_to = u.id ORDER BY t.created_at DESC LIMIT 5');
$recent_tasks = $stmt->fetchAll();
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Proje Kartı -->
    <div class="stats-card">
        <div class="stats-number"><?php echo $stats['projects']; ?></div>
        <div class="stats-label">Toplam Proje</div>
    </div>
    
    <!-- Beklemede Kartı -->
    <div class="stats-card">
        <div class="stats-number"><?php echo $stats['pending']; ?></div>
        <div class="stats-label">Beklemede</div>
    </div>
    
    <!-- Devam Eden Kartı -->
    <div class="stats-card">
        <div class="stats-number"><?php echo $stats['in_progress']; ?></div>
        <div class="stats-label">Devam Eden</div>
    </div>
    
    <!-- Tamamlanan Kartı -->
    <div class="stats-card">
        <div class="stats-number"><?php echo $stats['completed']; ?></div>
        <div class="stats-label">Tamamlanan</div>
    </div>
</div>

<!-- Son Görevler -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">📌 Son Görevler</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Görev</th>
                <th>Proje</th>
                <th>Atanan Kişi</th>
                <th>Durum</th>
                <th>Öncelik</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_tasks as $task): ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                    <td><?php echo htmlspecialchars($task['project_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($task['assigned_name'] ?? 'Atanmadı'); ?></td>
                    <td>
                        <span class="badge-medium status-<?php echo $task['status']; ?>">
                            <?php echo ucfirst($task['status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge-<?php echo strtolower($task['priority']); ?>">
                            <?php echo ucfirst($task['priority']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>