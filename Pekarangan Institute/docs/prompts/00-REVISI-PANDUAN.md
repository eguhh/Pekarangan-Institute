# Paket Revisi Mitra: Hosting, Firebase RTDB, Halaman Publik/Admin, UI

Tambahan untuk project `pekarangan-v2` (PHP polos). Letakkan 5 file ini di `docs/prompts/`.
Satu prompt = satu percakapan baru di Antigravity. Cek di browser dan `git commit` sebelum lanjut.

## 1. Masukan mitra (salin ke FEEDBACK.md)

| # | Masukan | Jenis | Status |
|---|---------|-------|--------|
| 1 | Web akan di-hosting di domain pekarangan.web.id (IdWebHost) | Teknis | Baru |
| 2 | Database harus memakai Firebase Realtime Database (RTDB), bukan MySQL | Teknis | Baru |
| 3 | Setelah hosting, halaman user langsung tampil, tanpa kredensial/tautan login di halaman publik | Tampilan/Teknis | Baru |
| 4 | Halaman admin tetap ada: edit halaman user, tambah/kurangi barang, unggah foto, dll | Fitur | Baru |
| 5 | Perbaikan UI agar lebih natural dan rapi, tetap biru-putih sesuai logo mitra | Tampilan | Baru |
| 6 | Tambahkan logo instansi (di samping logo mitra) | Tampilan | Baru |

## 2. Keputusan baru (salin ke DECISIONS.md)

Aturan lama di AGENTS.md yang bertentangan dengan keputusan ini DIGANTI oleh keputusan ini. Ini perintah eksplisit pemilik project.

| # | Keputusan | Alasan |
|---|-----------|--------|
| 5 | Database: Firebase RTDB, diakses dari PHP lewat REST API + service account. MySQL/PDO tidak dipakai lagi | Syarat mitra |
| 6 | Tetap tanpa Composer dan tanpa SDK. Cukup cURL + OpenSSL bawaan PHP | Hosting bersama, sederhana |
| 7 | Rules RTDB dikunci total (`.read` dan `.write` = false). Semua akses lewat server PHP | Browser tidak pernah menyentuh database |
| 8 | Halaman publik tidak menampilkan tautan, tombol, atau kredensial login. Admin masuk lewat alamat langsung `/admin/login.php` | Syarat mitra |
| 9 | Admin boleh mengedit teks dan gambar halaman publik lewat SATU halaman `admin/konten.php` (kolom isian tetap). Bukan page builder | Syarat mitra |
| 10 | Foto dan berkas tetap di folder `uploads/` pada hosting (RTDB hanya menyimpan nama file) | RTDB bukan penyimpan file |
| 11 | Palet biru dan putih sesuai logo mitra. Oranye dihapus (tombol donasi dan progres memakai biru). Hijau/kuning/merah hanya untuk badge status di admin | Permintaan mitra |
| 12 | Dua logo: logo mitra dan logo instansi | Permintaan mitra |

## 3. Perubahan AGENTS.md (suruh agent mengerjakannya di awal P5)

- Bagian 3: ganti "MySQL/MariaDB lewat PDO" menjadi "Firebase RTDB lewat REST (cURL) + service account". Larangan Composer/library tetap.
- Bagian 5: tambah `includes/firebase.php`, `includes/data.php`, `admin/konten.php`, `admin/tes-koneksi.php`, `robots.txt`. Hapus `database/schema.sql` dan `seed.sql`, ganti `database/seed.json` dan `database/rules.json`.
- Bagian 7: ganti palet sesuai keputusan 11.
- Bagian 8: pengecualian larangan "CMS" untuk `admin/konten.php` (keputusan 9).
- Bagian 9: "transaksi database" menjadi "pembaruan bersyarat RTDB (ETag) dengan percobaan ulang".
- Bagian 10: "prepared statement" menjadi "validasi tiap input, kunci RTDB hanya dari id yang dibuat server, tidak pernah dari input mentah".
- Bagian 12: tambah hosting IdWebHost dan HTTPS (diselesaikan di P8).

## 4. Urutan

| Prompt | Isi |
|---|---|
| P5 | Migrasi MySQL ke Firebase RTDB |
| P6 | Halaman publik bebas login, admin tersembunyi, halaman Konten Halaman |
| P7 | Perapian UI (anti-slop), biru-putih, dua logo |
| P8 | Persiapan dan uji hosting di pekarangan.web.id |

Pesan pembuka tiap sesi (ganti nama file):
```
Baca AGENTS.md, FEEDBACK.md, DECISIONS.md, lalu kerjakan persis sesuai
docs/prompts/P5-firebase-rtdb.md.
Berhenti di akhir dan tunggu review saya.
```

## 5. Hal yang kamu siapkan sendiri

1. Project Firebase (console.firebase.google.com), aktifkan Realtime Database, lokasi Singapore (`asia-southeast1`).
2. Service account: Project settings > Service accounts > Generate new private key (file JSON).
3. File logo: `assets/img/logo.png` (mitra) dan `assets/img/logo-instansi.png`. PNG transparan.
4. JANGAN tempel isi file JSON service account ke chat Antigravity atau ke git. Taruh file itu manual (lihat P5).
