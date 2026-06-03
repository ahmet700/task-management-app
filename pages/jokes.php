<?php
$pageTitle = 'Şaka Üreteci';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-2">😂 Rastgele Şaka Üreteci</h2>
        <p class="text-gray-600">Her seferinde yeni bir şaka ile güülsün! (JokeAPI'den çekilen)</p>
    </div>

    <!-- Kategori Seçimi -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">🎯 Şaka Kategorisi Seç</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button onclick="getJoke('general')" class="btn-primary">
                👨‍👩‍👧‍👦 Genel
            </button>
            <button onclick="getJoke('programming')" class="btn-primary">
                💻 Programlama
            </button>
            <button onclick="getJoke('knock-knock')" class="btn-primary">
                🚪 Kapı Çalma
            </button>
            <button onclick="getJoke('spooky')" class="btn-primary">
                👻 Korku
            </button>
        </div>
    </div>

    <!-- Şaka Gösterme Alanı -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div id="jokeContainer" class="text-center min-h-32 flex flex-col justify-center">
            <p class="text-gray-500 text-lg">Bir kategori seçin veya aşağıdaki düğmeyi kullanın</p>
        </div>
    </div>

    <!-- Kontrol Butonları -->
    <div class="flex gap-4 justify-center">
        <button onclick="getRandomJoke()" class="btn-orange flex items-center gap-2">
            🎲 Rastgele Şaka Getir
        </button>
        <button onclick="shareJoke()" class="btn-secondary flex items-center gap-2">
            📤 Paylaş
        </button>
    </div>

    <!-- Şaka Geçmişi -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">📚 Şaka Geçmişi</h3>
        <div id="jokeHistory" class="space-y-3">
            <p class="text-gray-500 text-center">Henüz şaka yüklenmedi</p>
        </div>
    </div>
</div>

<style>
    #jokeContainer {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .joke-item {
        background: linear-gradient(135deg, #3b82f6 0%, #f97316 100%);
        color: white;
        padding: 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .joke-item:hover {
        transform: translateX(5px);
    }
</style>

<script>
let jokeHistory = JSON.parse(localStorage.getItem('jokeHistory')) || [];
let currentJoke = null;

// Şaka geçmişini göster
function displayHistory() {
    const historyContainer = document.getElementById('jokeHistory');
    
    if (jokeHistory.length === 0) {
        historyContainer.innerHTML = '<p class="text-gray-500 text-center">Henüz şaka yüklenmedi</p>';
        return;
    }
    
    historyContainer.innerHTML = jokeHistory.map((joke, index) => `
        <div class="joke-item" onclick="showJoke('${joke.setup}', '${joke.delivery || joke.joke}')">
            <small class="text-blue-100">#${jokeHistory.length - index}</small>
            <p class="text-sm font-semibold">${joke.setup || joke.joke}</p>
        </div>
    `).join('');
}

// Rastgele şaka getir
async function getRandomJoke() {
    try {
        showLoading();
        const response = await fetch('https://v2.jokeapi.dev/joke/Any');
        const data = await response.json();
        displayJoke(data);
    } catch (error) {
        showError('Şaka getirilemedi: ' + error.message);
    }
}

// Kategori seçerek şaka getir
async function getJoke(category) {
    try {
        showLoading();
        
        // Kategori eşlemeleri
        const categoryMap = {
            'general': 'General',
            'programming': 'Programming',
            'knock-knock': 'Knock-Knock',
            'spooky': 'Spooky'
        };
        
        const categoryName = categoryMap[category] || 'Any';
        const response = await fetch(`https://v2.jokeapi.dev/joke/${categoryName}`);
        const data = await response.json();
        
        if (data.error) {
            showError('Bu kategoride şaka bulunamadı');
            return;
        }
        
        displayJoke(data);
    } catch (error) {
        showError('Şaka getirilemedi: ' + error.message);
    }
}

// Şakayı göster
function displayJoke(joke) {
    const container = document.getElementById('jokeContainer');
    currentJoke = joke;
    
    let jokeText = '';
    
    if (joke.type === 'single') {
        jokeText = `
            <h2 class="text-2xl font-bold text-gray-800 mb-4">😄</h2>
            <p class="text-xl text-gray-700 leading-relaxed">${joke.joke}</p>
        `;
    } else if (joke.type === 'twopart') {
        jokeText = `
            <h2 class="text-2xl font-bold text-gray-800 mb-4">😄</h2>
            <p class="text-lg font-semibold text-gray-700 mb-4">🎭 Kurgu:</p>
            <p class="text-lg text-gray-700 mb-6">${joke.setup}</p>
            <p class="text-lg font-semibold text-gray-700 mb-4">😂 Punch Line:</p>
            <p class="text-xl text-orange-600 font-bold">${joke.delivery}</p>
        `;
    }
    
    // Kategori ve tür bilgileri
    const info = `
        <div class="mt-6 flex gap-2 justify-center">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">📁 ${joke.category}</span>
            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm">🏷️ ${joke.type}</span>
        </div>
    `;
    
    container.innerHTML = jokeText + info;
    
    // Geçmişe ekle
    addToHistory(joke);
    
    // Bildirim göster
    showNotification('Şaka başarıyla yüklendi! 😄', 'success');
}

// Geçmişe şaka ekle
function addToHistory(joke) {
    // Aynı şaka varsa kaldır
    jokeHistory = jokeHistory.filter(j => 
        j.joke !== joke.joke && j.setup !== joke.setup
    );
    
    // Başa ekle
    jokeHistory.unshift(joke);
    
    // Maksimum 10 şaka tut
    if (jokeHistory.length > 10) {
        jokeHistory.pop();
    }
    
    // LocalStorage'a kaydet
    localStorage.setItem('jokeHistory', JSON.stringify(jokeHistory));
    
    // Gösterimi güncelle
    displayHistory();
}

// Geçmiş şakayı göster
function showJoke(setup, delivery) {
    currentJoke = {
        setup: setup,
        delivery: delivery,
        type: 'twopart',
        category: 'History'
    };
    
    const container = document.getElementById('jokeContainer');
    container.innerHTML = `
        <h2 class="text-2xl font-bold text-gray-800 mb-4">😄</h2>
        <p class="text-lg font-semibold text-gray-700 mb-4">🎭 Kurgu:</p>
        <p class="text-lg text-gray-700 mb-6">${setup}</p>
        <p class="text-lg font-semibold text-gray-700 mb-4">😂 Punch Line:</p>
        <p class="text-xl text-orange-600 font-bold">${delivery}</p>
        <div class="mt-6">
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">📚 Geçmiş</span>
        </div>
    `;
}

// Şakayı paylaş
function shareJoke() {
    if (!currentJoke) {
        showNotification('Lütfen önce bir şaka yükleyin', 'error');
        return;
    }
    
    let jokeText = '';
    
    if (currentJoke.type === 'single') {
        jokeText = currentJoke.joke;
    } else {
        jokeText = currentJoke.setup + '\n' + currentJoke.delivery;
    }
    
    const shareText = `😂 ${jokeText}\n\nGörev Yönetim Sistemi - Şaka Üreteci`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Harika Bir Şaka',
            text: shareText
        });
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(shareText).then(() => {
            showNotification('Şaka panoya kopyalandı!', 'success');
        });
    }
}

// Yükleniyor göster
function showLoading() {
    const container = document.getElementById('jokeContainer');
    container.innerHTML = `
        <div class="flex flex-col items-center">
            <div class="animate-spin h-12 w-12 border-4 border-blue-500 border-t-orange-500 rounded-full mb-4"></div>
            <p class="text-gray-600">Şaka yükleniyor...</p>
        </div>
    `;
}

// Hata göster
function showError(message) {
    const container = document.getElementById('jokeContainer');
    container.innerHTML = `
        <div class="text-center">
            <p class="text-red-600 font-semibold mb-4">⚠️ Hata!</p>
            <p class="text-gray-700">${message}</p>
            <button onclick="getRandomJoke()" class="btn-primary mt-4">
                Tekrar Dene
            </button>
        </div>
    `;
}

// Sayfa yüklendiğinde geçmişi göster
window.addEventListener('load', () => {
    displayHistory();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>