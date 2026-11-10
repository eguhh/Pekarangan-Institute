<?php
/**
 * Aksi Pemrosesan Produk - Pekarangan Institute (Firebase RTDB)
 * Menangani tambah, ubah, ubah status, dan hapus produk via data.php.
 * Memastikan proteksi CSRF, validasi bisnis integritas pesanan, dan pembersihan berkas foto.
 */

require_once __DIR__ . '/../includes/fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !cek_csrf()) {
    pesan_flash('merah', 'Permintaan tidak valid atau token keamanan kedaluwarsa.');
    alihkan('admin/produk.php');
}

$aksi = trim($_POST['aksi'] ?? '');

switch ($aksi) {
    case 'tambah':
    case 'ubah':
        $adalah_edit = ($aksi === 'ubah');
        $id = trim((string)($_POST['id'] ?? ''));

        if ($adalah_edit && $id === '') {
            pesan_flash('merah', 'ID produk tidak valid.');
            alihkan('admin/produk.php');
        }

        $nama = trim($_POST['nama'] ?? '');
        $slug_input = trim($_POST['slug'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $harga_input = trim($_POST['harga'] ?? '');
        $stok_input = trim($_POST['stok'] ?? '');
        $deskripsi_singkat = trim($_POST['deskripsi_singkat'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $penjual = trim($_POST['penjual'] ?? '');
        $status = trim($_POST['status'] ?? 'draf');

        $errors = [];

        if ($nama === '') {
            $errors['nama'] = 'Nama produk wajib diisi.';
        } elseif (mb_strlen($nama) > 200) {
            $errors['nama'] = 'Nama produk terlalu panjang (maksimal 200 karakter).';
        }

        $slug = buat_slug($slug_input !== '' ? $slug_input : $nama);
        // Cek keunikan slug pada data produk
        $semua_produk = produk_semua();
        foreach ($semua_produk as $p) {
            if (($p['slug'] ?? '') === $slug) {
                if (!$adalah_edit || (string)($p['id'] ?? '') !== $id) {
                    $slug = $slug . '-' . time();
                    break;
                }
            }
        }

        if ($kategori === '') {
            $errors['kategori'] = 'Kategori produk wajib diisi.';
        } elseif (mb_strlen($kategori) > 100) {
            $errors['kategori'] = 'Kategori terlalu panjang (maksimal 100 karakter).';
        }

        if ($harga_input === '' || !is_numeric($harga_input) || (int)$harga_input < 0) {
            $errors['harga'] = 'Harga harus berupa angka dan tidak boleh negatif.';
        }
        $harga = (int)$harga_input;

        if ($stok_input === '' || !is_numeric($stok_input) || (int)$stok_input < 0) {
            $errors['stok'] = 'Stok harus berupa angka dan minimal 0.';
        }
        $stok = (int)$stok_input;

        if ($deskripsi_singkat === '') {
            $errors['deskripsi_singkat'] = 'Deskripsi singkat wajib diisi.';
        } elseif (mb_strlen($deskripsi_singkat) > 160) {
            $errors['deskripsi_singkat'] = 'Deskripsi singkat tidak boleh lebih dari 160 karakter.';
        }

        if ($deskripsi === '') {
            $errors['deskripsi'] = 'Deskripsi lengkap wajib diisi.';
        }

        if (!in_array($status, ['draf', 'tampil', 'arsip'], true)) {
            $errors['status'] = 'Status produk tidak valid.';
        }

        // Ambil data produk lama jika mode edit
        $produk_lama = null;
        if ($adalah_edit) {
            $produk_lama = produk_ambil($id);
            if (!$produk_lama) {
                pesan_flash('merah', 'Produk lama tidak ditemukan.');
                alihkan('admin/produk.php');
            }
        }

        // Penanganan Unggahan & Penghapusan Foto
        $nama_foto = $produk_lama['foto'] ?? null;
        if (!empty($_POST['hapus_foto'])) {
            if (!empty($nama_foto)) {
                $jalur_lama = __DIR__ . '/../uploads/' . $nama_foto;
                if (file_exists($jalur_lama) && is_file($jalur_lama)) {
                    @unlink($jalur_lama);
                }
            }
            $nama_foto = null;
        } elseif (!empty($_FILES['foto']['name'])) {
            $hasil_unggah = unggah_berkas($_FILES['foto'], 'gambar');
            if (!$hasil_unggah['sukses']) {
                $errors['foto'] = $hasil_unggah['pesan'];
            } else {
                $nama_foto_baru = $hasil_unggah['nama'];
                // Hapus foto lama jika ada
                if (!empty($nama_foto)) {
                    $jalur_lama = __DIR__ . '/../uploads/' . $nama_foto;
                    if (file_exists($jalur_lama) && is_file($jalur_lama)) {
                        @unlink($jalur_lama);
                    }
                }
                $nama_foto = $nama_foto_baru;
            }
        }

        // Jika terdapat error validasi, kembalikan ke form
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_input'] = $_POST;
            $tujuan = $adalah_edit ? 'admin/produk-form.php?id=' . urlencode($id) : 'admin/produk-form.php';
            alihkan($tujuan);
        }

        // Simpan ke database RTDB
        try {
            $data_simpan = [
                'nama'              => $nama,
                'slug'              => $slug,
                'kategori'          => $kategori,
                'harga'             => $harga,
                'stok'              => $stok,
                'deskripsi_singkat' => $deskripsi_singkat,
                'deskripsi'         => $deskripsi,
                'penjual'           => $penjual,
                'foto'              => $nama_foto,
                'status'            => $status,
            ];

            if ($adalah_edit) {
                produk_ubah($id, $data_simpan);
                pesan_flash('hijau', 'Produk "' . $nama . '" berhasil diperbarui.');
            } else {
                $data_simpan['is_contoh'] = 0;
                produk_tambah($data_simpan);
                pesan_flash('hijau', 'Produk baru "' . $nama . '" berhasil ditambahkan.');
            }
        } catch (Throwable $e) {
            pesan_flash('merah', pesan_error_db('Gagal menyimpan data produk', $e));
        }

        alihkan('admin/produk.php');
        break;

    case 'ubah_status':
        $id = trim((string)($_POST['id'] ?? ''));
        $status_baru = trim($_POST['status_baru'] ?? '');

        if ($id === '' || !in_array($status_baru, ['tampil', 'draf', 'arsip'], true)) {
            pesan_flash('merah', 'Data status tidak valid.');
            alihkan('admin/produk.php');
        }

        try {
            $sukses = produk_ubah($id, ['status' => $status_baru]);
            if ($sukses) {
                pesan_flash('hijau', 'Status produk berhasil diubah menjadi ' . $status_baru . '.');
            } else {
                pesan_flash('merah', 'Gagal memperbarui status produk.');
            }
        } catch (Throwable $e) {
            pesan_flash('merah', pesan_error_db('Gagal memperbarui status', $e));
        }
        alihkan('admin/produk.php');
        break;

    case 'hapus':
        $id = trim((string)($_POST['id'] ?? ''));
        if ($id === '') {
            pesan_flash('merah', 'ID produk tidak valid.');
            alihkan('admin/produk.php');
        }

        try {
            // Periksa apakah produk pernah dipesan
            if (produk_pernah_dipesan($id)) {
                pesan_flash('kuning', 'Produk ini tidak dapat dihapus karena sudah memiliki riwayat pesanan. Anda dapat memilih untuk Mengarsipkan produk ini agar tidak muncul lagi di publik.');
                alihkan('admin/produk.php');
            }

            // Ambil info foto sebelum hapus record
            $data_produk = produk_ambil($id);
            if (!$data_produk) {
                pesan_flash('merah', 'Produk tidak ditemukan.');
                alihkan('admin/produk.php');
            }

            // Hapus file foto dari disk jika ada dan bukan data contoh
            if (!empty($data_produk['foto']) && empty($data_produk['is_contoh'])) {
                $jalur_foto = __DIR__ . '/../uploads/' . $data_produk['foto'];
                if (file_exists($jalur_foto) && is_file($jalur_foto)) {
                    @unlink($jalur_foto);
                }
            }

            // Hapus dari RTDB
            $sukses = produk_hapus($id);
            if ($sukses) {
                pesan_flash('hijau', 'Produk "' . ($data_produk['nama'] ?? '') . '" berhasil dihapus.');
            } else {
                pesan_flash('merah', 'Gagal menghapus produk.');
            }
        } catch (Throwable $e) {
            pesan_flash('merah', pesan_error_db('Gagal menghapus produk', $e));
        }

        alihkan('admin/produk.php');
        break;

    case 'ubah_stok_cepat':
        $id = trim((string)($_POST['id'] ?? ''));
        $perubahan = (int)($_POST['perubahan'] ?? 0);
        if ($id === '' || ($perubahan !== 1 && $perubahan !== -1)) {
            pesan_flash('merah', 'Data penyesuaian stok tidak valid.');
            alihkan('admin/produk.php');
        }

        $produk = produk_ambil($id);
        if (!$produk) {
            pesan_flash('merah', 'Produk tidak ditemukan.');
            alihkan('admin/produk.php');
        }

        if ($perubahan > 0) {
            $sukses = produk_tambah_stok_bersyarat($id, $perubahan);
        } else {
            $sukses = produk_kurangi_stok_bersyarat($id, abs($perubahan));
        }

        if ($sukses) {
            $tanda = ($perubahan > 0 ? '+1' : '-1');
            pesan_flash('hijau', 'Stok produk "' . ($produk['nama'] ?? '') . '" berhasil diperbarui (' . $tanda . '). Penyesuaian stok manual tercatat.');
        } else {
            pesan_flash('merah', 'Gagal memperbarui stok. Stok mungkin sudah 0 atau terjadi tabrakan pembaruan.');
        }

        alihkan('admin/produk.php');
        break;

    default:
        pesan_flash('merah', 'Aksi tidak dikenali.');
        alihkan('admin/produk.php');
        break;
}
