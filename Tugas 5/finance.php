<?php
declare(strict_types=1);
require_once './Transaction.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi saldo dan riwayat transaksi dalam sesi jika belum ada
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 0.0;
}
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

// 1. Bangkitkan token CSRF acak kriptografis jika belum ada di sesi
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$error = '';

// 2. Pemrosesan Formulir Transaksi saat metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postToken = $_POST['csrf_token'] ?? '';

    // Proteksi CSRF: Verifikasi token menggunakan perbandingan waktu konstan (hash_equals)
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Akses Ditolak: Token CSRF tidak valid.');
    }

    $typeInput = $_POST['type'] ?? '';
    $amountInput = $_POST['amount'] ?? '';

    // Validasi jumlah transaksi sebagai angka desimal positif
    if (!is_numeric($amountInput) || (float)$amountInput <= 0) {
        $error = 'Jumlah transaksi harus berupa angka desimal positif!';
    } else {
        $amount = (float)$amountInput;

        // Pencocokan jenis transaksi menggunakan Match Expression (PHP 8)
        $type = match ($typeInput) {
            'deposit' => 'deposit',
            'withdraw' => 'withdraw',
            default => null,
        };

        if ($type === null) {
            $error = 'Jenis transaksi tidak valid!';
        } else {
            // Instansiasi objek Transaction
            $transactionId = 'TRX-' . time() . '-' . rand(100, 999);
            $transaction = new Transaction($transactionId, $type, $amount);

            // Eksekusi pemrosesan transaksi (menambah/mengurangi saldo)
            if ($transaction->process()) {
                $_SESSION['transactions'][] = [
                    'id' => $transaction->getId(),
                    'type' => $transaction->getType(),
                    'amount' => $transaction->getAmount(),
                    'time' => date('H:i:s d-m-Y')
                ];
                $message = 'Transaksi ' . $type . ' sebesar Rp ' . number_format($amount, 2, ',', '.') . ' berhasil diproses!';
            } else {
                $error = 'Transaksi penarikan gagal! Saldo Anda tidak mencukupi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Keuangan Sederhana - Tugas Mandiri 5</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 800px;">
        <h1 class="h3 mb-4 text-center text-primary fw-bold">Sistem Manajemen Keuangan Sederhana</h1>

        <!-- Kartu Informasi Saldo -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body bg-white rounded p-4 text-center">
                <span class="text-muted d-block mb-1">Sisa Saldo Saat Ini:</span>
                <h2 class="display-6 fw-bold text-success mb-0">
                    Rp <?= htmlspecialchars(number_format($_SESSION['balance'], 2, ',', '.')); ?>
                </h2>
            </div>
        </div>

        <!-- Notifikasi Pesan Sukses / Error -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulir Transaksi dengan Proteksi CSRF -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-primary text-white fw-bold">Formulir Transaksi Keuangan</div>
            <div class="card-body p-4">
                <form action="finance.php" method="POST">
                    <!-- Token Hidden CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="mb-3">
                        <label for="type" class="form-label fw-bold">Jenis Transaksi:</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="deposit">Deposit (Pemasukan)</option>
                            <option value="withdraw">Penarikan (Pengeluaran)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label fw-bold">Jumlah Transaksi (Rp):</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" placeholder="Contoh: 50000.00" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Proses Transaksi</button>
                </form>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi disanitasi dengan htmlspecialchars -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white fw-bold">Riwayat Transaksi</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($_SESSION['transactions'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat transaksi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_reverse($_SESSION['transactions']) as $trx): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($trx['id']); ?></code></td>
                                        <td>
                                            <span class="badge <?= $trx['type'] === 'deposit' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?= htmlspecialchars(strtoupper($trx['type'])); ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold">
                                            Rp <?= htmlspecialchars(number_format($trx['amount'], 2, ',', '.')); ?>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($trx['time']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>