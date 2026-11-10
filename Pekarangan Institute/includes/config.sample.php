<?php
/**
 * Konfigurasi Sistem Pekarangan Institute (Firebase RTDB)
 *
 * Salin file ini menjadi 'config.php' di folder yang sama (includes/config.php)
 * dan sesuaikan URL Firebase RTDB serta path file JSON service account Anda.
 * File config.php dan berkas JSON service account bersifat rahasia dan tidak boleh di-commit.
 */

// URL Firebase Realtime Database
// Contoh untuk region Singapore (asia-southeast1):
// https://<project-id>-default-rtdb.asia-southeast1.firebasedatabase.app
define('FIREBASE_DB_URL', 'https://pekarangan-project-default-rtdb.asia-southeast1.firebasedatabase.app');

// Path absolut atau relatif ke berkas kunci Service Account Google (format JSON)
// Disarankan diletakkan di luar document root web server (misal satu tingkat di atas public_html)
define('FIREBASE_KUNCI_PATH', __DIR__ . '/../../kunci-firebase.json');

// URL Dasar Aplikasi (sesuaikan dengan nama folder di web server)
// Contoh: '/pekarangan-v2' atau '/pekarangan' atau '' jika di root domain
define('BASE_URL', '/pekarangan-v2');

// Lingkungan Aplikasi: 'lokal' atau 'produksi'
// Pada 'lokal', error PHP akan ditampilkan untuk memudahkan pengembangan.
// Pada 'produksi', error disembunyikan demi keamanan.
define('APP_ENV', 'lokal');

// Batas Ukuran Unggahan Berkas (dalam satuan byte)
define('BATAS_UNGGAH_GAMBAR', 2 * 1024 * 1024); // 2 MB
define('BATAS_UNGGAH_BERKAS', 5 * 1024 * 1024); // 5 MB

// Zona Waktu Baku
date_default_timezone_set('Asia/Jakarta');

// Penanganan Tampilan Error
if (APP_ENV === 'lokal') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
