<?php
declare(strict_types=1);

class Transaction {
    // Constructor Property Promotion (PHP 8.x) & Enkapsulasi Properti Private
    public function __construct(
        private string $id,
        private string $type,
        private float $amount
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    /**
     * Memproses transaksi keuangan pada saldo
     * Return true jika transaksi berhasil, false jika saldo tidak mencukupi.
     */
    public function process(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['balance'])) {
            $_SESSION['balance'] = 0.0;
        }

        if ($this->type === 'deposit') {
            $_SESSION['balance'] += $this->amount;
            return true;
        } elseif ($this->type === 'withdraw') {
            // Tolak penarikan bila saldo tidak mencukupi
            if ($_SESSION['balance'] < $this->amount) {
                return false;
            }
            $_SESSION['balance'] -= $this->amount;
            return true;
        }

        return false;
    }
}