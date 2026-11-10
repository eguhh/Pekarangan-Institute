# STRUKTUR.md - Pohon Struktur Data Firebase Realtime Database

Dokumen ini mendeskripsikan arsitektur dan struktur koleksi node data pada Firebase Realtime Database (RTDB) Pekarangan Institute.

## 1. Pohon Data (Data Tree Overview)

```
/ (root)
├── admin/
│   └── {admin_id}/
├── produk/
│   └── {produk_id}/
├── program/
│   └── {program_id}/
├── pesanan/
│   └── {pesanan_id}/
├── donasi/
│   └── {donasi_id}/
├── laporan/
│   └── {laporan_id}/
├── pengaturan/
│   └── {kunci}: string
└── _tes/
    └── {waktu}: string (hanya digunakan sesaat oleh admin/tes-koneksi.php)
```

Seluruh akses database dikunci total lewat `database/rules.json`:
```json
{
  "rules": {
    ".read": false,
    ".write": false
  }
}
```
Akses baca dan tulis hanya dilakukan oleh server backend PHP menggunakan **Service Account Google (OAuth2 Access Token)** melalui antarmuka REST API.

---

## 2. Contoh Record per Koleksi

### A. `/admin/{admin_id}`
Menyimpan akun pengelola/administrator sistem.
```json
{
  "id": "-OAdmin123XYZ",
  "nama": "Pengelola Pekarangan",
  "email": "admin@pekarangan.org",
  "password_hash": "$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
  "dibuat_pada": "2026-01-10 10:00:00"
}
```

### B. `/produk/{produk_id}`
Katalog produk dari kelompok tani / mitra binaan.
```json
{
  "id": "prod_1",
  "nama": "[Nama Produk] 1",
  "slug": "nama-produk-1",
  "kategori": "Bahan Pangan",
  "harga": 25000,
  "stok": 20,
  "deskripsi_singkat": "[Deskripsi Singkat Produk] 1",
  "deskripsi": "[Deskripsi Lengkap Produk] Produk contoh bahan pangan lokal berkualitas hasil panen kelompok mitra.",
  "penjual": "[Kelompok Tani Contoh] A",
  "foto": null,
  "status": "tampil",
  "is_contoh": 1,
  "dibuat_pada": "2026-01-10 10:00:00",
  "diubah_pada": "2026-01-10 10:00:00"
}
```

### C. `/program/{program_id}`
Program sosial dan inisiatif kemasyarakatan.
```json
{
  "id": "prog_1",
  "judul": "[Judul Program] 1",
  "slug": "judul-program-1",
  "ringkasan": "[Ringkasan Program] 1 - Program pemberdayaan pekarangan produktif untuk ketahanan pangan keluarga.",
  "cerita": "[Cerita Program] 1 - Program ini bertujuan mendampingi warga dalam mengoptimalkan lahan pekarangan rumah tangga menjadi sumber pangan mandiri bergizi seimbang dan bernilai ekonomi.",
  "target_dana": 10000000,
  "tenggat": "2026-12-31",
  "lokasi": "[Kabupaten/Kota Contoh] 1",
  "penerima_manfaat": "[Kelompok Warga Komunitas Contoh] 1",
  "foto_sampul": null,
  "status": "aktif",
  "is_contoh": 1,
  "dibuat_pada": "2026-01-10 10:00:00",
  "diubah_pada": "2026-01-10 10:00:00"
}
```

### D. `/pesanan/{pesanan_id}`
Pemesanan produk (terpisah dari donasi).
```json
{
  "id": "ord_1",
  "kode": "ORD-20260112-X1Y2",
  "produk_id": "prod_1",
  "nama_produk": "[Nama Produk] 1",
  "harga_satuan": 25000,
  "jumlah": 2,
  "total": 50000,
  "nama_pemesan": "[Nama Pemesan] 1",
  "whatsapp": "08xx-xxxx-xxxx",
  "alamat": "[Alamat Pengiriman Contoh] Jl. Merpati No. 5, Kota Contoh",
  "catatan": "Tolong dikemas rapi.",
  "status": "dibayar",
  "is_contoh": 1,
  "dibuat_pada": "2026-01-12 08:30:00",
  "diubah_pada": "2026-01-12 09:00:00"
}
```

### E. `/donasi/{donasi_id}`
Donasi untuk program sosial (terpisah dari pesanan).
```json
{
  "id": "don_1",
  "kode": "DON-20260110-A1B2",
  "program_id": "prog_1",
  "nama_donatur": "[Nama Donatur] 1",
  "whatsapp": "08xx-xxxx-xxxx",
  "jumlah": 500000,
  "anonim": 0,
  "pesan": "Semoga program ini berkah dan bermanfaat bagi warga.",
  "status": "berhasil",
  "is_contoh": 1,
  "dibuat_pada": "2026-01-10 11:30:00",
  "diubah_pada": "2026-01-10 11:30:00"
}
```

### F. `/laporan/{laporan_id}`
Laporan berkala transparansi penyaluran dana.
```json
{
  "id": "lap_1",
  "judul": "[Judul Laporan] Laporan Transparansi Q1 2026",
  "periode": "Januari - Maret 2026",
  "ringkasan": "[Ringkasan Laporan Contoh] Rekapitulasi penyaluran hasil penjualan produk dan donasi masyarakat untuk kegiatan pemberdayaan komunitas pekarangan.",
  "berkas": "laporan-contoh-q1-2026.pdf",
  "status": "tampil",
  "is_contoh": 1,
  "dibuat_pada": "2026-01-10 10:00:00"
}
```

### G. `/pengaturan/{kunci}`
Konfigurasi teks, kontak, dan alokasi transparansi.
```json
{
  "nama_situs": "Pekarangan Institute",
  "tagline": "Berbelanja, Berbagi, Berdampak",
  "hero_judul": "Berbelanja, Berbagi, Berdampak.",
  "hero_teks": "Inisiatif kewirausahaan sosial yang menghubungkan produk hasil pekarangan dengan pemberdayaan dan program sosial kemasyarakatan.",
  "tentang_teks": "Pekarangan Institute adalah inisiatif kewirausahaan sosial yang bergerak di bidang pemberdayaan pekarangan produktif dan penguatan ekonomi komunitas.",
  "whatsapp": "08xx-xxxx-xxxx",
  "email": "kontak@pekarangan.org",
  "alamat": "[Alamat Kantor/Sekretariat Contoh] Jl. Contoh Pekarangan No. 12",
  "info_pembayaran": "[Nama Bank] [Nomor Rekening] a.n. [Nama Rekening]",
  "donasi_minimum": "10000",
  "alokasi_operasional": "30",
  "alokasi_sosial": "70",
  "catatan_transparansi": "Alokasi dana operasional (30%) dan dana sosial (70%) di atas adalah angka contoh untuk simulasi transparansi dana."
}
```

---

## 3. Aturan Tanggal dan Zona Waktu
Semua field tanggal `dibuat_pada` dan `diubah_pada` disimpan dalam format standar MySQL: `YYYY-MM-DD HH:MM:SS` pada zona waktu `Asia/Jakarta`.
