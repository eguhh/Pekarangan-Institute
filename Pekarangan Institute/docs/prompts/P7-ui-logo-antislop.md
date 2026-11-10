# P7 - Perapian UI (anti-slop), Biru-Putih, Dua Logo

Prasyarat: P5 dan P6 selesai. Gunakan skill anti-slop yang sudah terpasang. Fungsi dan data tidak boleh berubah di tahap ini. Jangan sentuh `data.php`, `firebase.php`, atau logika `*-aksi.php`.

## Arah desain
- Warna: biru logo mitra (`--biru:#0049AB`) + putih, dengan abu-biru sangat muda sebagai latar. Hapus oranye (`--oranye`, `--oranye-muda`). Tombol utama dan donasi = biru solid, sekunder = garis biru. Hijau/kuning/merah hanya untuk badge status di admin. Kontras WCAG AA.
- Natural dan rapi: satu keluarga huruf (Poppins), skala ukuran huruf dan jarak yang konsisten (variabel CSS), tata letak berbasis grid yang sejajar. Kurangi bayangan tebal, gradien, kartu bersarang, dan dekorasi yang tidak membawa informasi.
- Ikon: SVG inline bergaya seragam (tebal garis sama). Hindari emoji sebagai ikon.
- Teks: kalimat singkat dan jelas, tanpa klise pemasaran. Dilarang menambah statistik atau klaim baru (aturan emas AGENTS.md).
- Setiap halaman punya satu titik fokus (hero beranda, daftar produk, form donasi). Hierarki heading benar.
- Keadaan kosong, memuat, dan error di tiap daftar/form dibuat ramah dan konsisten.
- [PILIH SATU SEBELUM DIKIRIM] Navigasi publik: (A) tetap sidebar kanan seperti sekarang

## Logo
- File: `assets/img/logo.png` (mitra) dan `assets/img/logo-instansi.png`. Tampilkan keduanya berdampingan dengan pemisah tipis di header publik, di kaki halaman (label "Bekerja sama dengan"), di halaman login admin, dan di sidebar admin. Tinggi seragam, proporsi dijaga, `alt` jelas.
- Logo bisa diganti lewat `admin/konten.php` (P6). Bila berkas belum ada, jatuh ke teks nama situs tanpa error.
- Tambahkan favicon dari logo mitra dan `og:image` di metadata.

## Cakupan berkas
`assets/css/style.css`, `assets/css/admin.css`, `includes/header.php`, `includes/footer.php`, `includes/admin-header.php`, tampilan HTML di tiap halaman. Hapus gaya `style=""` inline yang tersisa dan pindahkan ke CSS.

## Pengecekan
Tinjau di lebar 360, 768, dan 1280 px. Fokus keyboard terlihat, `prefers-reduced-motion` dihormati, tidak ada scroll horizontal. Jelaskan di ringkasan apa yang diubah per halaman dan mengapa. Bila tidak bisa membuka browser, katakan terus terang bahwa pemeriksaan visual belum dilakukan. BERHENTI.
