<?php
$pageTitle = 'Hava Durumu';
require_once __DIR__ . '/../includes/header.php';
requireIfNotLoggedIn();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-2">🌤️ Hava Durumu Bilgisi</h2>
        <p class="text-gray-600">Şehrinizin hava durumunu öğrenin (OpenWeatherMap API)</p>
    </div>

    <!-- Arama Alanı -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex gap-2">
            <input 
                type="text" 
                id="cityInput" 
                placeholder="Şehir adı girin... (örn: Istanbul)" 
                class="form-control flex-1"
                onkeypress="if(event.key === 'Enter') searchWeather()"
            >
            <button onclick="searchWeather()" class="btn-primary">
                🔍 Ara
            </button>
        </div>
    </div>

    <!-- Hava Durumu Bilgileri -->
    <div id="weatherContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
            <p>Bir şehir adı girerek hava durumunu öğrenin</p>
        </div>
    </div>

    <!-- Konum Bilgisi -->
    <div class="bg-blue-50 rounded-lg p-4 mt-6 text-sm text-gray-600">
        <p>💡 <strong>İpucu:</strong> Konumunuzun hava durumunu almak için tarayıcıdan konuma erişim izni vermeyi deneyin.</p>
        <button onclick="getLocationWeather()" class="btn-secondary mt-2">
            📍 Konumumu Kullan
        </button>
    </div>
</div>

<style>
    .weather-card {
        background: linear-gradient(135deg, #3b82f6 0%, #f97316 100%);
        color: white;
    }

    .weather-icon {
        font-size: 64px;
        margin: 20px 0;
    }

    .weather-detail {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.3);
    }

    .detail-item {
        text-align: center;
    }

    .detail-label {
        font-size: 12px;
        opacity: 0.8;
    }

    .detail-value {
        font-size: 20px;
        font-weight: bold;
        margin-top: 5px;
    }
</style>

<script>
const API_KEY = 'YOUR_OPENWEATHERMAP_API_KEY'; // https://openweathermap.org/api adresinden ücretsiz API anahtarı alın

async function searchWeather() {
    const city = document.getElementById('cityInput').value.trim();
    
    if (!city) {
        showNotification('Lütfen bir şehir adı girin', 'error');
        return;
    }
    
    if (!API_KEY || API_KEY === 'YOUR_OPENWEATHERMAP_API_KEY') {
        showNotification('API anahtarı yapılandırılmadı. OpenWeatherMap\'den ücretsiz API anahtarı alın.', 'error');
        return;
    }
    
    try {
        showLoading();
        const response = await fetch(
            `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=tr&appid=${API_KEY}`
        );
        
        if (!response.ok) {
            if (response.status === 404) {
                showNotification('Şehir bulunamadı', 'error');
                return;
            }
            throw new Error('API isteği başarısız');
        }
        
        const data = await response.json();
        displayWeather(data);
    } catch (error) {
        showNotification('Hava durumu alınamadı: ' + error.message, 'error');
    }
}

function displayWeather(data) {
    const container = document.getElementById('weatherContainer');
    const weather = data.weather[0];
    const main = data.main;
    const wind = data.wind;
    const clouds = data.clouds.all;
    
    // Hava durumuna göre emoji seç
    const weatherEmojis = {
        'Clear': '☀️',
        'Clouds': '☁️',
        'Rain': '🌧️',
        'Drizzle': '🌦️',
        'Thunderstorm': '⛈️',
        'Snow': '❄️',
        'Mist': '🌫️'
    };
    
    const emoji = weatherEmojis[weather.main] || '🌤️';
    
    container.innerHTML = `
        <div class="weather-card rounded-lg shadow-md p-8">
            <h3 class="text-3xl font-bold">${data.name}, ${data.sys.country}</h3>
            <div class="weather-icon">${emoji}</div>
            <p class="text-5xl font-bold">${Math.round(main.temp)}°C</p>
            <p class="text-xl mt-2 capitalize">${weather.description}</p>
            
            <div class="weather-detail">
                <div class="detail-item">
                    <div class="detail-label">Hissedilen</div>
                    <div class="detail-value">${Math.round(main.feels_like)}°C</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Min/Max</div>
                    <div class="detail-value">${Math.round(main.temp_min)}° / ${Math.round(main.temp_max)}°</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nem</div>
                    <div class="detail-value">${main.humidity}%</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-xl font-bold text-gray-800 mb-4">📊 Detaylı Bilgiler</h4>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <span class="text-gray-600">💨 Rüzgar Hızı</span>
                    <span class="font-bold text-gray-800">${wind.speed} m/s</span>
                </div>
                
                <div class="flex justify-between items-center border-b pb-3">
                    <span class="text-gray-600">☁️ Bulutluluk</span>
                    <span class="font-bold text-gray-800">${clouds}%</span>
                </div>
                
                <div class="flex justify-between items-center border-b pb-3">
                    <span class="text-gray-600">🔽 Basınç</span>
                    <span class="font-bold text-gray-800">${main.pressure} hPa</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">👁️ Görüş Mesafesi</span>
                    <span class="font-bold text-gray-800">${(data.visibility / 1000).toFixed(2)} km</span>
                </div>
            </div>
            
            <button onclick="searchWeather()" class="btn-orange w-full mt-6">
                🔄 Yenile
            </button>
        </div>
    `;
}

function getLocationWeather() {
    if (!navigator.geolocation) {
        showNotification('Konuma dayalı hizmetler desteklenmiyor', 'error');
        return;
    }
    
    showNotification('Konumunuz alınıyor...', 'info');
    
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const { latitude, longitude } = position.coords;
            
            if (!API_KEY || API_KEY === 'YOUR_OPENWEATHERMAP_API_KEY') {
                showNotification('API anahtarı yapılandırılmadı', 'error');
                return;
            }
            
            try {
                const response = await fetch(
                    `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&units=metric&lang=tr&appid=${API_KEY}`
                );
                const data = await response.json();
                displayWeather(data);
            } catch (error) {
                showNotification('Hava durumu alınamadı', 'error');
            }
        },
        (error) => {
            showNotification('Konuma erişim reddedildi: ' + error.message, 'error');
        }
    );
}

function showLoading() {
    const container = document.getElementById('weatherContainer');
    container.innerHTML = `
        <div class="col-span-full text-center py-12">
            <div class="inline-block animate-spin h-12 w-12 border-4 border-blue-500 border-t-orange-500 rounded-full mb-4"></div>
            <p class="text-gray-600">Hava durumu yükleniyor...</p>
        </div>
    `;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>