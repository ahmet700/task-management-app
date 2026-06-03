<?php
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Görev Yönetim Sistemi'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-blue: #3b82f6;
            --primary-orange: #f97316;
            --primary-dark-blue: #1e40af;
            --primary-dark-orange: #c2410c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .btn-primary {
            @apply bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition;
        }
        
        .btn-orange {
            @apply bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition;
        }
        
        .btn-secondary {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition;
        }
        
        .card {
            @apply bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition;
        }
        
        .sidebar {
            @apply bg-gradient-to-b from-blue-900 to-blue-800 text-white min-h-screen w-64 fixed left-0 top-0;
        }
        
        .main-content {
            @apply ml-64 p-8;
        }
        
        .nav-link {
            @apply block px-4 py-2 rounded-lg hover:bg-orange-500 transition duration-200;
        }
        
        .nav-link.active {
            @apply bg-orange-500;
        }
    </style>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-100">
<?php if (isLoggedIn()): ?>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="p-6 border-b border-blue-700">
            <h1 class="text-2xl font-bold">📋 Görev Yönetim</h1>
            <p class="text-blue-200 text-sm mt-2"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
        </div>
        
        <nav class="mt-8">
            <a href="/pages/dashboard.php" class="nav-link">📊 Dashboard</a>
            <a href="/pages/projects.php" class="nav-link">📁 Projeler</a>
            <a href="/pages/tasks.php" class="nav-link">✓ Görevler</a>
            <a href="/pages/profile.php" class="nav-link">👤 Profilim</a>
            <?php if (hasPermission('admin')): ?>
                <a href="/pages/settings.php" class="nav-link">⚙️ Ayarlar</a>
            <?php endif; ?>
            <a href="/api/logout.php" class="nav-link bg-orange-500 mt-4">🚪 Çıkış Yap</a>
        </nav>
    </div>
    
    <div class="main-content">
<?php endif; ?>