<?php
declare(strict_types=1);

$host = '127.0.0.1';
$db   = 'pengaduan_sekolah';
$user = 'root';
$pass = ''; // Sesuaikan dengan konfigurasi MySQL lokal Anda.

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Database tidak dapat dihubungkan. Periksa konfigurasi database.');
}
