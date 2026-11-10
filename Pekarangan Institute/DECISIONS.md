# DECISIONS.md - Keputusan yang Sudah Disepakati

Agen AI tidak boleh mengubah item di sini tanpa perintah eksplisit.

| # | Tanggal | Keputusan | Alasan |
|---|---------|-----------|--------|
| 1 | 2026-11-09 | Pesan produk satu per satu (tanpa keranjang), konfirmasi lewat WhatsApp | Sederhana untuk pembeli dan admin |
| 2 | 2026-11-09 | Pembayaran: transfer manual, dikonfirmasi admin | Belum ada payment gateway |
| 3 | 2026-11-09 | Pesanan dan donasi di tabel terpisah | Pemisahan dana komersial dan donasi |
| 4 | 2026-11-09 | Alokasi dana disimpan di pengaturan | Menunggu angka resmi mitra |
| 5 | 2026-11-09 | Database: Firebase RTDB, diakses dari PHP lewat REST API + service account. MySQL/PDO tidak dipakai lagi | Syarat mitra |
| 6 | 2026-11-09 | Tetap tanpa Composer dan tanpa SDK. Cukup cURL + OpenSSL bawaan PHP | Hosting bersama, sederhana |
| 7 | 2026-11-09 | Rules RTDB dikunci total (`.read` dan `.write` = false). Semua akses lewat server PHP | Browser tidak pernah menyentuh database |
| 8 | 2026-11-09 | Halaman publik tidak menampilkan tautan, tombol, atau kredensial login. Admin masuk lewat alamat langsung `/admin/login.php` | Syarat mitra |
| 9 | 2026-11-09 | Admin boleh mengedit teks dan gambar halaman publik lewat SATU halaman `admin/konten.php` (kolom isian tetap). Bukan page builder | Syarat mitra |
| 10 | 2026-11-09 | Foto dan berkas tetap di folder `uploads/` pada hosting (RTDB hanya menyimpan nama file) | RTDB bukan penyimpan file |
| 11 | 2026-11-09 | Palet biru dan putih sesuai logo mitra. Oranye dihapus (tombol donasi dan progres memakai biru). Hijau/kuning/merah hanya untuk badge status di admin | Permintaan mitra |
| 12 | 2026-11-09 | Dua logo: logo mitra dan logo instansi | Permintaan mitra |
| 13 | 2026-11-10 | Tata letak (layout) halaman publik dan admin tetap dipertahankan persis seperti sebelumnya (struktur hero, tiga langkah, produk, program, CTA, footer, navigasi sidebar kanan/topbar). Perubahan desain UI dari referensi CoachPro difokuskan murni pada aspek visual/tampilan: palet warna modern mint/teal (#0A6B75, #14999E, #EDF5F4, #0E383C), tombol bentuk pill penuh dengan gradien teal, kartu membulat 20px dengan bayangan lembut, progress bar gradien, serta cache-busting otomatis pada stylesheet | Perintah langsung pengguna: ubah tampilan UI-nya saja, tata letak tetap sama seperti sebelumnya |
| 14 | 2026-11-10 | Seluruh keterangan, lencana ('DATA CONTOH', 'SIMULASI', 'CONTOH'), dan teks disclaimer prototipe data contoh dihapus dari antarmuka publik dan admin karena konten sudah dapat dikelola penuh lewat panel admin. Pada halaman login admin (/admin/login.php), ditambahkan tombol dan alur 'Login sebagai admin baru' yang mengizinkan registrasi/setup akun admin baru secara langsung dan otomatis login ke dashboard admin. | Perintah eksplisit pengguna |
