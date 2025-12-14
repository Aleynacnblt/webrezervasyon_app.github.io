# RANDEVU VE REZERVASYON SİSTEMİ
## PHP / MySQL Web Uygulaması

### 📋 PROJE HAKKINDA
Bu proje, müşterilerin kullanıcı adı/şifre olmadan hızlıca randevu alabileceği ve işletme sahiplerinin bu randevuları yönetebileceği modern bir web uygulamasıdır.

### 🎯 ÖZELLİKLER
- ✅ Kullanıcı kaydı olmadan randevu alma
- ✅ Kategori bazlı işletme listeleme (Restoran, Kuaför)
- ✅ İşletme detay bilgileri ve ortalama puan görüntüleme
- ✅ AJAX ile dinamik veri yükleme
- ✅ İşletme yönetim paneli
- ✅ Randevu onaylama/reddetme sistemi
- ✅ Puanlama ve yorum sistemi
- ✅ Responsive tasarım
- ✅ Modern ve tematik arayüz

### 🗂️ DOSYA YAPISI
```
rezervasyon-app/
├── database.sql              # Veritabanı oluşturma scripti
├── db.php                    # PDO veritabanı bağlantısı
├── index.php                 # Müşteri randevu sayfası
├── login.php                 # İşletme giriş sayfası
├── get_isletmeler.php        # AJAX endpoint
├── css/
│   └── style.css             # Tematik stil dosyası
├── panel/
│   ├── business_panel.php    # İşletme yönetim paneli
│   └── logout.php            # Çıkış işlemi
└── README.md                 # Bu dosya
```

### 🛠️ KURULUM

#### 1. Veritabanı Kurulumu
1. XAMPP Control Panel'den Apache ve MySQL'i başlatın
2. phpMyAdmin'e gidin (http://localhost/phpmyadmin)
3. `database.sql` dosyasını import edin veya içeriğini SQL sekmesine yapıştırıp çalıştırın
4. `rezervasyon_app` veritabanı otomatik olarak oluşturulacaktır

#### 2. Dosyaları Yerleştirme
Tüm proje dosyalarını `C:\xampp\htdocs\rezervasyon-app\` klasörüne kopyalayın

#### 3. Veritabanı Bağlantısı
`db.php` dosyasındaki ayarları kontrol edin:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'rezervasyon_app');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP varsayılan şifre boş
```

### 🚀 KULLANIM

#### Müşteri Tarafı (index.php)
1. Tarayıcıda `http://localhost/rezervasyon-app/` adresine gidin
2. Kategori seçin (Restoranlar veya Kuaförler)
3. İşletme seçin ve detayları görüntüleyin
4. Randevu formunu doldurun ve gönderin
5. İsterseniz işletmeyi puanlayın

#### İşletme Tarafı (login.php)
1. Tarayıcıda `http://localhost/rezervasyon-app/login.php` adresine gidin
2. Test hesaplarından biriyle giriş yapın:
   - **Restoran:** Kullanıcı: `restoran` | Şifre: `123456`
   - **Kuaför:** Kullanıcı: `kuafor` | Şifre: `147852`
3. Bekleyen randevuları görüntüleyin
4. Randevuları onaylayın veya reddedin
5. İstatistikleri görüntüleyin

### 📊 VERİTABANI TABLOLARI

#### 1. KULLANICILAR
İşletme sahiplerinin giriş bilgileri
- KULLANICIID (PK)
- Kullanici_adi
- Sifre
- Role (ENUM)
- Ad_soyad

#### 2. KATAGORİLER
İşletme kategorileri
- KATAGORİLERID (PK)
- Kategori_Adi

#### 3. ISLETMELER
İşletme detay bilgileri
- ISLETMELERID (PK)
- KULLANICILERID
- KATAGORILERID
- Isletme_adi
- Adresi
- Ilce
- Fiyat_araligi
- Open_time
- Open_days

#### 4. RANDEVULAR
Müşteri randevu talepleri
- RANDEVUID (PK)
- ISLETMELERID
- Musteri_adi
- Musteri_telefon
- Randevu_gunu
- Randevu_saati
- Aciklama
- Durum (ENUM: Beklemede, Onaylandı, Reddedildi)

#### 5. PUANLAMA
İşletme puanları ve yorumları
- PUANLAMAID (PK)
- ISLETMELERID
- Musteri_adi
- Puanlama (1-5)
- Yorum
- Yorum_tarihi

### 🔒 GÜVENLİK ÖZELLİKLERİ
- ✅ PDO Prepared Statements (SQL Injection koruması)
- ✅ XSS koruması (htmlspecialchars)
- ✅ Input temizleme fonksiyonu
- ✅ Session güvenliği
- ✅ İşletme bazlı veri erişim kontrolü
- ✅ Form validasyonu

### 🎨 TEKNOLOJİLER
- **Backend:** PHP 7.4+
- **Veritabanı:** MySQL 5.7+ / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **AJAX:** Fetch API
- **Stil:** Gradient temalar, modern responsive tasarım

### 📱 RESPONSIVE TASARIM
Uygulama mobil, tablet ve masaüstü cihazlarda sorunsuz çalışır.

### 🔄 GELECEKTEKİ GELİŞTİRMELER
- [ ] Email/SMS bildirimleri
- [ ] Şifre hashleme (bcrypt/password_hash)
- [ ] Admin paneli
- [ ] Randevu takvim görünümü
- [ ] Müşteri profilleri
- [ ] Favori işletmeler
- [ ] Gelişmiş filtreleme ve arama

### 📝 NOTLAR
- Veritabanında yabancı anahtar (Foreign Key) kısıtlamaları kullanılmamıştır
- Veri bütünlüğü PHP tarafında kontrol edilmektedir
- Bildirim sistemi şu anda PHP yorumu olarak belirtilmiştir
- Test verileri otomatik olarak yüklenmektedir

### 👨‍💻 GELIŞTIRICI NOTLARI
Kodlar detaylı Türkçe yorumlarla açıklanmıştır. Her dosyanın başında dosya açıklaması bulunmaktadır.

### 📞 DESTEK
Herhangi bir sorun yaşarsanız:
1. XAMPP servislerinin çalıştığından emin olun
2. Veritabanının doğru import edildiğini kontrol edin
3. db.php'deki bağlantı bilgilerini doğrulayın
4. Tarayıcı konsol hatalarını kontrol edin (F12)

---
**Proje Durumu:** ✅ Tamamlandı ve Test Edildi
**Versiyon:** 1.0.0
**Son Güncelleme:** 14 Aralık 2024
