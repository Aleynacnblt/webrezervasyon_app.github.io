-- ===================================================================
-- RANDEVU VE REZERVASYON SİSTEMİ VERİTABANI
-- Veritabanı Adı: rezervasyon_app
-- Motor: InnoDB
-- Not: Yabancı anahtar kısıtlamaları kullanılmamaktadır (PHP'de kontrol edilecek)
-- ===================================================================

-- Veritabanını oluştur ve seç
DROP DATABASE IF EXISTS rezervasyon_app;
CREATE DATABASE rezervasyon_app CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE rezervasyon_app;

-- ===================================================================
-- TABLO 1: KULLANICILAR
-- Süper Yönetici hesaplarının giriş bilgilerini tutar
-- Her kategori için tek bir yönetici hesabı bulunur
-- ===================================================================
CREATE TABLE KULLANICILAR (
    KULLANICIID INT AUTO_INCREMENT PRIMARY KEY,
    Kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    Sifre VARCHAR(255) NOT NULL,
    Role ENUM('Müşteri', 'Süper Yönetici') NOT NULL,
    Ad_soyad VARCHAR(100) NOT NULL,
    Kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Başlangıç Verileri: Süper Yönetici Hesapları
INSERT INTO KULLANICILAR (Kullanici_adi, Sifre, Role, Ad_soyad) VALUES
('restoranyonetim', '123456', 'Süper Yönetici', 'Restoran Süper Yöneticisi'),
('kuaforyonetim', '147852', 'Süper Yönetici', 'Kuaför Süper Yöneticisi');

-- ===================================================================
-- TABLO 2: KATAGORİLER
-- İşletme kategorilerini tutar (Restoran, Kuaför vb.)
-- ===================================================================
CREATE TABLE KATAGORİLER (
    KATAGORİLERID INT AUTO_INCREMENT PRIMARY KEY,
    Kategori_Adi VARCHAR(100) NOT NULL UNIQUE,
    Kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Başlangıç Verileri: Kategoriler
INSERT INTO KATAGORİLER (Kategori_Adi) VALUES
('Restoranlar'),
('Kuaförler');

-- ===================================================================
-- TABLO 3: ISLETMELER
-- İşletmelerin detay bilgilerini tutar
-- Tüm işletmeler Aksaray ilinde yer alır ve Süper Yöneticiler tarafından yönetilir
-- ===================================================================
CREATE TABLE ISLETMELER (
    ISLETMELERID INT AUTO_INCREMENT PRIMARY KEY,
    KULLANICILERID INT NOT NULL,
    KATAGORILERID INT NOT NULL,
    Isletme_adi VARCHAR(200) NOT NULL,
    Adresi TEXT NOT NULL,
    Il VARCHAR(50) NOT NULL DEFAULT 'Aksaray',
    Ilce VARCHAR(100) NOT NULL,
    Fiyat_araligi DECIMAL(10,2) DEFAULT 0.00,
    Open_time VARCHAR(100),
    Open_days VARCHAR(200),
    Kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kullanici (KULLANICILERID),
    INDEX idx_kategori (KATAGORILERID),
    INDEX idx_il (Il),
    INDEX idx_ilce (Ilce)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Başlangıç Verileri: Aksaray İlindeki İşletmeler
-- Kuaför İşletmeleri (Süper Yönetici ID: 2)
INSERT INTO ISLETMELER (KULLANICILERID, KATAGORILERID, Isletme_adi, Adresi, Il, Ilce, Fiyat_araligi, Open_time, Open_days) VALUES
(2, 2, 'Miss World Bayan Kuaför', 'Cumhuriyet Meydanı Karşısı No:15', 'Aksaray', 'Merkez', 150.00, '09:00 - 19:00', 'Pazartesi-Cumartesi'),
(2, 2, 'Rapunzel Vip Güzellik Salonu', 'Atatürk Bulvarı No:78/A', 'Aksaray', 'Merkez', 200.00, '09:00 - 20:00', 'Her Gün'),
(2, 2, 'The Woman Studio Kuaför', 'Bankalar Caddesi No:45', 'Aksaray', 'Merkez', 180.00, '09:30 - 19:30', 'Pazartesi-Cumartesi'),
(2, 2, 'Nurdan Bayan Kuaför', 'Kışla Mahallesi Şehit Caddesi No:12', 'Aksaray', 'Merkez', 120.00, '08:30 - 18:30', 'Pazartesi-Cumartesi'),

-- Restoran İşletmeleri (Süper Yönetici ID: 1)
(1, 1, 'The Hunger', 'Zafer Meydanı Yanı No:23', 'Aksaray', 'Merkez', 350.00, '11:00 - 23:00', 'Her Gün'),
(1, 1, 'Kardeşler', 'Çarşı İçi No:8', 'Aksaray', 'Merkez', 120.00, '08:00 - 22:00', 'Her Gün'),
(1, 1, 'Mir Konak', 'Nevşehir Yolu Üzeri No:56', 'Aksaray', 'Merkez', 280.00, '12:00 - 00:00', 'Her Gün'),
(1, 1, 'Ömür Baba', 'Pazar Yeri Karşısı No:34', 'Aksaray', 'Merkez', 90.00, '07:00 - 21:00', 'Her Gün');

-- ===================================================================
-- TABLO 4: RANDEVULAR
-- Müşterilerin randevu taleplerini tutar
-- ===================================================================
CREATE TABLE RANDEVULAR (
    RANDEVUID INT AUTO_INCREMENT PRIMARY KEY,
    ISLETMELERID INT NOT NULL,
    Musteri_adi VARCHAR(100) NOT NULL,
    Musteri_telefon VARCHAR(20) NOT NULL,
    Randevu_gunu DATE NOT NULL,
    Randevu_saati TIME NOT NULL,
    Aciklama TEXT,
    Durum ENUM('Beklemede', 'Onaylandı', 'Reddedildi') DEFAULT 'Beklemede',
    Olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_isletme (ISLETMELERID),
    INDEX idx_durum (Durum),
    INDEX idx_tarih (Randevu_gunu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ===================================================================
-- TABLO 5: PUANLAMA
-- İşletmelere verilen puanlar ve yorumlar
-- ===================================================================
CREATE TABLE PUANLAMA (
    PUANLAMAID INT AUTO_INCREMENT PRIMARY KEY,
    ISLETMELERID INT NOT NULL,
    Musteri_adi VARCHAR(100) NOT NULL,
    Puanlama INT NOT NULL CHECK (Puanlama BETWEEN 1 AND 5),
    Yorum TEXT,
    Yorum_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_isletme (ISLETMELERID),
    INDEX idx_puan (Puanlama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Başlangıç Verileri: Örnek Puanlamalar (Aksaray İşletmeleri)
INSERT INTO PUANLAMA (ISLETMELERID, Musteri_adi, Puanlama, Yorum) VALUES
(1, 'Zeynep A.', 5, 'Miss World harika bir kuaför, çok memnun kaldım!'),
(1, 'Elif K.', 4, 'Profesyonel hizmet, uygun fiyat.'),
(2, 'Ayşe Y.', 5, 'Rapunzel Vip gerçekten VIP hizmet veriyor!'),
(3, 'Fatma D.', 4, 'The Woman Studio çok şık bir mekan.'),
(4, 'Nurten S.', 5, 'Nurdan Bayan Kuaför uygun fiyata kaliteli hizmet.'),
(5, 'Mehmet R.', 5, 'The Hunger muhteşem bir restoran, lezzet ve sunum harika!'),
(6, 'Ali K.', 4, 'Kardeşler Restoran çok samimi ve lezzetli.'),
(7, 'Hasan T.', 5, 'Mir Konak manzarası ve yemekleri mükemmel.'),
(8, 'İbrahim Y.', 4, 'Ömür Baba uygun fiyata doyurucu yemekler.');

-- ===================================================================
-- VERİTABANI OLUŞTURMA TAMAMLANDI
-- Toplam 5 tablo oluşturuldu: KULLANICILAR, KATAGORİLER, ISLETMELER, RANDEVULAR, PUANLAMA
-- ===================================================================
