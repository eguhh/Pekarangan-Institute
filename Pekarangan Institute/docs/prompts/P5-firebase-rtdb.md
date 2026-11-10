# P5 - Migrasi MySQL ke Firebase Realtime Database

Tujuan: seluruh data (admin, produk, program, pesanan, donasi, laporan, pengaturan) disimpan di Firebase RTDB. Tampilan TIDAK diubah di tahap ini.

## Sebelum coding
1. Baca isi folder, lalu tulis rencana maks 10 baris.
2. Perbarui AGENTS.md sesuai bagian 3 di `00-REVISI-PANDUAN.md`. Tambahkan keputusan 5-12 ke DECISIONS.md dan masukan 1-6 ke FEEDBACK.md.
3. Jangan mengarang data. Data contoh tetap bertanda `is_contoh = 1` dan label DATA CONTOH.

## Arsitektur (wajib)
- PHP memanggil RTDB lewat REST (`https://<project>-default-rtdb.<region>.firebasedatabase.app/<path>.json`) memakai cURL. Tanpa Composer, tanpa SDK.
- Autentikasi: service account. Buat JWT RS256 dengan `openssl_sign()`, tukar jadi access token di `https://oauth2.googleapis.com/token`, simpan token di berkas cache sampai hampir kedaluwarsa. Cache disimpan di folder yang tidak bisa diakses browser.
- Rules RTDB dikunci total. Buat `database/rules.json`:
  ```json
  { "rules": { ".read": false, ".write": false } }
  ```
  Service account tetap bisa baca/tulis karena melewati rules. Browser tidak boleh memuat konfigurasi Firebase apa pun (tidak ada JS Firebase di halaman).
- Lokasi kunci service account: di luar folder web (mis. satu tingkat di atas `public_html`). `config.php` hanya menyimpan path-nya. Jika terpaksa di dalam project, folder itu wajib `.htaccess` `Require all denied` dan masuk `.gitignore`.

## Yang dikerjakan
1. `includes/firebase.php`: fungsi `fb_token()`, `fb_ambil($jalur)`, `fb_simpan($jalur, $data)`, `fb_ubah($jalur, $data)` (PATCH), `fb_tambah($jalur, $data)` (POST, id otomatis), `fb_hapus($jalur)`, serta `fb_ambil_etag($jalur)` dan `fb_simpan_jika_etag($jalur, $data, $etag)` (header `If-Match`, balasan 412 = berubah, ulangi maks 5 kali). Timeout cURL jelas, error dicatat ke log, pesan ke pengunjung umum saja ("Data belum bisa dimuat").
2. `includes/data.php`: fungsi tingkat atas berbahasa Indonesia yang dipakai halaman, mis. `produk_semua()`, `produk_ambil($id)`, `produk_by_slug($slug)`, `program_ambil(...)`, `donasi_total_program($id)`, `pesanan_buat(...)`, `pengaturan_ambil($kunci)`. Halaman tidak boleh memanggil `fb_*` langsung.
3. Struktur data (kunci = id string dari `fb_tambah`):
   `/admin`, `/produk`, `/program`, `/pesanan`, `/donasi`, `/laporan`, `/pengaturan/{kunci}`.
   Isi tiap record sama dengan kolom di `schema.sql` lama (nama kolom tetap Indonesia), termasuk `is_contoh`, `dibuat_pada`, `diubah_pada` (format `Y-m-d H:i:s`, zona Asia/Jakarta).
4. Data kecil: ambil satu koleksi lalu saring/urutkan/paginasi di PHP. Tidak perlu `indexOn` kecuali agent menemukan alasan kuat.
5. Port SEMUA file publik dan `admin/*` dari PDO ke `data.php`. Hapus `includes/db.php`.
6. Aturan bisnis yang harus tetap berlaku (AGENTS.md bagian 9):
   - Stok berkurang tepat sekali saat pesanan menjadi `dibayar`, kembali saat batal setelah `dibayar`. Gunakan pembaruan bersyarat ETag pada record produk (bukan baca-lalu-tulis biasa).
   - Kode pesanan/donasi unik (`ORD-YYYYMMDD-XXXX`, `DON-YYYYMMDD-XXXX`). Cek keunikan sebelum simpan.
   - Harga selalu dari database. Progres program = donasi `berhasil` saja, dihitung saat tampil.
   - Produk yang pernah dipesan dan program yang punya donasi tidak boleh dihapus (cek dengan memindai koleksi pesanan/donasi).
7. `admin/setup.php`: buat admin pertama di `/admin` RTDB (`password_hash`). Hapus tombol/blok "reset akun demo" dan semua teks demo. Setelah ada admin, halaman menjadi 404. Tombol "isi data contoh" boleh ada hanya saat `APP_ENV = 'lokal'`, memuat `database/seed.json` (semua `is_contoh = 1`).
8. Ganti `config.sample.php`: hapus `DB_*`, tambah `FIREBASE_DB_URL` dan `FIREBASE_KUNCI_PATH`. Perbarui `.gitignore`. `config.php` yang ada sekarang (root, tanpa password) hanya untuk lokal dan tidak boleh ikut diunggah.
9. `admin/tes-koneksi.php` (wajib_login): cek token, tulis dan baca satu node uji `/_tes`, lalu hapus node itu. Tampilkan hasil sukses/gagal beserta saran perbaikan (cURL/OpenSSL aktif? URL benar? kunci terbaca?). Tidak menampilkan isi kunci.
10. Ganti `schema.sql` dan `seed.sql` dengan `database/seed.json`, `database/rules.json`, dan `database/STRUKTUR.md` (pohon data + contoh satu record per koleksi).
11. Perbarui README: persyaratan (PHP 8+, ekstensi `curl`, `openssl`, `fileinfo`, `mbstring`), cara membuat project Firebase dan service account, cara mengisi `config.php`, dan struktur folder baru.

## Akhir sesi
Jalankan `php -l` pada semua file PHP jika PHP tersedia. Koneksi ke Firebase TIDAK bisa agent uji tanpa kunci saya. Katakan terus terang bagian mana belum teruji dan beri daftar langkah uji manual (buka `admin/tes-koneksi.php`, buat produk, pesan produk, ubah status ke `dibayar`, cek stok). Lalu BERHENTI.
