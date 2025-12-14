# RANDEVU VE REZERVASYON SİSTEMİ - REVİZYON 2.0
## Aksaray İli Süper Yönetici Sistemi

### 📋 REVİZYON ÖZETİ
Bu revizyon, sistemin **tek bir yönetici hesabıyla birden fazla işletmeyi yönetebilecek** şekilde güncellenmesini içerir. Artık her işletme için ayrı hesap yerine, kategori bazında (Restoran/Kuaför) **Süper Yönetici** hesapları bulunmaktadır.

### 🆕 TEMEL DEĞİŞİKLİKLER

#### 1. Süper Yönetici Yapısı
- ❌ Eski: Her işletme için ayrı hesap
- ✅ Yeni: Kategori başına tek bir Süper Yönetici hesabı
- 🔑 **Restoran Yönetimi:** `restoranyonetim` / `123456`
- 🔑 **Kuaför Yönetimi:** `kuaforyonetim` / `147852`

#### 2. Aksaray İli Odaklı Sistem
- 📍 Tüm işletmeler **Aksaray** ilinde yer alır
- 🏪 Her işletme için **İl** ve **İlçe** bilgisi gösterilir
- 🔍 İşletme listeleme otomatik olarak Aksaray ile filtrelenir

#### 3. Çoklu İşletme Yönetimi
- 👨‍💼 Bir yönetici birden fazla işletmeyi yönetebilir
- 📊 Tek panelde tüm işletmelerin randevuları görüntülenir
- 🏢 Her randevuda hangi işletmeye ait olduğu belirtilir

### 🗂️ VERİTABANI DEĞİŞİKLİKLERİ

#### KULLANICILAR Tablosu
```sql
Role: ENUM('Müşteri', 'Süper Yönetici')  -- Revize edildi
```

**Yeni Hesaplar:**
| Kullanıcı Adı | Şifre | Rol | Kategori |
|---------------|-------|-----|----------|
| restoranyonetim | 123456 | Süper Yönetici | Restoranlar |
| kuaforyonetim | 147852 | Süper Yönetici | Kuaförler |

#### ISLETMELER Tablosu
```sql
Il VARCHAR(50) NOT NULL DEFAULT 'Aksaray'  -- YENİ ALAN
```

**Aksaray İşletmeleri:**

**Kuaför İşletmeleri (Yönetici: kuaforyonetim)**
1. Miss World Bayan Kuaför - Merkez - 150₺
2. Rapunzel Vip Güzellik Salonu - Merkez - 200₺
3. The Woman Studio Kuaför - Merkez - 180₺
4. Nurdan Bayan Kuaför - Merkez - 120₺

**Restoran İşletmeleri (Yönetici: restoranyonetim)**
1. The Hunger - Merkez - 350₺
2. Kardeşler - Merkez - 120₺
3. Mir Konak - Merkez - 280₺
4. Ömür Baba - Merkez - 90₺

### 🔄 UYGULAMA DEĞİŞİKLİKLERİ

#### 1. database.sql
- ✅ Role ENUM güncellendi (Süper Yönetici)
- ✅ ISLETMELER tablosuna `Il` alanı eklendi
- ✅ 8 adet Aksaray işletmesi eklendi
- ✅ İşletme-Yönetici ilişkileri kuruldu
- ✅ Örnek puanlamalar güncellendi

#### 2. get_isletmeler.php
- ✅ Aksaray filtresi eklendi (`WHERE i.Il = 'Aksaray'`)
- ✅ Sadece Aksaray'daki işletmeler listelenir

#### 3. index.php
- ✅ İşletme detaylarında İl/İlçe gösterimi
- ✅ "📍 İl / İlçe: Aksaray / Merkez" formatı

#### 4. login.php
- ✅ Süper Yönetici giriş kontrolü
- ✅ Yöneticinin kaç işletmesi olduğu hesaplanır
- ✅ Session'a kullanici_id, ad_soyad, role kaydedilir
- ✅ Test hesapları bilgisi güncellendi

#### 5. panel/business_panel.php
- ✅ **Çoklu işletme desteği** - En kritik değişiklik!
- ✅ Yönetilen işletmelerin kartlar halinde gösterimi
- ✅ Tüm işletmelerin randevularını tek panelde listeleme
- ✅ Her randevuda işletme adı ve konumu görüntülenir
- ✅ Güvenlik: Sadece kendi işletmelerinin randevularını yönetebilir
- ✅ İstatistikler tüm işletmeleri kapsıyor

### 🎯 KULLANIM SENARYOLARı

#### Senaryo 1: Kuaför Yöneticisi
1. `kuaforyonetim` / `147852` ile giriş yap
2. 4 kuaför işletmesini görüntüle
3. Tüm kuaförlerin randevularını tek panelde yönet
4. Randevu onaylama/reddetme

#### Senaryo 2: Restoran Yöneticisi
1. `restoranyonetim` / `123456` ile giriş yap
2. 4 restoran işletmesini görüntüle
3. Tüm restoranların randevularını tek panelde yönet
4. Randevu onaylama/reddetme

#### Senaryo 3: Müşteri
1. index.php'ye git (giriş gerektirmez)
2. Kategori seç (Kuaförler/Restoranlar)
3. Sadece Aksaray'daki işletmeleri gör
4. İşletme seç ve detayları incele
5. Randevu oluştur veya işletmeyi puanla

### 🔒 GÜVENLİK İYİLEŞTİRMELERİ

```php
// Eski Kod (Tek İşletme):
WHERE RANDEVUID = ? AND ISLETMELERID = ?

// Yeni Kod (Çoklu İşletme + Güvenlik):
WHERE r.RANDEVUID = ? AND i.KULLANICILERID = ?
```

- ✅ INNER JOIN ile işletme-yönetici ilişkisi doğrulanır
- ✅ Yönetici sadece kendi işletmelerinin randevularını görebilir
- ✅ Session kontrolü: Role = 'Süper Yönetici' olmalı

### 📊 YENİ PANEL ÖZELLİKLERİ

#### Dashboard Kartları
```
┌─────────────────────────────────────────┐
│ 🏪 Yönettiğiniz İşletmeler (4)         │
│ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐  │
│ │İşletme│ │İşletme│ │İşletme│ │İşletme│  │
│ │  1    │ │  2    │ │  3    │ │  4    │  │
│ └──────┘ └──────┘ └──────┘ └──────┘  │
└─────────────────────────────────────────┘
```

#### Randevu Tablosu
| ID | **İşletme** | Müşteri | Telefon | Tarih | Saat | İşlemler |
|----|-------------|---------|---------|-------|------|----------|
| #1 | 🏪 Miss World<br>Aksaray/Merkez | Zeynep A. | 0555... | 15.12.2025 | 14:00 | ✅❌ |

### 🚀 KURULUM (Güncelleme)

#### Mevcut Sistemden Güncelleme
```sql
-- 1. Veritabanını yedekle
mysqldump -u root rezervasyon_app > backup.sql

-- 2. Veritabanını sil ve yeniden oluştur
DROP DATABASE rezervasyon_app;

-- 3. Yeni database.sql dosyasını import et
mysql -u root < database.sql
```

#### Yeni Kurulum
1. XAMPP'i başlat (Apache + MySQL)
2. phpMyAdmin'e git
3. `database.sql` dosyasını import et
4. `http://localhost/rezervasyon-app/` adresine git

### 📝 KARŞILAŞTIRMA

| Özellik | Versiyon 1.0 | Versiyon 2.0 (Revize) |
|---------|--------------|------------------------|
| Yönetici Yapısı | Her işletme için ayrı hesap | Kategori başına tek hesap |
| İşletme Sayısı | Yönetici başına 1 | Yönetici başına çoklu |
| İl Filtresi | Yok | Aksaray özel |
| Panel Görünümü | Tek işletme | Çoklu işletme |
| İşletme Bilgisi | Yalnızca İlçe | İl + İlçe |
| Randevu Listesi | Bir işletme | Tüm işletmeler |

### 🎨 ARAYÜZ İYİLEŞTİRMELERİ

- 🏪 Yönetilen işletmeler kartlar halinde gösteriliyor
- 📊 Her randevuda işletme adı vurgulanıyor
- 🎯 İstatistikler tüm işletmeleri kapsıyor
- 📱 Responsive tasarım korundu

### 🔍 TEST ADIMLARI

#### Test 1: Süper Yönetici Girişi
```
✓ kuaforyonetim ile giriş yap
✓ 4 işletme görüntülensin
✓ Panel başlığı "Süper Yönetici Paneli" olsun
```

#### Test 2: Randevu Yönetimi
```
✓ Bekleyen randevuları gör
✓ Her randevuda işletme adı görünsün
✓ Onayla/Reddet butonları çalışsın
✓ İstatistikler doğru hesaplansın
```

#### Test 3: Müşteri Tarafı
```
✓ Sadece Aksaray işletmeleri listelenmeli
✓ İl/İlçe bilgisi görünmeli
✓ Randevu oluşturma çalışmalı
```

### 📞 DESTEK

**Sık Sorulan Sorular:**

**S: Eski hesaplarla giriş yapabilir miyim?**
C: Hayır, yeni Süper Yönetici hesaplarını kullanmalısınız.

**S: Başka illerdeki işletmeler eklenebilir mi?**
C: Evet, ISLETMELER tablosunda Il alanını değiştirerek ekleyebilirsiniz. Ancak get_isletmeler.php'de filtreyi güncellemeniz gerekir.

**S: Bir işletmeyi başka yöneticiye aktarabilir miyim?**
C: Evet, ISLETMELER tablosundaki KULLANICILERID'yi güncelleyin.

---
**Proje Durumu:** ✅ Revize Edildi ve Test Edildi  
**Versiyon:** 2.0 (Aksaray Süper Yönetici)  
**Son Güncelleme:** 14 Aralık 2025  
**Önemli Not:** Veritabanını yeniden import etmeyi unutmayın!
