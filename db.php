<?php
/**
 * VERİTABANI BAĞLANTI DOSYASI
 * PDO kullanarak MySQL veritabanına güvenli bağlantı sağlar
 */

// Veritabanı yapılandırma bilgileri
define('DB_HOST', 'localhost');
define('DB_NAME', 'rezervasyon_app');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP varsayılan şifre boş

try {
    // PDO bağlantısı oluştur
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Hata yönetimi
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Associative array olarak getir
            PDO::ATTR_EMULATE_PREPARES => false, // Gerçek prepared statements kullan
        ]
    );
} catch (PDOException $e) {
    // Bağlantı hatası durumunda kullanıcıya mesaj göster
    die("Veritabanı Bağlantı Hatası: " . $e->getMessage());
}

/**
 * Güvenli şekilde veri temizleme fonksiyonu
 * XSS saldırılarını önlemek için kullanılır
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Session başlatma (güvenli)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
