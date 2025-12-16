<?php
/**
 * İŞLETME YÖNETİM PANELİ
 * İşletme sahiplerinin randevuları görüp yönetebileceği panel
 */

require_once '../db.php';

// Giriş kontrolü - Süper Yönetici olmalı
if (!isset($_SESSION['kullanici_id']) || $_SESSION['role'] !== 'Süper Yönetici') {
    header('Location: ../login.php');
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$kullanici_adi = $_SESSION['kullanici_adi'];
$ad_soyad = $_SESSION['ad_soyad'];

$message = '';
$message_type = '';

// RANDEVU DURUM GÜNCELLEMESİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $randevu_id = filter_var($_POST['randevu_id'], FILTER_VALIDATE_INT);
    $yeni_durum = clean_input($_POST['yeni_durum']);
    
    // Durum kontrolü
    if ($randevu_id && in_array($yeni_durum, ['Onaylandı', 'Reddedildi'])) {
        try {
            // Sadece bu yöneticiye ait işletmelerin randevularını güncelleyebilir (güvenlik)
            $stmt = $pdo->prepare("
                UPDATE RANDEVULAR r
                INNER JOIN ISLETMELER i ON r.ISLETMELERID = i.ISLETMELERID
                SET r.Durum = ? 
                WHERE r.RANDEVUID = ? AND i.KULLANICILERID = ?
            ");
            $stmt->execute([$yeni_durum, $randevu_id, $kullanici_id]);
            
            if ($stmt->rowCount() > 0) {
                $message = $yeni_durum === 'Onaylandı' 
                    ? '✅ Randevu başarıyla onaylandı!' 
                    : '❌ Randevu reddedildi.';
                $message_type = 'success';
                
                // NOT: Burada müşteriye SMS/Email bildirimi gönderildiği varsayılır
                // Örnek: sendNotification($randevu_id, $yeni_durum);
                
            } else {
                $message = 'Randevu güncellenemedi. Lütfen tekrar deneyin.';
                $message_type = 'error';
            }
            
        } catch (PDOException $e) {
            $message = 'Bir hata oluştu: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// RANDEVULARI GETİR (Bu yöneticiye ait TÜM işletmelerin randevuları)
try {
    // Önce yöneticinin işletmelerini getir
    $stmt_isletmeler = $pdo->prepare("
        SELECT ISLETMELERID, Isletme_adi, Il, Ilce 
        FROM ISLETMELER 
        WHERE KULLANICILERID = ?
        ORDER BY Isletme_adi
    ");
    $stmt_isletmeler->execute([$kullanici_id]);
    $yonetilen_isletmeler = $stmt_isletmeler->fetchAll();
    
    // Bekleyen randevuları getir (Tüm işletmeler için)
    $stmt = $pdo->prepare("
        SELECT 
            r.*,
            i.Isletme_adi,
            i.Il,
            i.Ilce,
            DATE_FORMAT(r.Randevu_gunu, '%d.%m.%Y') as Formatted_Gun,
            DATE_FORMAT(r.Randevu_saati, '%H:%i') as Formatted_Saat,
            DATE_FORMAT(r.Olusturma_tarihi, '%d.%m.%Y %H:%i') as Formatted_Olusturma
        FROM RANDEVULAR r
        INNER JOIN ISLETMELER i ON r.ISLETMELERID = i.ISLETMELERID
        WHERE i.KULLANICILERID = ? AND r.Durum = 'Beklemede'
        ORDER BY r.Randevu_gunu ASC, r.Randevu_saati ASC
    ");
    $stmt->execute([$kullanici_id]);
    $bekleyen_randevular = $stmt->fetchAll();
    
    // Tüm randevuları getir (istatistik için)
    $stmt = $pdo->prepare("
        SELECT 
            r.*,
            i.Isletme_adi,
            i.Il,
            i.Ilce,
            DATE_FORMAT(r.Randevu_gunu, '%d.%m.%Y') as Formatted_Gun,
            DATE_FORMAT(r.Randevu_saati, '%H:%i') as Formatted_Saat,
            DATE_FORMAT(r.Olusturma_tarihi, '%d.%m.%Y %H:%i') as Formatted_Olusturma
        FROM RANDEVULAR r
        INNER JOIN ISLETMELER i ON r.ISLETMELERID = i.ISLETMELERID
        WHERE i.KULLANICILERID = ?
        ORDER BY r.Olusturma_tarihi DESC
        LIMIT 100
    ");
    $stmt->execute([$kullanici_id]);
    $tum_randevular = $stmt->fetchAll();
    
    // İstatistikler (Tüm işletmeler için)
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as toplam,
            SUM(CASE WHEN r.Durum = 'Beklemede' THEN 1 ELSE 0 END) as beklemede,
            SUM(CASE WHEN r.Durum = 'Onaylandı' THEN 1 ELSE 0 END) as onaylandi,
            SUM(CASE WHEN r.Durum = 'Reddedildi' THEN 1 ELSE 0 END) as reddedildi
        FROM RANDEVULAR r
        INNER JOIN ISLETMELER i ON r.ISLETMELERID = i.ISLETMELERID
        WHERE i.KULLANICILERID = ?
    ");
    $stmt->execute([$kullanici_id]);
    $stats = $stmt->fetch();
    
} catch (PDOException $e) {
    $yonetilen_isletmeler = [];
    $bekleyen_randevular = [];
    $tum_randevular = [];
    $stats = ['toplam' => 0, 'beklemede' => 0, 'onaylandi' => 0, 'reddedildi' => 0];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İşletme Yönetim Paneli</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <!-- PANEL HEADER -->
        <div class="panel-header">
            <div>
                <h2>👨‍💼 Süper Yönetici Paneli</h2>
                <p>Hoş geldiniz, <?= htmlspecialchars($ad_soyad) ?> (<?= count($yonetilen_isletmeler) ?> İşletme)</p>
            </div>
            <div>
                <a href="logout.php" class="btn btn-danger">🚪 Çıkış Yap</a>
            </div>
        </div>

        <!-- İSTATİSTİKLER -->
        <div class="content">
            <!-- YÖNETİLEN İŞLETMELER LİSTESİ -->
            <?php if (count($yonetilen_isletmeler) > 0): ?>
                <div style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); padding: 20px; border-radius: 15px; margin-bottom: 25px;">
                    <h3 style="color: #d63447; margin-bottom: 15px;">🏪 Yönettiğiniz İşletmeler (<?= count($yonetilen_isletmeler) ?>)</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        <?php foreach ($yonetilen_isletmeler as $isletme): ?>
                            <div style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <h4 style="color: #667eea; margin-bottom: 8px;">📍 <?= htmlspecialchars($isletme['Isletme_adi']) ?></h4>
                                <p style="color: #666; margin: 0; font-size: 0.9em;">
                                    <?= htmlspecialchars($isletme['Il']) ?> / <?= htmlspecialchars($isletme['Ilce']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 15px; color: white; text-align: center;">
                    <h3 style="font-size: 2.5em; margin: 0;"><?= $stats['toplam'] ?></h3>
                    <p>Toplam Randevu</p>
                </div>
                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 15px; color: white; text-align: center;">
                    <h3 style="font-size: 2.5em; margin: 0;"><?= $stats['beklemede'] ?></h3>
                    <p>Bekleyen</p>
                </div>
                <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 20px; border-radius: 15px; color: white; text-align: center;">
                    <h3 style="font-size: 2.5em; margin: 0;"><?= $stats['onaylandi'] ?></h3>
                    <p>Onaylanan</p>
                </div>
                <div style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); padding: 20px; border-radius: 15px; color: white; text-align: center;">
                    <h3 style="font-size: 2.5em; margin: 0;"><?= $stats['reddedildi'] ?></h3>
                    <p>Reddedilen</p>
                </div>
            </div>

            <!-- MESAJLAR -->
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- BEKLEYEN RANDEVULAR -->
            <h2 style="color: #667eea; margin-bottom: 20px;">⏳ Bekleyen Randevular (<?= count($bekleyen_randevular) ?>)</h2>
            
            <?php if (count($bekleyen_randevular) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İşletme</th>
                                <th>Müşteri</th>
                                <th>Telefon</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Açıklama</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bekleyen_randevular as $randevu): ?>
                                <tr>
                                    <td><strong>#<?= $randevu['RANDEVUID'] ?></strong></td>
                                    <td>
                                        <strong style="color: #667eea;">🏪 <?= htmlspecialchars($randevu['Isletme_adi']) ?></strong><br>
                                        <small style="color: #999;"><?= htmlspecialchars($randevu['Il']) ?> / <?= htmlspecialchars($randevu['Ilce']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($randevu['Musteri_adi']) ?></td>
                                    <td><?= htmlspecialchars($randevu['Musteri_telefon']) ?></td>
                                    <td>📅 <?= $randevu['Formatted_Gun'] ?></td>
                                    <td>🕐 <?= $randevu['Formatted_Saat'] ?></td>
                                    <td><?= htmlspecialchars($randevu['Aciklama']) ?: '-' ?></td>
                                    <td><?= $randevu['Formatted_Olusturma'] ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="randevu_id" value="<?= $randevu['RANDEVUID'] ?>">
                                                <input type="hidden" name="yeni_durum" value="Onaylandı">
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Bu randevuyu onaylamak istediğinizden emin misiniz?')">
                                                    ✅ Onayla
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="randevu_id" value="<?= $randevu['RANDEVUID'] ?>">
                                                <input type="hidden" name="yeni_durum" value="Reddedildi">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Bu randevuyu reddetmek istediğinizden emin misiniz?')">
                                                    ❌ Reddet
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    ℹ️ Şu anda bekleyen randevu bulunmamaktadır.
                </div>
            <?php endif; ?>

            <!-- TÜM RANDEVULAR -->
            <h2 style="color: #667eea; margin-top: 50px; margin-bottom: 20px;">📋 Tüm Randevular</h2>
            
            <?php if (count($tum_randevular) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İşletme</th>
                                <th>Müşteri</th>
                                <th>Telefon</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tum_randevular as $randevu): ?>
                                <tr>
                                    <td><strong>#<?= $randevu['RANDEVUID'] ?></strong></td>
                                    <td>
                                        <strong style="color: #667eea;">🏪 <?= htmlspecialchars($randevu['Isletme_adi']) ?></strong><br>
                                        <small style="color: #999;"><?= htmlspecialchars($randevu['Il']) ?> / <?= htmlspecialchars($randevu['Ilce']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($randevu['Musteri_adi']) ?></td>
                                    <td><?= htmlspecialchars($randevu['Musteri_telefon']) ?></td>
                                    <td>📅 <?= $randevu['Formatted_Gun'] ?></td>
                                    <td>🕐 <?= $randevu['Formatted_Saat'] ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower(str_replace(['ı', 'ğ'], ['i', 'g'], $randevu['Durum'])) ?>">
                                            <?php
                                            $durum_icon = [
                                                'Beklemede' => '⏳',
                                                'Onaylandı' => '✅',
                                                'Reddedildi' => '❌'
                                            ];
                                            echo $durum_icon[$randevu['Durum']] . ' ' . $randevu['Durum'];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= $randevu['Formatted_Olusturma'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    ℹ️ Henüz hiç randevu kaydı bulunmamaktadır.
                </div>
            <?php endif; ?>

            <!-- ANA SAYFAYA DÖN -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="../index.html" class="btn btn-primary">🏠 Ana Sayfaya Dön</a>
            </div>
        </div>
    </div>

    <script>
        // Sayfa her 30 saniyede bir otomatik yenilenir (yeni randevuları görmek için)
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
