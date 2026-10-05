<?php
declare(strict_types=1);
require_once './classes/GuestBook.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Inisialisasi token CSRF acak
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$guestBook = new GuestBook();
$message = '';
$error = '';

// 2. Pemrosesan formulir saat submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postToken = $_POST['csrf_token'] ?? '';

    // Verifikasi Token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Akses Ditolak: Token CSRF tidak valid.');
    }

    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pesan = trim($_POST['pesan'] ?? '');

        // Validasi Masukan Sisi Server
        if (empty($nama)) {
            $error = 'Nama tidak boleh kosong!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format alamat email tidak valid!';
        } elseif (strlen($pesan) < 5) {
            $error = 'Isi pesan minimal harus 5 karakter!';
        } else {
            // Simpan ke basis data
            if ($guestBook->simpanPesan($nama, $email, $pesan)) {
                $message = 'Pesan Anda berhasil disimpan di buku tamu!';
            } else {
                $error = 'Gagal menyimpan pesan. Silakan coba lagi.';
            }
        }
    } elseif ($action === 'delete') {
        $idHapus = (int)($_POST['id_pesan'] ?? 0);
        if ($idHapus > 0 && $guestBook->hapusPesan($idHapus)) {
            $message = 'Pesan berhasil dihapus dari basis data!';
        } else {
            $error = 'Gagal menghapus pesan.';
        }
    }
}

// Ambil riwayat pesan terbaru
$daftarPesan = $guestBook->ambilSemuaPesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Digital Perpustakaan UNHAS - Tugas 7</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Roboto Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --unhas-red: #800000;
            --unhas-red-dark: #600000;
            --unhas-gold: #d4af37;
            --unhas-bg: #fdfbfb;
        }

        body {
            font-family: 'Roboto Mono', monospace;
            background-color: var(--unhas-bg);
            color: #212529;
        }

        .header-unhas {
            border-bottom: 3px solid var(--unhas-gold);
            padding-bottom: 12px;
        }

        .card-custom {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.06);
            background: #ffffff;
        }

        .card-header-unhas {
            background-color: var(--unhas-red);
            color: #ffffff;
            font-weight: 600;
            border-top-left-radius: 9px !important;
            border-top-right-radius: 9px !important;
            border-bottom: 2px solid var(--unhas-gold);
        }

        .btn-unhas {
            background-color: var(--unhas-red);
            color: #ffffff;
            font-weight: 600;
            border: 1px solid var(--unhas-red);
            transition: all 0.2s ease-in-out;
        }

        .btn-unhas:hover {
            background-color: var(--unhas-red-dark);
            color: var(--unhas-gold);
            border-color: var(--unhas-red-dark);
        }

        .form-control:focus {
            border-color: var(--unhas-red);
            box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.15);
        }

        .badge-unhas-gold {
            background-color: var(--unhas-gold);
            color: #000000;
            font-weight: 600;
        }

        .table-custom th {
            background-color: #f8f9fa;
            color: var(--unhas-red);
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container py-5" style="max-width: 900px;">
        
        <!-- Header Utama -->
        <div class="text-center mb-4 header-unhas">
            <h1 class="h3 fw-bold mb-1" style="color: var(--unhas-red);">Buku Tamu Perpustakaan UNHAS</h1>
            <p class="text-muted small mb-0">Sistem Pencatatan Pesan & Kesan Pengunjung</p>
        </div>

        <!-- Notifikasi Pesan -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3 alert-dismissible fade show mb-4" role="alert">
                <strong>Berhasil!</strong> <?= htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 alert-dismissible fade show mb-4" role="alert">
                <strong>Gagal!</strong> <?= htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulir Input Buku Tamu -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-unhas py-3">
                <i class="me-1">📝</i> Formulir Pengisian Buku Tamu
            </div>
            <div class="card-body p-4">
                <form action="guestbook.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action" value="create">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label fw-medium small">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control py-2" placeholder="Contoh: Ahmad Yani" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium small">Alamat Email</label>
                            <input type="email" name="email" id="email" class="form-control py-2" placeholder="nama@unhas.ac.id" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pesan" class="form-label fw-medium small">Pesan / Kesan</label>
                        <textarea name="pesan" id="pesan" rows="3" class="form-control" placeholder="Tuliskan pesan atau saran Anda untuk perpustakaan..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-unhas w-100 py-2">Kirim Pesan Sekarang</button>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Pesan Pengunjung -->
        <div class="card card-custom">
            <div class="card-header card-header-unhas py-3 d-flex justify-content-between align-items-center">
                <span><i>💬</i> Riwayat Pesan Pengunjung</span>
                <span class="badge badge-unhas-gold px-3 py-2">Total: <?= count($daftarPesan); ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-custom">
                        <thead>
                            <tr>
                                <th class="ps-3 py-3">Pengirim</th>
                                <th class="py-3">Pesan</th>
                                <th class="py-3 text-end">Waktu</th>
                                <th class="pe-3 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftarPesan)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada pesan dari pengunjung.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftarPesan as $p): ?>
                                    <tr>
                                        <td class="ps-3 py-3">
                                            <div class="fw-bold" style="color: var(--unhas-red);"><?= htmlspecialchars($p['nama']); ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($p['email']); ?></div>
                                        </td>
                                        <td class="text-wrap py-3" style="max-width: 300px;">
                                            <?= htmlspecialchars($p['pesan']); ?>
                                        </td>
                                        <td class="py-3 text-end small text-muted">
                                            <?= htmlspecialchars(date('d M Y, H:i', strtotime($p['tanggal_kirim']))); ?>
                                        </td>
                                        <td class="pe-3 py-3 text-center">
                                            <form action="guestbook.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan dari <?= htmlspecialchars($p['nama']); ?>?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id_pesan" value="<?= $p['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>