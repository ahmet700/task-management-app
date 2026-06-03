# 📋 Görev Yönetim Sistemi

Modern, tasarımı mavi ve turuncu renklerle yapılmış, PHP 8.2+ ve MySQL kullanan profesyonel bir görev yönetim uygulaması.

## 🎨 Özellikler

- ✅ Modern Tailwind CSS arayüzü
- ✅ Mavi & Turuncu renk şeması
- ✅ jQuery ile interaktif özellikleri
- ✅ Responsive tasarım (mobil uyumlu)
- ✅ Rol bazlı erişim kontrolü (Admin, Manager, User)
- ✅ Proje yönetimi
- ✅ Görev yönetimi
- ✅ Kullanıcı yönetimi
- ✅ Profil yönetimi
- ✅ İstatistik paneli

## 🔧 Teknik Gereksinimler

- PHP 8.2 veya üstü
- MySQL 5.7 veya üstü
- Tarayıcı (Chrome, Firefox, Safari, Edge)

## 📁 Proje Yapısı

```
task-management-app/
├── config/
│   └── database.php          # Veritabanı konfigürasyonu
├── includes/
│   ├── header.php            # Üst kısım şablonu
│   └── footer.php            # Alt kısım şablonu
├── assets/
│   ├── css/
│   │   └── style.css         # Özel CSS stilleri
│   └── js/
│       └── main.js           # jQuery JavaScript kodu
├── pages/
│   ├── dashboard.php         # Dashboard sayfası
│   ├── projects.php          # Projeler sayfası
│   ├── tasks.php             # Görevler sayfası
│   ├── profile.php           # Profil sayfası
│   └── settings.php          # Ayarlar sayfası
├── api/
│   ├── tasks.php             # Görev API'si
│   ├── projects.php          # Proje API'si
│   ├── users.php             # Kullanıcı API'si
│   ├── profile.php           # Profil API'si
│   └── logout.php            # Çıkış API'si
├── index.php                 # Giriş sayfası
└── README.md                 # Bu dosya
```

## 🚀 Kurulum

### 1. Veritabanı Kurulumu

```sql
MySQL'de tasks.sql dosyasını çalıştırın
```

### 2. Dosyaları Sunucuya Yükleme

```bash
git clone <repository-url>
cd task-management-app
```

### 3. Konfigürasyon

`config/database.php` dosyasında veritabanı bilgilerini güncelleyin:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'gorev_yonetim');
```

### 4. Çalıştırma

```
http://localhost/task-management-app
```

## 👥 Demo Kullanıcılar

| Kullanıcı Adı | Şifre | Rol |
|---|---|---|
| admin | password | Admin |
| user | password | User |

## 📊 Veritabanı Tabloları

- **users**: Kullanıcı bilgileri
- **projects**: Proje bilgileri
- **project_members**: Proje üyeleri
- **tasks**: Görev bilgileri
- **task_comments**: Görev yorumları
- **task_attachments**: Görev dosyaları
- **tags**: Etiketler
- **task_tags**: Görev etiketleri
- **task_history**: Görev geçmişi

## 🔐 Güvenlik Özellikleri

- Şifre hashleme (bcrypt)
- PDO prepared statements (SQL injection koruması)
- Session kontrolü
- Rol bazlı erişim kontrolü

## 📝 Lisans

MIT License

## 👨‍💻 Geliştirici

Copilot tarafından oluşturulmuştur.

---

**Son Güncelleme**: 2026-06-03