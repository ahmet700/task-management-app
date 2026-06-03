<?php
$pageTitle = 'Projeler';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();

// Projeleri getir
$stmt = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC');
$projects = $stmt->fetchAll();
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">📁 Projeler</h2>
    <?php if (hasPermission('manager')): ?>
        <button onclick="openModal('projectModal')" class="btn-primary">+ Yeni Proje</button>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($projects as $project): ?>
        <div class="card">
            <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($project['project_name']); ?></h3>
            <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?></p>
            
            <div class="mb-4">
                <span class="badge-medium status-<?php echo $project['status']; ?>">
                    <?php echo ucfirst($project['status']); ?>
                </span>
            </div>
            
            <div class="text-sm text-gray-500 mb-4">
                <p>Başlangıç: <?php echo $project['start_date'] ?? 'N/A'; ?></p>
                <p>Bitiş: <?php echo $project['end_date'] ?? 'N/A'; ?></p>
            </div>
            
            <div class="flex gap-2">
                <a href="/pages/tasks.php?project=<?php echo $project['id']; ?>" class="btn-primary flex-1">Görevler</a>
                <button class="btn-secondary" onclick="confirmDelete(<?php echo $project['id']; ?>, 'project')">Sil</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Proje Modal -->
<div id="projectModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('projectModal')">&times;</span>
        <div class="modal-header">Yeni Proje Oluştur</div>
        
        <form action="/api/projects.php" method="POST">
            <div class="form-group">
                <label>Proje Adı</label>
                <input type="text" name="project_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label>Başlangıç Tarihi</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Bitiş Tarihi</label>
                <input type="date" name="end_date" class="form-control">
            </div>
            
            <button type="submit" class="btn-primary w-full">Proje Oluştur</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>