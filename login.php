<?php
/**
 * İŞLETME GİRİŞ SAYFASI
 * İşletme sahipleri buradan sisteme giriş yapar
 */

require_once 'db.php';

// Zaten giriş yapmışsa panel'e yönlendir
if (isset($_SESSION['isletme_id'])) {
    header('Location: panel/business_panel.php');
    exit;
}

$error = '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici_adi = clean_input($_POST['kullanici_adi']);
    $sifre = clean_input($_POST['sifre']);
    
    if (!empty($kullanici_adi) && !empty($sifre)) {
        try {
            // Süper Yönetici kullanıcısını kontrol et
            $stmt = $pdo->prepare("
                SELECT * FROM KULLANICILAR
                WHERE Kullanici_adi = ? AND Sifre = ? AND Role = 'Süper Yönetici'
            ");
            $stmt->execute([$kullanici_adi, $sifre]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Bu yöneticinin kaç işletmesi olduğunu kontrol et
                $stmt_count = $pdo->prepare("
                    SELECT COUNT(*) as isletme_sayisi 
                    FROM ISLETMELER 
                    WHERE KULLANICILERID = ?
                ");
                $stmt_count->execute([$user['KULLANICIID']]);
                $count_result = $stmt_count->fetch();
                
                // Oturum bilgilerini kaydet
                $_SESSION['kullanici_id'] = $user['KULLANICIID'];
                $_SESSION['kullanici_adi'] = $user['Kullanici_adi'];
                $_SESSION['ad_soyad'] = $user['Ad_soyad'];
                $_SESSION['role'] = $user['Role'];
                $_SESSION['isletme_sayisi'] = $count_result['isletme_sayisi'];
                
                // Panele yönlendir
                header('Location: panel/business_panel.php');
                exit;
            } else {
                $error = 'Kullanıcı adı veya şifre hatalı!';
            }
            
        } catch (PDOException $e) {
            $error = 'Giriş yapılırken bir hata oluştu. Lütfen tekrar deneyin.';
        }
    } else {
        $error = 'Lütfen tüm alanları doldurun.';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İşletme Girişi - Randevu Sistemi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>🏢 İşletme Girişi</h2>
            <p>Randevu yönetim paneline erişim</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                ❌ <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="kullanici_adi">👤 Kullanıcı Adı:</label>
                <input type="text" id="kullanici_adi" name="kullanici_adi" required autofocus placeholder="Kullanıcı adınızı girin">
            </div>

            <div class="form-group">
                <label for="sifre">🔒 Şifre:</label>
                <input type="password" id="sifre" name="sifre" required placeholder="Şifrenizi girin">
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                🚀 Giriş Yap
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="index.html" style="color: #667eea; text-decoration: none;">
                ← Ana Sayfaya Dön
            </a>
        </div>

        <!-- Bilgi Kutusu -->
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 10px; font-size: 0.9em;">
            <strong>📌 Süper Yönetici Hesapları:</strong><br>
            <div style="margin-top: 10px;">
                <strong>Restoran Yönetimi:</strong> <code>restoranyonetim</code> | şifre: <code>123456</code><br>
                <strong>Kuaför Yönetimi:</strong> <code>kuaforyonetim</code> | şifre: <code>147852</code>
            </div>
        </div>
    </div>
</body>
</html>
