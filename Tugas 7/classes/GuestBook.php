<?php
declare(strict_types=1);

class GuestBook {
    private PDO $pdo;

    /**
     * Konstruktor untuk menginisialisasi koneksi PDO ke MySQL
     */
    public function __construct(
        string $host = 'localhost',
        string $dbname = 'db_perpustakaan',
        string $username = 'root',
        string $password = 'qwcortis123321$' 
    ) {
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die('Koneksi Basis Data Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan data pesan baru menggunakan Prepared Statement (Mencegah SQL Injection)
     */
    public function simpanPesan(string $nama, string $email, string $pesan): bool {
        $sql = "INSERT INTO buku_tamu (nama, email, pesan, tanggal_kirim) 
                VALUES (:nama, :email, :pesan, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nama'  => $nama,
            ':email' => $email,
            ':pesan' => $pesan
        ]);
    }

    /**
     * Mengambil seluruh riwayat pesan dari yang terbaru
     */
    public function ambilSemuaPesan(): array {
        $sql = "SELECT id, nama, email, pesan, tanggal_kirim 
                FROM buku_tamu 
                ORDER BY tanggal_kirim DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Menghapus pesan berdasarkan ID (Mencegah SQL Injection)
     */
    public function hapusPesan(int $id): bool {
        $sql = "DELETE FROM buku_tamu WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}