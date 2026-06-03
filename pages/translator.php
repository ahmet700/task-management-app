<?php
$pageTitle = 'Çevirmen';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-2">🌐 Çevirmen</h2>
        <p class="text-gray-600">Metinleri farklı dillere çevirin (Google Translate API)</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sol Taraf - Kaynak Dil -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <label class="block font-bold text-gray-800 mb-2">📝 Kaynak Dil</label>
                <select id="sourceLang" class="form-control" onchange="updateLanguage()">
                    <option value="tr">🇹🇷 Türkçe</option>
                    <option value="en">🇬🇧 İngilizce</option>
                    <option value="es">🇪🇸 İspanyolca</option>
                    <option value="fr">🇫🇷 Fransızca</option>
                    <option value="de">🇩🇪 Almanca</option>
                    <option value="it">🇮🇹 İtalyanca</option>
                    <option value="pt">🇵🇹 Portekizce</option>
                    <option value="ru">🇷🇺 Rusça</option>
                    <option value="ja">🇯🇵 Japonca</option>
                    <option value="zh">🇨🇳 Çince</option>
                    <option value="ar">🇸🇦 Arapça</option>
                    <option value="ko">🇰🇷 Korece</option>
                </select>
            </div>
            
            <textarea 
                id="sourceText" 
                class="form-control h-64" 
                placeholder="Çevirmek istediğiniz metni buraya yazın..."
                onchange="updateTranslation()"
                oninput="updateTranslation()"
            ></textarea>
            
            <div class="mt-4 text-gray-600 text-sm">
                <span id="charCount">0</span> / 5000 karakter
            </div>
        </div>
        
        <!-- Sağ Taraf - Hedef Dil -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <label class="block font-bold text-gray-800 mb-2">🎯 Hedef Dil</label>
                <select id="targetLang" class="form-control" onchange="updateTranslation()">
                    <option value="en">🇬🇧 İngilizce</option>
                    <option value="tr">🇹🇷 Türkçe</option>
                    <option value="es">🇪🇸 İspanyolca</option>
                    <option value="fr">🇫🇷 Fransızca</option>
                    <option value="de">🇩🇪 Almanca</option>
                    <option value="it">🇮🇹 İtalyanca</option>
                    <option value="pt">🇵🇹 Portekizce</option>
                    <option value="ru">🇷🇺 Rusça</option>
                    <option value="ja">🇯🇵 Japonca</option>
                    <option value="zh">🇨🇳 Çince</option>
                    <option value="ar">🇸🇦 Arapça</option>
                    <option value="ko">🇰🇷 Korece</option>
                </select>
            </div>
            
            <textarea 
                id="targetText" 
                class="form-control h-64" 
                placeholder="Çevrilen metin burada gösterilecek..."
                readonly
            ></textarea>
            
            <div class="mt-4 flex gap-2">
                <button onclick="copyTranslation()" class="btn-secondary flex-1">
                    📋 Kopyala
                </button>
                <button onclick="speakTranslation()" class="btn-secondary flex-1">
                    🔊 Dinle
                </button>
            </div>
        </div>
    </div>

    <!-- Kontrol Butonları -->
    <div class="mt-6 flex gap-2 justify-center">
        <button onclick="swapLanguages()" class="btn-primary">
            🔄 Dilleri Değiştir
        </button>
        <button onclick="clearAll()" class="btn-secondary">
            🗑️ Temizle
        </button>
    </div>

    <!-- Çeviri Geçmişi -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">📚 Çeviri Geçmişi</h3>
        <div id="translationHistory" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <p class="text-gray-500 text-center col-span-full">Henüz çeviri yok</p>
        </div>
    </div>
</div>

<style>
    .translation-item {
        background: linear-gradient(135deg, #dbeafe 0%, #fed7aa 100%);
        padding: 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .translation-item:hover {
        transform: scale(1.02);
    }
</style>

<script>
let translationHistory = JSON.parse(localStorage.getItem('translationHistory')) || [];
let debounceTimer;

// Metin girdisini dinle
document.getElementById('sourceText').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('charCount').textContent = charCount;
    
    if (charCount > 5000) {
        showNotification('Maksimum 5000 karakter girilebilir', 'error');
        this.value = this.value.substring(0, 5000);
    }
    
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        updateTranslation();
    }, 500);
});

async function updateTranslation() {
    const sourceText = document.getElementById('sourceText').value.trim();
    const sourceLang = document.getElementById('sourceLang').value;
    const targetLang = document.getElementById('targetLang').value;
    const targetTextarea = document.getElementById('targetText');
    
    if (!sourceText) {
        targetTextarea.value = '';
        return;
    }
    
    if (sourceLang === targetLang) {
        targetTextarea.value = sourceText;
        return;
    }
    
    try {
        targetTextarea.value = '⏳ Çevriliyor...';
        
        // MyMemory Translation API - Ücretsiz hizmet
        const response = await fetch(
            `https://api.mymemory.translated.net/get?q=${encodeURIComponent(sourceText)}&langpair=${sourceLang}|${targetLang}`
        );
        
        const data = await response.json();
        
        if (data.responseStatus === 200) {
            targetTextarea.value = data.responseData.translatedText;
            addToHistory(sourceText, data.responseData.translatedText, sourceLang, targetLang);
        } else {
            targetTextarea.value = 'Çeviri başarısız oldu';
        }
    } catch (error) {
        targetTextarea.value = 'Hata: ' + error.message;
    }
}

function addToHistory(source, target, sourceLang, targetLang) {
    const langNames = {
        'tr': 'Türkçe',
        'en': 'İngilizce',
        'es': 'İspanyolca',
        'fr': 'Fransızca',
        'de': 'Almanca',
        'it': 'İtalyanca',
        'pt': 'Portekizce',
        'ru': 'Rusça',
        'ja': 'Japonca',
        'zh': 'Çince',
        'ar': 'Arapça',
        'ko': 'Korece'
    };
    
    translationHistory.unshift({
        source: source.substring(0, 50),
        target: target.substring(0, 50),
        sourceLang: langNames[sourceLang],
        targetLang: langNames[targetLang],
        timestamp: new Date().toLocaleString('tr-TR')
    });
    
    if (translationHistory.length > 10) {
        translationHistory.pop();
    }
    
    localStorage.setItem('translationHistory', JSON.stringify(translationHistory));
    displayHistory();
}

function displayHistory() {
    const historyContainer = document.getElementById('translationHistory');
    
    if (translationHistory.length === 0) {
        historyContainer.innerHTML = '<p class="text-gray-500 text-center col-span-full">Henüz çeviri yok</p>';
        return;
    }
    
    historyContainer.innerHTML = translationHistory.map((item, index) => `
        <div class="translation-item" onclick="loadTranslation(${index})">
            <p class="font-semibold text-sm text-gray-800">${item.sourceLang} → ${item.targetLang}</p>
            <p class="text-xs text-gray-600 mt-1">${item.source}...</p>
            <p class="text-xs text-gray-500 mt-1">⏰ ${item.timestamp}</p>
        </div>
    `).join('');
}

function loadTranslation(index) {
    const item = translationHistory[index];
    document.getElementById('sourceText').value = item.source;
    document.getElementById('targetText').value = item.target;
}

function swapLanguages() {
    const sourceLang = document.getElementById('sourceLang');
    const targetLang = document.getElementById('targetLang');
    
    const temp = sourceLang.value;
    sourceLang.value = targetLang.value;
    targetLang.value = temp;
    
    const sourceText = document.getElementById('sourceText').value;
    const targetText = document.getElementById('targetText').value;
    
    document.getElementById('sourceText').value = targetText;
    
    updateTranslation();
}

function copyTranslation() {
    const targetText = document.getElementById('targetText').value;
    if (!targetText) {
        showNotification('Kopyalanacak metin yok', 'error');
        return;
    }
    
    navigator.clipboard.writeText(targetText).then(() => {
        showNotification('Çevrilen metin kopyalandı!', 'success');
    });
}

function speakTranslation() {
    const targetText = document.getElementById('targetText').value;
    const targetLang = document.getElementById('targetLang').value;
    
    if (!targetText) {
        showNotification('Dinlenecek metin yok', 'error');
        return;
    }
    
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(targetText);
        utterance.lang = targetLang;
        speechSynthesis.speak(utterance);
    } else {
        showNotification('Ses sentezi desteklenmiyor', 'error');
    }
}

function clearAll() {
    if (confirm('Tüm metinleri temizleyin mi?')) {
        document.getElementById('sourceText').value = '';
        document.getElementById('targetText').value = '';
        document.getElementById('charCount').textContent = '0';
    }
}

window.addEventListener('load', () => {
    displayHistory();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>