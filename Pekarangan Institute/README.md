# Pekarangan Institute - Prototype Website (Firebase Realtime Database)

Website purwarupa (prototype) resmi **Pekarangan Institute**, inisiatif kewirausahaan sosial berbasis komunitas dengan konsep **Berbelanja, Berbagi, Berdampak**. Website ini memadukan profil inisiatif, katalog produk hasil pekarangan, penggalangan donasi program sosial, transparansi pertanggungjawaban dana, serta panel administrasi sederhana.

Seluruh data aplikasi disimpan di **Firebase Realtime Database (RTDB)** dan diakses secara aman oleh backend PHP melalui REST API menggunakan autentikasi **Google Service Account (OAuth2 Access Token via JWT RS256)** tanpa ketergantungan library luar atau SDK (cukup cURL dan OpenSSL bawaan PHP). Rules RTDB dikunci total (`.read: false, .write: false`), sehingga peramban publik tidak pernah memuat kredensial Firebase.

---

## 1. Kebutuhan Sistem

- **PHP 8.0 atau lebih baru** dengan ekstensi aktif:
  - `curl` (komunikasi REST API ke Firebase dan Google OAuth2)
  - `openssl` (penandatanganan JWT RS256 untuk Service Account)
  - `fileinfo` (validasi tipe MIME berkas foto/dokumen)
  - `mbstring` (penanganan string multibyte & slug)
  - `session` (manajemen sesi aman admin)
- **Akun Google Firebase** dengan **Realtime Database** aktif (disarankan region Singapore: `asia-southeast1`).
- **Berkas Kunci Google Service Account (format JSON)**.
- **Web Server Apache** (misalnya via paket **Laragon**, **XAMPP**, atau web hosting bersama/cPanel) dengan dukungan berkas `.htaccess`.
- Peramban web modern (Google Chrome, Mozilla Firefox, Microsoft Edge, Safari).

---

## 2. Panduan Penyiapan Firebase & Service Account

Ikuti langkah-langkah berikut untuk menyiapkan Firebase RTDB:

### Langkah 1: Buat Project Firebase
1. Buka [Firebase Console](https://console.firebase.google.com/) dan login dengan akun Google Anda.
2. Klik **Add project** (Tambah project), beri nama project (misalnya `pekarangan-institute`), lalu ikuti panduan hingga project selesai dibuat.

### Langkah 2: Aktifkan Realtime Database (Singapore)
1. Pada menu sebelah kiri console, pilih **Build &rarr; Realtime Database**.
2. Klik **Create Database**.
3. Pilih lokasi database: **Singapore (`asia-southeast1`)** untuk latensi tercepat dari Indonesia.
4. Pada pilihan Security Rules, pilih **Start in locked mode**.

### Langkah 3: Terapkan Aturan Keamanan (Rules)
1. Buka tab **Rules** pada Realtime Database.
2. Masukkan aturan berikut (kunci total untuk browser publik):
   ```json
   {
     "rules": {
       ".read": false,
       ".write": false
     }
   }
   ```
3. Klik **Publish**. Semua akses data dilakukan secara aman oleh server PHP melalui Service Account.

### Langkah 4: Unduh Kunci Service Account (Private Key JSON)
1. Klik ikon gerigi (Settings) di sebelah kiri atas &rarr; pilih **Project settings**.
2. Buka tab **Service accounts**.
3. Pastikan bahasa terpilih adalah Node.js atau lainnya, lalu klik tombol **Generate new private key** (Buat kunci privat baru).
4. Klik **Generate key**. Browser Anda akan mengunduh satu berkas JSON rahasia.
5. **PENTING**:
   - Jangan pernah melakukan *commit* berkas JSON ini ke repositori Git!
   - Simpan berkas ini di lokasi yang aman di komputer lokal/server Anda, idealnya di luar direktori publik web server (misalnya satu tingkat di atas `public_html` atau di folder `includes/` yang telah dilindungi `.htaccess` `Require all denied`).

---

## 3. Panduan Instalasi Lokal Langkah Demi Langkah

### Langkah 1: Tempatkan Folder Proyek
- Jika menggunakan **Laragon**: letakkan di `C:\laragon\www\pekarangan-v2`.
- Jika menggunakan **XAMPP**: letakkan di `C:\xampp\htdocs\pekarangan-v2`.

### Langkah 2: Buat Berkas Konfigurasi (`config.php`)
1. Masuk ke folder `includes/`.
2. Salin berkas `config.sample.php` menjadi `config.php`.
3. Buka `includes/config.php` dan sesuaikan nilainya:
   ```php
   // URL Realtime Database dari console Firebase Anda
   define('FIREBASE_DB_URL', 'https://<project-id>-default-rtdb.asia-southeast1.firebasedatabase.app');

   // Path absolut atau relatif ke berkas JSON service account yang telah Anda unduh
   define('FIREBASE_KUNCI_PATH', __DIR__ . '/kunci-firebase.json');

   // URL dasar folder aplikasi
   define('BASE_URL', '/pekarangan-v2');

   // Lingkungan pengembangan
   define('APP_ENV', 'lokal');
   ```

### Langkah 3: Inisialisasi Admin Pertama & Data Contoh
1. Buka peramban dan akses alamat:
   [http://localhost/pekarangan-v2/admin/setup.php](http://localhost/pekarangan-v2/admin/setup.php)
2. Pada mode lokal (`APP_ENV = 'lokal'`), Anda dapat mengklik tombol **Isi Data Contoh (Seed Data)** untuk mengisi produk, program, pesanan, donasi, laporan, dan pengaturan awal dari `database/seed.json` (seluruhnya bertanda `is_contoh = 1`).
3. Isi formulir **Setup Admin Pertama**: Nama Lengkap, Alamat Email, dan Kata Sandi kuat (minimal 8 karakter, kombinasi huruf besar, kecil, dan angka).
4. Klik **Buat Akun Administrator**. Halaman setup otomatis dinonaktifkan (HTTP 404) setelah akun admin pertama terdaftar.

### Langkah 4: Uji Koneksi Firebase RTDB
1. Masuk ke halaman login admin rahasia: [http://localhost/pekarangan-v2/admin/login.php](http://localhost/pekarangan-v2/admin/login.php).
2. Setelah login, buka menu **Pengaturan** &rarr; klik tombol **⚡ Uji Koneksi Firebase** (atau langsung akses [http://localhost/pekarangan-v2/admin/tes-koneksi.php](http://localhost/pekarangan-v2/admin/tes-koneksi.php)).
3. Halaman ini akan memverifikasi cURL, OpenSSL, pembacaan berkas kunci, pembuatan token OAuth2 Google, serta melakukan uji tulis, baca, dan hapus pada node `/_tes`.

---

## 4. Struktur Berkas & Folder

```text
pekarangan/
├── .gitignore                  Mengabaikan config.php, berkas JSON kunci service account, dan cache token.
├── .htaccess                   Proteksi direktori root, pemblokiran dotfiles, dan pencegahan directory browsing.
├── robots.txt                  Instruksi mesin perayap (Disallow: /admin/).
├── index.php                   Halaman beranda publik (hero profil, langkah kerja, produk & program terbaru, ajakan).
├── tentang.php                 Profil inisiatif, tiga pilar nilai, dan simulasi alokasi dana operasional/sosial.
├── produk.php                  Katalog produk publik dilengkapi pencarian nama, filter kategori, urutan harga, dan paginasi.
├── produk-detail.php           Halaman rincian 1 produk dan formulir pemesanan langsung tanpa keranjang.
├── pesanan-sukses.php          Halaman sukses pemesanan berisi rincian pesanan, rekening transfer, dan tautan WhatsApp.
├── program.php                 Daftar program pemberdayaan sosial aktif dan selesai beserta progres dana real-time.
├── program-detail.php          Cerita lengkap program, capaian dana terkumpul, dan daftar donatur terverifikasi.
├── donasi.php                  Formulir donasi untuk program sosial aktif dengan opsi nominal cepat dan anonimitas.
├── donasi-sukses.php           Instruksi transfer rekening resmi dan tombol konfirmasi donasi via WhatsApp.
├── transparansi.php            Akuntabilitas dana terpisah (penjualan vs donasi), diagram alokasi, dan laporan berkala.
├── kontak.php                  Halaman kontak resmi sekretariat, nomor WhatsApp, alamat kantor, dan surel.
│
├── admin/
│   ├── index.php               Dashboard admin menampilkan ringkasan penjualan, donasi, antrean transaksi, dan aksi cepat.
│   ├── setup.php               Formulir inisialisasi akun admin pertama (otomatis 404 setelah ada admin).
│   ├── login.php               Halaman masuk administrator dengan proteksi brute force, CSRF, dan noindex.
│   ├── logout.php              Proses keluar admin yang aman melalui metode POST dan validasi token CSRF.
│   ├── konten.php              Pengelolaan konten narasi, foto hero/tentang, dan logo mitra/instansi halaman publik.
│   ├── tes-koneksi.php         Diagnosa dan uji koneksi REST cURL, service account, dan OAuth2 token ke Firebase RTDB.
│   ├── produk.php              Manajemen tabel produk (pencarian, filter status, tombol ubah status dan hapus).
│   ├── produk-form.php         Formulir tambah dan edit produk beserta unggah foto dan validasi input.
│   ├── produk-aksi.php         Pemrosesan simpan, perbarui status, dan hapus produk (terproteksi relasi pesanan).
│   ├── program.php             Manajemen tabel program sosial kemasyarakatan.
│   ├── program-form.php        Formulir tambah dan edit program sosial beserta target dana dan foto sampul.
│   ├── program-aksi.php        Pemrosesan backend program sosial (terproteksi relasi catatan donasi).
│   ├── donasi.php              Daftar seluruh transaksi donasi masyarakat dengan filter status dan informasi program.
│   ├── donasi-aksi.php         Pemrosesan perubahan status donasi (menunggu, berhasil, gagal) tanpa opsi hapus data.
│   ├── pesanan.php             Daftar pesanan produk dari pembeli dilengkapi filter status dan riwayat item.
│   ├── pesanan-aksi.php        State machine status pesanan dan pemotongan/pengembalian stok bersyarat (ETag).
│   ├── laporan.php             Daftar publikasi dokumen laporan transparansi berkala.
│   ├── laporan-form.php        Formulir unggah berkas PDF/laporan pertanggungjawaban kegiatan.
│   ├── laporan-aksi.php        Pemrosesan simpan dan hapus berkas laporan fisik dari server.
│   └── pengaturan.php          Kelola identitas situs, rekening bank, alokasi dana, kontak, dan catatan transparansi.
│
├── includes/
│   ├── .htaccess               Blokir total seluruh akses peramban langsung ke folder konfigurasi dan logika internal.
│   ├── config.sample.php       Templat konfigurasi Firebase RTDB dan konstanta lingkungan aplikasi.
│   ├── config.php              Konfigurasi aktif lokal/produksi (rahasia, diabaikan oleh git).
│   ├── firebase.php            Klien REST API Firebase RTDB murni PHP via cURL & OpenSSL (OAuth2 JWT RS256 & ETag locking).
│   ├── data.php                Lapisan abstraksi data bahasa Indonesia (/admin, /produk, /program, /pesanan, /donasi, /laporan, /pengaturan).
│   ├── auth.php                Manajemen sesi aman (cookie HttpOnly, SameSite=Lax, masa aktif 2 jam) dan fungsi wajib_login().
│   ├── fungsi.php              Pustaka fungsi pembantu (e(), rupiah(), url(), alihkan(), token_csrf(), cek_csrf(), unggah_berkas(), dll).
│   ├── header.php              Kepala HTML publik semantik, Google Fonts Poppins, metadata Open Graph, dan navigasi utama.
│   ├── footer.php              Kaki halaman publik semantik, navigasi sekunder, kontak, dan penanda data contoh.
│   ├── admin-header.php        Kepala panel admin, pengecekan otentikasi wajib, sidebar menu responsif, dan pesan kilas.
│   └── admin-footer.php        Kaki halaman panel admin dan penutup layout konten.
│
├── assets/
│   ├── css/
│   │   ├── style.css           Lembar gaya utama seluruh halaman publik (variabel warna, tipografi, kartu, form, aksesibilitas).
│   │   └── admin.css           Lembar gaya khusus panel admin (sidebar, topbar, kartu ringkasan, tabel data, badge status).
│   ├── js/
│   │   ├── app.js              Skrip interaksi publik (toggle menu navigasi mobile, tombol nominal cepat donasi).
│   │   └── admin.js            Skrip panel admin (toggle sidebar mobile, toggle lihat kata sandi, konfirmasi hapus data).
│   └── img/
│       ├── logo.png            Logo mitra resmi.
│       └── logo-instansi.png   Logo instansi pendamping.
│
├── database/
│   ├── .htaccess               Blokir total seluruh akses langsung ke folder database dari browser.
│   ├── rules.json              Rules keamanan Firebase RTDB terkunci (.read: false, .write: false).
│   ├── seed.json               Data awalan contoh yang seluruhnya bertanda is_contoh = 1.
│   └── STRUKTUR.md             Dokumentasi hierarki pohon data dan contoh struktur record tiap koleksi.
│
└── uploads/
    ├── .htaccess               Pencegah eksekusi script berbahaya (PHP engine off) dan penonaktifan directory browsing.
    └── .gitkeep                Penjaga keberadaan direktori penyimpanan berkas di sistem git.
```

---

## 5. Aturan Bisnis & Mekanisme ETag (Optimistic Locking)

1. **Pembaruan Stok Bersyarat**:
   - Pemotongan stok saat pesanan berstatus `dibayar` dan pengembalian stok saat pesanan dibatalkan (`batal` setelah `dibayar`) menggunakan mekanisme **ETag HTTP (Optimistic Concurrency Control)**.
   - Server PHP mengirimkan header `If-Match: <etag>`. Bila data produk telah berubah di RTDB pada saat bersamaan, Firebase mengembalikan HTTP status 412 (Precondition Failed), dan server PHP akan mengulang proses secara otomatis hingga maksimal 5 kali.
2. **Pemisahan Dana Komersial & Donasi**:
   - Pesanan produk (`/pesanan`) dan donasi program (`/donasi`) disimpan pada node terpisah dan dilaporkan secara terpisah, tidak pernah digabungkan.
3. **Penghitungan Progres Donasi**:
   - Progres capaian dana program dihitung secara dinamis dari seluruh donasi yang berstatus `berhasil`.
4. **Proteksi Penghapusan Data**:
   - Produk yang pernah memiliki pesanan dan program yang pernah menerima donasi tidak dapat dihapus dari sistem (harus diarsipkan/ditutup).
