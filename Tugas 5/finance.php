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

        $type = match ($typeInput) {
            'deposit' => 'deposit',
            'withdraw' => 'withdraw',
            default => null,
        };

        if ($type === null) {
            $error = 'Jenis transaksi tidak valid!';
        } else {
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & JetBrains Mono untuk Tampilan Fintech -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --fin-primary: #0f172a;
            --fin-accent: #2563eb;
            --fin-success: #16a34a;
            --fin-danger: #dc2626;
            --fin-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--fin-bg);
            color: #1e293b;
        }

        .balance-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
        }

        .balance-amount {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2.25rem;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: -0.5px;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        .card-custom .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
            color: var(--fin-primary);
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
            padding: 16px 20px;
        }

        .btn-fintech {
            background-color: var(--fin-accent);
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-fintech:hover {
            background-color: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .trx-amount {
            font-family: 'JetBrains Mono', monospace;
        }

        .table-custom th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        code.trx-id {
            font-family: 'JetBrains Mono', monospace;
            color: #475569;
            background-color: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container py-5" style="max-width: 820px;">
        
        <!-- Header Utama -->
        <div class="text-center mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">Dompet Digital Kampus</h1>
            <p class="text-muted small">Modul Pemrosesan Transaksi Keuangan Sederhana</p>
        </div>

        <!-- Kartu Utama Saldo Sesi  -->
        <div class="card balance-card p-4 mb-4 border-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-light opacity-75 small text-uppercase fw-semibold" style="letter-spacing: 1px;">Sisa Saldo Aktif</span>
                <span class="badge bg-primary bg-opacity-25 text-info px-3 py-2 rounded-pill">Status: Aktif</span>
            </div>
            <div class="balance-amount">
                Rp <?= htmlspecialchars(number_format($_SESSION['balance'], 2, ',', '.')); ?>
            </div>
        </div>

        <!-- Notifikasi Pesan Sukses / Error -->
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

        <!-- Formulir Transaksi Keuangan -->
        <div class="card card-custom mb-4">
            <div class="card-header">Formulir Transaksi</div>
            <div class="card-body p-4">
                <form action="finance.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="type" class="form-label fw-medium small text-secondary">Jenis Transaksi</label>
                            <select name="type" id="type" class="form-select py-2" required>
                                <option value="deposit"> Deposit (Pemasukan)</option>
                                <option value="withdraw"> Penarikan (Pengeluaran)</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label for="amount" class="form-label fw-medium small text-secondary">Jumlah Nominal (Rp)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control py-2" placeholder="Contoh: 100000.00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-fintech w-100">Proses Transaksi Sekarang</button>
                </form>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi -->
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Transaksi Terakhir</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal">Total: <?= count($_SESSION['transactions']); ?> Transaksi</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-custom">
                        <thead>
                            <tr>
                                <th class="ps-4">ID Transaksi</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th class="pe-4 text-end">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($_SESSION['transactions'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat transaksi yang tercatat.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_reverse($_SESSION['transactions']) as $trx): ?>
                                    <tr>
                                        <td class="ps-4"><code class="trx-id"><?= htmlspecialchars($trx['id']); ?></code></td>
                                        <td>
                                            <?php if ($trx['type'] === 'deposit'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">DEPOSIT</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">PENARIKAN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold trx-amount text-dark">
                                            <?= $trx['type'] === 'deposit' ? '+' : '-'; ?> Rp <?= htmlspecialchars(number_format($trx['amount'], 2, ',', '.')); ?>
                                        </td>
                                        <td class="pe-4 text-end small text-muted"><?= htmlspecialchars($trx['time']); ?></td>
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
