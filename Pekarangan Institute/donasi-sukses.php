<?php
/**
 * Halaman Donasi Sukses - Pekarangan Institute
 * Menampilkan kode referensi donasi DON, program yang didukung, nominal donasi,
 * informasi rekening pembayaran, banner konfirmasi admin, dan tombol konfirmasi WhatsApp.
 */

require_once __DIR__ . '/includes/fungsi.php';

$kode = trim($_GET['kode'] ?? '');

$donasi = null;
if ($kode !== '') {
    $donasi = donasi_by_kode($kode);
    if ($donasi) {
        $prog = !empty($donasi['program_id']) ? program_ambil((string)$donasi['program_id']) : null;
        $donasi['judul_program'] = $prog['judul'] ?? '-';
        $donasi['slug_program'] = $prog['slug'] ?? '';
    }
}

// Jika kode donasi tidak ditemukan, tampilkan 404 ramah
if (!$donasi) {
    http_response_code(404);
    $judul_halaman = 'Donasi Tidak Ditemukan';
    $menu_aktif = 'donasi';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <div class="kontainer">
        <div class="kotak-404">
            <h1>404</h1>
            <h2>Donasi Tidak Ditemukan</h2>
            <p>Maaf, kode donasi yang Anda tuju tidak tercatat dalam sistem kami. Pastikan tautan yang Anda gunakan sudah benar.</p>
            <div>
                <a href="<?= url('program.php') ?>" class="tombol tombol-utama">&larr; Kembali ke Daftar Program</a>
            </div>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$judul_halaman = 'Instruksi Donasi - ' . $donasi['kode'];
$menu_aktif = 'donasi';
$deskripsi_halaman = 'Instruksi transfer dan konfirmasi pembayaran donasi sosial Pekarangan Institute.';

$info_pembayaran = ambil_pengaturan('info_pembayaran', '[Nama Bank] [Nomor Rekening] a.n. Pekarangan Institute');
$nomor_admin_wa  = ambil_pengaturan('whatsapp', '');

// Buat template pesan WhatsApp untuk konfirmasi donasi
$pesan_wa = sprintf(
    "Halo Admin Pekarangan Institute, saya telah mengirimkan donasi untuk program:\n\nKode Donasi: %s\nProgram: %s\nNominal: %s\n\nMohon diverifikasi pembayarannya. Terima kasih!",
    $donasi['kode'],
    $donasi['judul_program'],
    rupiah($donasi['jumlah'])
);

$tautan_konfirmasi_wa = tautan_wa($nomor_admin_wa, $pesan_wa);

require_once __DIR__ . '/includes/header.php';
?>

<div class="kontainer">
    <div class="kartu-sukses">
        <div class="sukses-ikon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </div>

        <div class="teks-tengah jarak-bawah-lg">
            <h1 class="sukses-judul">Terima Kasih atas Kebaikan Anda</h1>
            <p class="teks-redup">
                Niat baik Anda telah kami catat dalam antrean verifikasi donasi. Silakan selesaikan proses transfer untuk mewujudkan dampak nyata.
            </p>
        </div>

        <!-- Kode Referensi Donasi -->
        <div class="kode-kotak">
            <div class="kode-kotak-label">Kode Referensi Donasi</div>
            <div class="kode-kotak-nilai"><?= e($donasi['kode']) ?></div>
            <span class="teks-xs teks-redup blok jarak-atas-xs">
                Gunakan kode ini saat melakukan konfirmasi kepada admin kami.
            </span>
        </div>

        <!-- Banner Verifikasi Donasi Sesuai Aturan Bisnis -->
        <div class="banner-peringatan" role="note">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <div>
                <strong>Donasi dihitung setelah dikonfirmasi admin</strong>
                <p class="jarak-atas-xs teks-sm">
                    Progres dana pada halaman program akan bertambah secara resmi setelah dana masuk dan diverifikasi oleh admin.
                </p>
            </div>
        </div>

        <!-- Rincian Donasi -->
        <h3 class="teks-lg jarak-atas-lg jarak-bawah-sm">Rincian Donasi</h3>
        <table class="tabel-ringkasan" aria-label="Rincian Donasi">
            <thead>
                <tr>
                    <th scope="col">Keterangan</th>
                    <th scope="col" class="teks-kanan">Rincian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Program Sosial</td>
                    <td><strong><?= e($donasi['judul_program']) ?></strong></td>
                </tr>
                <tr>
                    <td>Status Donasi</td>
                    <td>
                        <span class="lencana lencana-kuning">Menunggu Verifikasi</span>
                    </td>
                </tr>
                <tr class="baris-total">
                    <td>Nominal Donasi</td>
                    <td class="teks-biru teks-tebal"><?= rupiah($donasi['jumlah']) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Instruksi Pembayaran Rekening -->
        <div class="kotak-instruksi-bank">
            <h4>Rekening Resmi Donasi:</h4>
            <p class="jarak-bawah-sm">
                Silakan lakukan transfer sebesar <strong><?= rupiah($donasi['jumlah']) ?></strong> ke rekening resmi berikut:
            </p>
            <div class="kotak-rekening">
                <?= nl2br(e($info_pembayaran)) ?>
            </div>
            <p class="teks-xs teks-redup jarak-nol">
                Sertakan kode referensi <strong><?= e($donasi['kode']) ?></strong> pada catatan transfer bank.
            </p>
        </div>

        <!-- Tombol Konfirmasi WhatsApp -->
        <div class="grup-aksi-sukses">
            <a href="<?= e($tautan_konfirmasi_wa) ?>" target="_blank" rel="noopener noreferrer" class="tombol tombol-whatsapp tombol-blok">
                Konfirmasi via WhatsApp
            </a>

            <div class="grup-tombol grup-tombol-tengah jarak-atas-sm">
                <a href="<?= url('program-detail.php?slug=' . urlencode($donasi['slug_program'])) ?>" class="tombol tombol-sekunder">
                    Lihat Program
                </a>
                <a href="<?= url() ?>" class="tombol tombol-sekunder">
                    Halaman Utama
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
