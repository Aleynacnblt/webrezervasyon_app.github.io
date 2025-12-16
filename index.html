<?php
/**
 * MÜŞTERİ RANDEVU OLUŞTURMA SAYFASI
 * Kullanıcı adı/şifre olmadan randevu alma ve işletmeleri puanlama
 */

require_once 'db.php';

// Form gönderimi işlemleri
$message = '';
$message_type = '';

// RANDEVU OLUŞTURMA İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'create_appointment') {
        // Form verilerini al ve temizle
        $isletme_id = filter_var($_POST['isletme_id'], FILTER_VALIDATE_INT);
        $musteri_adi = clean_input($_POST['musteri_adi']);
        $musteri_telefon = clean_input($_POST['musteri_telefon']);
        $randevu_gunu = clean_input($_POST['randevu_gunu']);
        $randevu_saati = clean_input($_POST['randevu_saati']);
        $aciklama = clean_input($_POST['aciklama']);
        
        // Validasyon kontrolleri
        if ($isletme_id && !empty($musteri_adi) && !empty($musteri_telefon) && !empty($randevu_gunu) && !empty($randevu_saati)) {
            try {
                // Randevuyu veritabanına kaydet
                $stmt = $pdo->prepare("
                    INSERT INTO RANDEVULAR (ISLETMELERID, Musteri_adi, Musteri_telefon, Randevu_gunu, Randevu_saati, Aciklama, Durum) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Beklemede')
                ");
                $stmt->execute([$isletme_id, $musteri_adi, $musteri_telefon, $randevu_gunu, $randevu_saati, $aciklama]);
                
                $message = "✅ Randevunuz başarıyla iletildi! İşletme en kısa sürede size geri dönüş yapacaktır.";
                $message_type = 'success';
                
                // NOT: Burada müşteriye SMS/Email bildirimi gönderilebilir (şimdilik PHP yorumu olarak belirtilmiştir)
                
            } catch (PDOException $e) {
                $message = "❌ Randevu oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.";
                $message_type = 'error';
            }
        } else {
            $message = "⚠️ Lütfen tüm zorunlu alanları doldurun.";
            $message_type = 'error';
        }
    }
    
    // PUANLAMA İŞLEMİ
    elseif ($_POST['action'] === 'rate_business') {
        $isletme_id = filter_var($_POST['isletme_id_rate'], FILTER_VALIDATE_INT);
        $musteri_adi = clean_input($_POST['musteri_adi_rate']);
        $puanlama = filter_var($_POST['rating'], FILTER_VALIDATE_INT);
        $yorum = clean_input($_POST['yorum']);
        
        // Validasyon
        if ($isletme_id && !empty($musteri_adi) && $puanlama >= 1 && $puanlama <= 5) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO PUANLAMA (ISLETMELERID, Musteri_adi, Puanlama, Yorum) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$isletme_id, $musteri_adi, $puanlama, $yorum]);
                
                $message = "⭐ Puanlamanız başarıyla kaydedildi. Teşekkür ederiz!";
                $message_type = 'success';
                
            } catch (PDOException $e) {
                $message = "❌ Puanlama kaydedilirken bir hata oluştu.";
                $message_type = 'error';
            }
        } else {
            $message = "⚠️ Lütfen tüm alanları doğru şekilde doldurun.";
            $message_type = 'error';
        }
    }
}

// Kategorileri getir
$kategoriler = $pdo->query("SELECT * FROM KATAGORİLER ORDER BY Kategori_Adi")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Randevu Sistemi - Rezervasyon Yap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <a href="login.php" class="business-login-btn">🏢 İşletme Girişi</a>
            <h1>🎯 Randevu & Rezervasyon Sistemi</h1>
            <p>Hızlı ve kolay randevu alın, deneyiminizi paylaşın!</p>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <!-- MESAJLAR -->
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- KATEGORİ SEÇİMİ -->
            <div class="form-group">
                <label for="kategori">📂 Kategori Seçin:</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Kategori Seçiniz --</option>
                    <?php foreach ($kategoriler as $kategori): ?>
                        <option value="<?= $kategori['KATAGORİLERID'] ?>">
                            <?= htmlspecialchars($kategori['Kategori_Adi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- İŞLETME SEÇİMİ (AJAX ile yüklenecek) -->
            <div class="form-group" id="isletme-container" style="display:none;">
                <label for="isletme">🏪 İşletme Seçin:</label>
                <select id="isletme" name="isletme" required>
                    <option value="">-- Önce kategori seçiniz --</option>
                </select>
            </div>

            <!-- Loading Spinner -->
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Yükleniyor...</p>
            </div>

            <!-- İŞLETME DETAY BİLGİLERİ -->
            <div class="business-info" id="business-info">
                <h3 id="info-name">İşletme Adı</h3>
                <div class="info-row">
                    <span class="info-label">📍 Adres:</span>
                    <span class="info-value" id="info-address">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📍 İl / İlçe:</span>
                    <span class="info-value" id="info-ilce">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">💰 Fiyat Aralığı:</span>
                    <span class="info-value" id="info-price">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🕒 Çalışma Saatleri:</span>
                    <span class="info-value" id="info-hours">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📅 Açık Günler:</span>
                    <span class="info-value" id="info-days">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">⭐ Ortalama Puan:</span>
                    <span class="info-value rating-stars" id="info-rating">-</span>
                </div>
            </div>

            <!-- RANDEVU FORMU -->
            <form method="POST" id="appointment-form" style="display:none;">
                <input type="hidden" name="action" value="create_appointment">
                <input type="hidden" name="isletme_id" id="isletme_id_hidden">

                <div class="form-group">
                    <label for="musteri_adi">👤 Adınız Soyadınız: *</label>
                    <input type="text" id="musteri_adi" name="musteri_adi" required placeholder="Örn: Ahmet Yılmaz">
                </div>

                <div class="form-group">
                    <label for="musteri_telefon">📱 Telefon Numaranız: *</label>
                    <input type="tel" id="musteri_telefon" name="musteri_telefon" required placeholder="Örn: 0555 123 45 67">
                </div>

                <div class="form-group">
                    <label for="randevu_gunu">📅 Randevu Tarihi: *</label>
                    <input type="date" id="randevu_gunu" name="randevu_gunu" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="randevu_saati">🕐 Randevu Saati: *</label>
                    <input type="time" id="randevu_saati" name="randevu_saati" required>
                </div>

                <div class="form-group">
                    <label for="aciklama">📝 Açıklama / Özel İstekler:</label>
                    <textarea id="aciklama" name="aciklama" placeholder="Varsa özel isteklerinizi buraya yazabilirsiniz..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-full">📤 Randevu Talebini Gönder</button>
            </form>

            <!-- PUANLAMA BUTONU -->
            <div id="rating-section" style="display:none; margin-top: 30px; text-align: center;">
                <button type="button" class="btn btn-warning" onclick="openRatingModal()">
                    ⭐ Bu İşletmeyi Puanla
                </button>
            </div>
        </div>
    </div>

    <!-- PUANLAMA MODAL -->
    <div class="modal" id="rating-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeRatingModal()">&times;</span>
            <h2 style="text-align:center; color:#667eea; margin-bottom:20px;">⭐ İşletmeyi Puanlayın</h2>
            
            <form method="POST" id="rating-form">
                <input type="hidden" name="action" value="rate_business">
                <input type="hidden" name="isletme_id_rate" id="isletme_id_rate">
                <input type="hidden" name="rating" id="rating_value" value="0">

                <div class="form-group">
                    <label>Puanınız:</label>
                    <div class="star-rating" id="star-rating">
                        <span class="star" data-value="1">★</span>
                        <span class="star" data-value="2">★</span>
                        <span class="star" data-value="3">★</span>
                        <span class="star" data-value="4">★</span>
                        <span class="star" data-value="5">★</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="musteri_adi_rate">👤 Adınız: *</label>
                    <input type="text" id="musteri_adi_rate" name="musteri_adi_rate" required>
                </div>

                <div class="form-group">
                    <label for="yorum">💬 Yorumunuz:</label>
                    <textarea id="yorum" name="yorum" placeholder="Deneyiminizi paylaşın..."></textarea>
                </div>

                <button type="submit" class="btn btn-success btn-full">💾 Puanı Kaydet</button>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        // Seçili işletme bilgilerini sakla
        let selectedBusiness = null;

        // Kategori değiştiğinde işletmeleri getir (AJAX)
        document.getElementById('kategori').addEventListener('change', function() {
            const kategoriId = this.value;
            const isletmeSelect = document.getElementById('isletme');
            const isletmeContainer = document.getElementById('isletme-container');
            const loading = document.getElementById('loading');
            
            if (kategoriId) {
                // Loading göster
                loading.style.display = 'block';
                
                // AJAX ile işletmeleri getir
                fetch('get_isletmeler.php?kategori_id=' + kategoriId)
                    .then(response => response.json())
                    .then(data => {
                        isletmeSelect.innerHTML = '<option value="">-- İşletme Seçiniz --</option>';
                        
                        if (data.length > 0) {
                            data.forEach(isletme => {
                                const option = document.createElement('option');
                                option.value = isletme.ISLETMELERID;
                                option.textContent = isletme.Isletme_adi + ' - ' + isletme.Ilce;
                                option.dataset.business = JSON.stringify(isletme);
                                isletmeSelect.appendChild(option);
                            });
                            isletmeContainer.style.display = 'block';
                        } else {
                            alert('Bu kategoride henüz işletme bulunmamaktadır.');
                        }
                        
                        loading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        alert('İşletmeler yüklenirken bir hata oluştu.');
                        loading.style.display = 'none';
                    });
            } else {
                isletmeContainer.style.display = 'none';
                document.getElementById('business-info').classList.remove('active');
                document.getElementById('appointment-form').style.display = 'none';
                document.getElementById('rating-section').style.display = 'none';
            }
        });

        // İşletme seçildiğinde detay bilgilerini göster
        document.getElementById('isletme').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value) {
                selectedBusiness = JSON.parse(selectedOption.dataset.business);
                
                // İşletme bilgilerini göster
                document.getElementById('info-name').textContent = selectedBusiness.Isletme_adi;
                document.getElementById('info-address').textContent = selectedBusiness.Adresi;
                document.getElementById('info-ilce').textContent = selectedBusiness.Il + ' / ' + selectedBusiness.Ilce;
                document.getElementById('info-price').textContent = selectedBusiness.Fiyat_araligi + ' ₺';
                document.getElementById('info-hours').textContent = selectedBusiness.Open_time || 'Belirtilmemiş';
                document.getElementById('info-days').textContent = selectedBusiness.Open_days || 'Belirtilmemiş';
                
                // Ortalama puanı göster
                const avgRating = selectedBusiness.avg_rating || 0;
                const stars = '★'.repeat(Math.round(avgRating)) + '☆'.repeat(5 - Math.round(avgRating));
                document.getElementById('info-rating').textContent = stars + ' (' + avgRating.toFixed(1) + '/5)';
                
                // İşletme bilgilerini ve formu göster
                document.getElementById('business-info').classList.add('active');
                document.getElementById('appointment-form').style.display = 'block';
                document.getElementById('rating-section').style.display = 'block';
                document.getElementById('isletme_id_hidden').value = selectedBusiness.ISLETMELERID;
                document.getElementById('isletme_id_rate').value = selectedBusiness.ISLETMELERID;
                
            } else {
                document.getElementById('business-info').classList.remove('active');
                document.getElementById('appointment-form').style.display = 'none';
                document.getElementById('rating-section').style.display = 'none';
            }
        });

        // Puanlama modalı aç
        function openRatingModal() {
            document.getElementById('rating-modal').style.display = 'block';
        }

        // Puanlama modalı kapat
        function closeRatingModal() {
            document.getElementById('rating-modal').style.display = 'none';
        }

        // Yıldız puanlama sistemi
        const stars = document.querySelectorAll('.star');
        const ratingValue = document.getElementById('rating_value');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                ratingValue.value = value;
                
                // Tüm yıldızları pasif yap
                stars.forEach(s => s.classList.remove('active'));
                
                // Seçilen yıldıza kadar aktif yap
                for (let i = 0; i < value; i++) {
                    stars[i].classList.add('active');
                }
            });

            // Hover efekti
            star.addEventListener('mouseover', function() {
                const value = this.dataset.value;
                stars.forEach(s => s.classList.remove('active'));
                for (let i = 0; i < value; i++) {
                    stars[i].classList.add('active');
                }
            });
        });

        // Modal dışına tıklandığında kapat
        window.onclick = function(event) {
            const modal = document.getElementById('rating-modal');
            if (event.target === modal) {
                closeRatingModal();
            }
        }
    </script>
</body>
</html>
