<?php
/**
 * ÇIKIŞ İŞLEMİ
 * Kullanıcı oturumunu sonlandırır ve giriş sayfasına yönlendirir
 */

session_start();

// Tüm session verilerini temizle
$_SESSION = [];

// Session cookie'sini sil
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Session'ı yok et
session_destroy();

// Giriş sayfasına yönlendir
header('Location: ../login.php');
exit;
?>
