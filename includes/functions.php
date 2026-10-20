<?php
declare(strict_types=1);

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrfToken(), $sent)) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

function requireLogin(): void {
    if (empty($_SESSION['user'])) {
        header('Location: ../login.php');
        exit;
    }
}

function requireStaff(): void {
    requireLogin();
    if (!in_array($_SESSION['user']['role'], ['admin_it', 'petugas'], true)) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function getCategories(PDO $pdo): array {
    return $pdo->query('SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori')
               ->fetchAll();
}

function getComplaints(PDO $pdo, int $userId, bool $staff, string $status, string $search, int $limit = 50): array {
    $limit = max(1, min($limit, 100));
    $where = [];
    $params = [];

    if (!$staff) {
        $where[] = 'p.id_user = :id_user';
        $params['id_user'] = $userId;
    }
    if (in_array($status, ['Menunggu', 'Diproses', 'Selesai'], true)) {
        $where[] = 'p.status = :status';
        $params['status'] = $status;
    }
    if ($search !== '') {
        $where[] = '(p.judul LIKE :search OR p.lokasi LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    $sql = 'SELECT p.id_pengaduan, p.judul, p.lokasi, p.deskripsi, p.status,
                   p.tanggal_laporan, k.nama_kategori, u.nama AS nama_pelapor
            FROM pengaduan p
            JOIN kategori k ON k.id_kategori = p.id_kategori
            JOIN users u ON u.id_user = p.id_user';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY p.tanggal_laporan DESC LIMIT ' . $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function statusClass(string $status): string {
    return match ($status) {
        'Selesai' => 'selesai',
        'Diproses' => 'diproses',
        default => 'menunggu',
    };
}
