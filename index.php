<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    header('Location: /pages/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND status = 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            header('Location: /pages/dashboard.php');
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - Görev Yönetim Sistemi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #3b82f6 0%, #f97316 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <h1 class="text-4xl font-bold text-center mb-2 text-gray-800">📋 Görev Yönetim</h1>
            <p class="text-center text-gray-600 mb-8">Sistem Giriş</p>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/index.php">
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Kullanıcı Adı</label>
                    <input type="text" name="username" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Şifre</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-orange-500 text-white font-bold py-3 rounded-lg hover:shadow-lg transition">
                    Giriş Yap
                </button>
            </form>
            
            <hr class="my-6">
            
            <p class="text-center text-gray-600 text-sm">
                Demo Kullanıcılar:
                <br>user / password
                <br>admin / password
            </p>
        </div>
    </div>
</body>
</html>