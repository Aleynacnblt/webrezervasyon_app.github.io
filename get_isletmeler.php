<?php
/**
 * AJAX İŞLETME VERİ ÇEKME ENDPOİNT'İ
 * Seçilen kategoriye göre işletmeleri JSON formatında döndürür
 */

require_once 'db.php';

// JSON header ayarla
header('Content-Type: application/json; charset=utf-8');

// Kategori ID'si kontrol et
if (!isset($_GET['kategori_id']) || empty($_GET['kategori_id'])) {
    echo json_encode([]);
    exit;
}

$kategori_id = filter_var($_GET['kategori_id'], FILTER_VALIDATE_INT);

if (!$kategori_id) {
    echo json_encode([]);
    exit;
}

try {
    // İşletmeleri ve ortalama puanlarını getir (Sadece Aksaray ili)
    $stmt = $pdo->prepare("
        SELECT 
            i.*,
            COALESCE(AVG(p.Puanlama), 0) as avg_rating,
            COUNT(p.PUANLAMAID) as total_ratings
        FROM ISLETMELER i
        LEFT JOIN PUANLAMA p ON i.ISLETMELERID = p.ISLETMELERID
        WHERE i.KATAGORILERID = ? AND i.Il = 'Aksaray'
        GROUP BY i.ISLETMELERID
        ORDER BY i.Isletme_adi ASC
    ");
    
    $stmt->execute([$kategori_id]);
    $isletmeler = $stmt->fetchAll();
    
    // Ortalama puanı round et
    foreach ($isletmeler as &$isletme) {
        $isletme['avg_rating'] = round($isletme['avg_rating'], 1);
    }
    
    echo json_encode($isletmeler, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Hata durumunda boş array döndür
    echo json_encode([]);
}
?>
