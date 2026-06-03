<?php
$pageTitle = 'Ayarlar';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();

if (!hasPermission('admin')) {
    echo '<div class="bg-red-100 text-red-800 p-4 rounded">Bu sayfaya erişim izniniz yok!</div>';
    exit;
}

// Kullanıcı listesi
$stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">⚙️ Sistem Ayarları</h2>

<!-- Kullanıcı Yönetimi -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-2xl font-bold text-gray-800">👥 Kullanıcı Yönetimi</h3>
        <button onclick="openModal('userModal')" class="btn-primary">+ Yeni Kullanıcı</button>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Ad Soyad</th>
                <th>Kullanıcı Adı</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge-medium"><?php echo ucfirst($user['role']); ?></span>
                    </td>
                    <td>
                        <span class="badge-medium"><?php echo $user['status'] ? 'Aktif' : 'Pasif'; ?></span>
                    </td>
                    <td>
                        <button class="btn-secondary text-sm" onclick="editUser(<?php echo $user['id']; ?>)">Düzenle</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Kullanıcı Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('userModal')">&times;</span>
        <div class="modal-header">Yeni Kullanıcı Oluştur</div>
        
        <form action="/api/users.php" method="POST">
            <div class="form-group">
                <label>Ad Soyad</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Rol</label>
                <select name="role" class="form-control">
                    <option value="user">Kullanıcı</option>
                    <option value="manager">Yönetici</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary w-full">Kullanıcı Oluştur</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>