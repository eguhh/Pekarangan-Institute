# P6 - Halaman Publik Bebas Login, Admin Tersembunyi, Kelola Konten

Prasyarat: P5 selesai dan diuji. Tahap ini hanya soal akses dan pengelolaan konten, bukan perapian visual (itu P7).

## A. Halaman publik tanpa login
1. Hapus `login.php` di folder root. Admin hanya masuk lewat `admin/login.php`.
2. Hapus tombol/tautan "Login Admin" dan blok "Kelola Halaman Web" untuk tamu dari `includes/header.php` (sidebar dan mobile) dan `includes/footer.php`. Blok "Admin Aktif + Panel Admin + Keluar" hanya muncul bila sesi admin sedang aktif.
3. Pengunjung biasa tidak pernah dialihkan ke login, dan tidak ada teks yang menyebut admin, akun demo, atau kredensial di halaman publik.
4. Cari di seluruh project: teks "demo", email placeholder `admin@pekarangan.org`, password bawaan, komentar yang membocorkan jalur admin. Hapus atau netralkan (placeholder email jadi `nama@email.com`).
5. `admin/login.php`: tambah `<meta name="robots" content="noindex,nofollow">`. Buat `robots.txt` dengan `Disallow: /admin/`. Login tetap: pesan error umum, batas 5 gagal per 60 detik, CSRF, anti open-redirect.

## B. Halaman Konten Halaman (`admin/konten.php`)
Satu halaman dengan kolom isian tetap, disimpan di `/pengaturan` RTDB (atau `/konten`), dengan teks bawaan di kode sebagai cadangan agar situs tidak pernah kosong.
Bagian yang bisa diedit:
- Beranda: judul dan teks hero, foto hero (unggah), tiga langkah kerja (judul + teks).
- Tentang: deskripsi, tiga pilar (judul + teks), foto.
- Footer: teks singkat.
- Logo mitra dan logo instansi (unggah, PNG/SVG/WebP, maks 2 MB).
- Kontak, rekening, alokasi dana tetap di `admin/pengaturan.php`.
Aturan: tombol "Kembalikan ke bawaan" per bagian, pratinjau gambar sebelum simpan, validasi MIME dengan `finfo`, nama file acak, simpan di `uploads/`.

## C. Barang dan foto
Pastikan di `admin/produk*.php`: tambah, ubah, ubah status (draf/tampil/arsip), hapus (hanya bila belum pernah dipesan), unggah/ganti/hapus foto (file lama ikut terhapus saat diganti). Tambahkan tombol cepat +/- untuk stok di daftar produk (memakai pembaruan bersyarat dari P5, tercatat bahwa ini penyesuaian stok manual).
Program dan laporan: unggah foto sampul dan berkas PDF dengan aturan yang sama.

## Akhir sesi
`php -l`. Beri daftar uji manual: buka situs tanpa login (tidak ada tautan login), buka `/admin/login.php`, login, edit hero, ganti foto produk, tambah dan arsipkan produk, logout. Sebutkan yang belum teruji. BERHENTI.
