# P8 - Persiapan dan Uji Hosting di pekarangan.web.id (IdWebHost)

Prasyarat: P5-P7 selesai dan lolos uji lokal.

## Dikerjakan agent
1. `includes/config.sample.php` versi produksi: `BASE_URL = ''` (situs di akar domain), `APP_ENV = 'produksi'`, `FIREBASE_DB_URL`, `FIREBASE_KUNCI_PATH`.
2. `.htaccess` akar: paksa HTTPS dan satu bentuk domain (`https://pekarangan.web.id`), tetap blokir dotfile, `.sql`, `.json` (kecuali yang memang publik), `.md`, folder `includes/` dan `docs/`.
3. `robots.txt` (Disallow `/admin/`), pastikan `uploads/.htaccess` mematikan eksekusi PHP.
4. Periksa tidak ada URL absolut `localhost` atau `/pekarangan-v2` yang tertinggal (semua lewat `url()`).
5. Buat `docs/DEPLOY.md` berisi langkah di bawah, ditulis untuk pemula.
6. Buat daftar berkas yang BOLEH diunggah. TIDAK diunggah: `docs/`, `database/`, `.git*`, `config.php` lokal, `README.md`, file zip.

## Langkah untuk saya di hosting (agent hanya menuliskannya ke DEPLOY.md)
1. cPanel/panel hosting: pilih PHP 8.0+ dan aktifkan ekstensi `curl`, `openssl`, `fileinfo`, `mbstring`. Cek lewat berkas uji sementara bila perlu, lalu hapus.
2. Pastikan domain `pekarangan.web.id` mengarah ke folder situs (document root) dan nameserver/DNS sudah benar.
3. Aktifkan sertifikat SSL (AutoSSL/Let's Encrypt) dan pastikan `https://pekarangan.web.id` terbuka.
4. Unggah berkas (File Manager atau FTP) ke document root. Folder `uploads/` dapat ditulis oleh PHP (umumnya izin 755).
5. Unggah file kunci service account SATU tingkat di atas document root, lalu isi `config.php` produksi dengan path-nya.
6. Buka `/admin/setup.php` sekali, buat admin pertama dengan password kuat, lalu HAPUS `admin/setup.php` dari server.
7. Login di `/admin/login.php`, buka `admin/tes-koneksi.php`. Bila sukses, hapus berkas itu dari server.
8. Pasang rules dari `database/rules.json` di Firebase Console (Realtime Database > Rules) dan pastikan tidak ada aturan terbuka.
9. Isi data asli lewat admin (jangan memuat data contoh di produksi).

## Daftar uji penerimaan (jalankan di ponsel dan laptop)
- Situs terbuka tanpa login dan tanpa tautan/teks login atau kredensial di halaman publik.
- Semua halaman publik tampil, foto dan dua logo muncul, tidak ada mixed content (semua HTTPS).
- Admin: login, tambah/arsipkan barang, ubah stok, unggah foto, edit Konten Halaman, ubah status pesanan, logout.
- Pesan produk dan donasi dari sisi publik tercatat di admin.
- `https://pekarangan.web.id/includes/config.php`, `/database/`, `/docs/` memberi 403/404.
- Data masuk ke Firebase Console (node `/produk`, `/pesanan`, dst).

## Akhir sesi
Beri ringkasan, daftar yang belum teruji (agent tidak bisa mengakses hosting), dan pertanyaan untuk mitra (izin donasi, kebijakan privasi, consent foto). BERHENTI.
