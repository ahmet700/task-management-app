<?php
$pageTitle = 'Profil';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();

$user = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$user->execute([$_SESSION['user_id']]);
$userData = $user->fetch();
?>

<div class="max-w-2xl">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">👤 Profil Bilgileri</h2>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-orange-500 rounded-full flex items-center justify-center text-white text-3xl">
                <?php echo strtoupper(substr($userData['name'], 0, 1)); ?>
            </div>
            <div class="ml-6">
                <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($userData['name']); ?></h3>
                <p class="text-gray-600">@<?php echo htmlspecialchars($userData['username']); ?></p>
                <p class="text-blue-500 font-semibold"><?php echo ucfirst($userData['role']); ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
            <div>
                <label class="font-bold">E-posta:</label>
                <p><?php echo htmlspecialchars($userData['email']); ?></p>
            </div>
            <div>
                <label class="font-bold">Durum:</label>
                <p><?php echo $userData['status'] ? 'Aktif' : 'Pasif'; ?></p>
            </div>
            <div>
                <label class="font-bold">Kayıt Tarihi:</label>
                <p><?php echo date('d.m.Y', strtotime($userData['created_at'])); ?></p>
            </div>
            <div>
                <label class="font-bold">Son Güncelleme:</label>
                <p><?php echo date('d.m.Y H:i', strtotime($userData['updated_at'])); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">🔐 Şifre Değiştir</h3>
        
        <form action="/api/profile.php" method="POST">
            <div class="form-group">
                <label>Eski Şifre</label>
                <input type="password" name="old_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Yeni Şifre</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Yeni Şifre Tekrar</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-primary">Şifre Değiştir</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>