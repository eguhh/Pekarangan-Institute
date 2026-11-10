# AGENTS.md - Pekarangan Institute (Versi Sederhana: HTML, PHP, JS + Firebase RTDB)

Baca file ini di awal setiap sesi. Aturan di sini mengalahkan instruksi lain kecuali pengguna mengubahnya secara eksplisit (catat di DECISIONS.md).

## 1. Konteks
Pekarangan Institute adalah inisiatif kewirausahaan sosial. Konsep: **Berbelanja, Berbagi, Berdampak.**
Website ini: profil singkat, katalog produk, donasi untuk program sosial, transparansi dana, dan admin sederhana.
Ini **prototype untuk didiskusikan dengan mitra** dan akan berubah. Prioritas utama: **SEDERHANA** (untuk pengguna, admin, dan kode). Jika ragu antara fitur lengkap dan sederhana, pilih sederhana.

## 2. Aturan emas: jangan mengarang data
- Dilarang membuat statistik, total donasi/penjualan, jumlah penerima manfaat, lokasi, pencapaian, testimoni, atau nama orang sebagai fakta.
- Data contoh wajib bertanda: kolom/properti `is_contoh = 1` di database dan label **"DATA CONTOH"** (`<span class="lencana-contoh">`) di tampilan. Teks placeholder: `[Nama Produk]`, `Rp X.XXX.XXX`, `[Foto]`, nomor `08xx-xxxx-xxxx`.
- Dilarang memakai foto/nama penerima manfaat (terutama anak). Tidak ada field untuk itu.
- Jangan membuat fitur yang tampak jalan tetapi tanpa logika. Pembayaran: transfer manual, tampilkan jelas "pembayaran dikonfirmasi admin".

## 3. Teknologi (BATASAN KERAS)
- **PHP 8+ polos** (gaya prosedural), **Firebase RTDB lewat REST (cURL) + service account**, **HTML5**, **CSS satu file**, **JavaScript biasa** (vanilla).
- DILARANG: framework PHP (Laravel dll.), Composer, Node.js/npm, bundler, Tailwind, Bootstrap, React/Vue, jQuery, ORM, library pihak ketiga. Satu-satunya sumber luar: Google Fonts (link biasa).
- Tanpa routing khusus dan tanpa pretty URL: pakai file langsung dan query string (`produk-detail.php?slug=kopi-contoh`).
- Tanpa OOP kustom, namespace, autoloader. Fungsi biasa saja.

## 4. Bahasa
UI, nama file, tabel/koleksi, properti, variabel, fungsi, dan komentar: **Bahasa Indonesia** (supaya mudah dibaca, dijelaskan, dan dibuat flowchart-nya). Komentar singkat, menjelaskan "kenapa", bukan mengulang kode.

## 5. Struktur folder (TETAP, jangan menambah folder tanpa alasan)
```
pekarangan/
├── index.php              Beranda
├── tentang.php
├── produk.php             Daftar produk
├── produk-detail.php      Detail + form pesan
├── pesanan-sukses.php
├── program.php            Daftar program
├── program-detail.php
├── donasi.php             Form donasi
├── donasi-sukses.php
├── transparansi.php
├── kontak.php
├── robots.txt
├── admin/
│   ├── setup.php          Buat admin pertama (nonaktif otomatis setelah ada admin)
│   ├── login.php  logout.php  index.php (dashboard)
│   ├── konten.php         Edit teks/konten halaman publik (bukan page builder)
│   ├── tes-koneksi.php    Uji koneksi cURL & service account ke RTDB
│   ├── produk.php  produk-form.php  produk-aksi.php
│   ├── program.php program-form.php program-aksi.php
│   ├── donasi.php  donasi-aksi.php
│   ├── pesanan.php pesanan-aksi.php
│   ├── laporan.php laporan-form.php laporan-aksi.php
│   └── pengaturan.php
├── includes/              config.sample.php, config.php (tidak di-commit),
│                          firebase.php, data.php, fungsi.php, auth.php,
│                          header.php, footer.php, admin-header.php, admin-footer.php
├── assets/ css/style.css, css/admin.css, js/app.js, js/admin.js, img/
├── uploads/               (foto & berkas unggahan)
├── database/              seed.json, rules.json, STRUKTUR.md
└── README.md
```

## 6. Aturan kode sederhana
- Satu halaman = satu file: logika di bagian atas, HTML di bawah. Tidak ada template engine.
- Pengulangan (header, footer, fungsi umum) ada di `includes/`. Tidak ada abstraksi lain.
- Fungsi pendek (maks sekitar 30 baris), nama jelas. Helper ada di `fungsi.php`, operasi data RTDB ada di `data.php` dan `firebase.php`.
- Aksi tulis (tambah/ubah/hapus) memakai POST ke file `*-aksi.php`, lalu redirect (pola Post-Redirect-Get) dengan pesan flash.
- JS hanya untuk interaksi kecil (menu mobile, tampil/sembunyi password, pratinjau gambar, konfirmasi hapus, tombol nominal donasi). Semua halaman tetap berfungsi tanpa JS bila memungkinkan.

## 7. Desain UI (ramah, bersih, biru-putih sesuai logo mitra)
- Mobile-first, satu kolom di HP, grid 2-3 kolom di layar besar. Lebar konten maks 1080px.
- Warna (CSS variable di `style.css`): `--biru:#0049AB` (logo), `--biru-tua:#003580`, `--biru-muda:#EAF1FB`, `--teks:#1B2430`, `--teks-redup:#5B6675`, `--latar:#F6F8FB`, `--putih:#FFFFFF`, `--garis:#E2E8F0`, status badge admin `--hijau:#1E9E5A`, `--kuning:#E0A100`, `--merah:#D64545`. Oranye dihapus (tombol donasi dan progres memakai biru). Pastikan kontras teks memenuhi WCAG AA.
- Huruf: **Poppins** (400/500/600/700) via Google Fonts, satu keluarga saja.
- Bentuk: sudut membulat 14px, bayangan lembut, tombol tinggi min 48px, jarak lega, ikon berupa SVG inline sederhana atau emoji (tanpa library ikon).
- Tombol: utama = biru solid, sekunder = garis biru, donasi = biru solid.
- Dua logo: logo mitra (`assets/img/logo.png`) dan logo instansi (`assets/img/logo-instansi.png`).
- Foto: bila belum ada, tampilkan kotak abu-biru bertuliskan "[Foto]" (class `.foto-kosong`) dengan rasio tetap.
- Animasi hanya: transisi hover/fokus dan animasi lebar progress bar. Hormati `prefers-reduced-motion`.
- Aksesibilitas: HTML semantik, urutan heading benar, label pada form, fokus terlihat, `alt` pada gambar, pesan error jelas.

## 8. Cakupan fitur (batas)
**Termasuk:** Beranda, Tentang, Produk (daftar, detail, pesan 1 produk), Program (daftar, detail), Donasi, Transparansi, Kontak, serta Admin (login, dashboard, CRUD produk/program/laporan, kelola donasi dan pesanan, pengaturan, satu halaman `admin/konten.php` untuk edit teks/gambar halaman publik sesuai Keputusan 9).
**DILARANG dibangun:** keranjang belanja, checkout banyak item, payment gateway, ongkir otomatis, registrasi publik, komentar/ulasan, multi-bahasa, galeri dokumentasi, halaman dampak/Desa Pekarangan, CMS rumit atau page builder visual, notifikasi email.

## 9. Aturan bisnis
- Pesanan (`pesanan`) dan donasi (`donasi`) berada di **koleksi terpisah** dan dilaporkan **terpisah**; tidak pernah dijumlahkan menjadi satu angka.
- Harga selalu diambil dari database/RTDB, bukan dari form.
- Progres program = jumlah donasi berstatus `berhasil` saja (dihitung saat ditampilkan, tidak disimpan).
- Stok berkurang tepat sekali saat pesanan menjadi `dibayar`; kembali saat dibatalkan setelah `dibayar`. Gunakan pembaruan bersyarat RTDB (ETag) dengan percobaan ulang pada record produk.
- Kode: pesanan `ORD-YYYYMMDD-XXXX`, donasi `DON-YYYYMMDD-XXXX` (XXXX = 4 karakter acak huruf besar/angka, unik). Cek keunikan sebelum simpan.
- Status pesanan: menunggu, dibayar, diproses, dikirim, selesai, batal. Donasi: menunggu, berhasil, gagal. Hanya transisi yang masuk akal.
- Produk yang pernah dipesan dan program yang punya donasi tidak boleh dihapus (arsipkan/tutup). Pesanan dan donasi tidak dihapus.
- Alokasi dana (mis. operasional vs sosial) disimpan di `pengaturan`, bukan angka tetap di kode.

## 10. Keamanan (wajib)
- Validasi tiap input, kunci RTDB hanya dari id yang dibuat server, tidak pernah dari input mentah. Dilarang menyusun path atau query dari input yang tidak divalidasi.
- Rules RTDB dikunci total (`.read: false, .write: false`). Semua akses lewat server PHP via REST API + service account. Browser tidak memuat kredensial Firebase.
- Semua output ke HTML melewati `e()` (htmlspecialchars, UTF-8).
- Password: `password_hash()` / `password_verify()`. Tidak ada password bawaan di repo.
- Semua form POST memakai **token CSRF**. Semua halaman admin memanggil `wajib_login()` di baris paling atas, termasuk file `*-aksi.php`.
- Sesi: `session_regenerate_id(true)` saat login, cookie `HttpOnly` dan `SameSite=Lax`, kedaluwarsa wajar (mis. 2 jam tidak aktif).
- Login: pesan error umum "Email atau password salah."; batasi percobaan (5 gagal -> tunggu 60 detik).
- Unggah berkas: validasi ukuran, ekstensi, dan tipe MIME (`finfo`), nama file acak, simpan di `uploads/`; folder itu tidak boleh mengeksekusi PHP (`.htaccess`).
- Form publik (pesan, donasi): validasi server, batas panjang, field jebakan (honeypot) sederhana.
- `includes/config.php` tidak di-commit (`.gitignore`). `includes/` dan `database/` tidak boleh diakses langsung dari browser (`.htaccess` Require all denied).
- `display_errors` mati kecuali `APP_ENV = 'lokal'`.
- Validasi dan batasi `?next=`/redirect agar hanya ke halaman internal.

## 11. Cara bekerja
- **Satu prompt per sesi.** Jangan mengerjakan tahap berikutnya.
- Sebelum coding: lihat isi folder, tulis rencana singkat (maks 10 baris).
- Di akhir: jalankan `php -l` pada semua file PHP jika PHP tersedia; jika tidak tersedia atau Firebase belum terhubung, **katakan terus terang** bahwa pengujian koneksi belum dilakukan dan sebutkan apa yang perlu saya cek manual. Jangan mengklaim sudah diuji bila belum.
- Beri ringkasan: apa yang dibuat, apa yang perlu saya lakukan (isi config, tempatkan service account JSON), hal yang masih contoh, dan pertanyaan untuk mitra. Lalu **BERHENTI dan tunggu review**.
- Masukan mitra dicatat dulu di `FEEDBACK.md`, dikerjakan per batch. Keputusan yang disepakati ada di `DECISIONS.md` dan tidak boleh diubah tanpa perintah eksplisit.

## 12. Catatan sebelum go-live (bukan untuk prototype)
Konfirmasi ke mitra: hosting IdWebHost (pekarangan.web.id) dan HTTPS (diselesaikan di P8), izin pengumpulan donasi, kebijakan privasi dan syarat layanan (data pribadi pemesan/donatur), consent foto penerima manfaat.
