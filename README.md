# Aplikasi Pengaduan Sarana Sekolah

Aplikasi web PHP + MySQL untuk laporan sarana sekolah. Dirancang untuk dijalankan di Laragon/XAMPP.

## Fitur
- Login berdasarkan role: murid, admin_it, petugas
- Murid mengirim pengaduan dan melihat pengaduannya sendiri
- Admin IT/petugas melihat semua pengaduan dan mengubah status
- Riwayat perubahan status
- Validasi server, prepared statements, password hashing, CSRF token, output escaping
- Pencarian dan filter status dengan query terbatas

## Instalasi (Laragon)
1. Ekstrak folder `pengaduan-sekolah` ke `C:\laragon\www\`.
2. Jalankan Laragon, lalu start Apache dan MySQL.
3. Buka `http://localhost/phpmyadmin`.
4. Import file `database.sql`.
5. Periksa `config/koneksi.php` dan sesuaikan host/user/password jika perlu.
6. Buka `http://localhost/pengaduan-sekolah/`.
7. Untuk membuat akun pertama, buka `setup_admin.php` sekali saja. Buat akun admin, lalu hapus file `setup_admin.php` dari folder proyek setelah berhasil.

## Membuat akun murid/petugas
Gunakan menu "Buat akun" di halaman login untuk akun murid. Akun petugas tambahan dapat dibuat dari phpMyAdmin dengan password hash yang dihasilkan PHP `password_hash()`, atau tambahkan halaman manajemen akun pada pengembangan berikutnya.

## Catatan
- Ini merupakan starter project untuk pembelajaran, bukan sistem produksi yang sudah diaudit.
- Pastikan `setup_admin.php` dihapus setelah admin pertama dibuat.
- Jangan gunakan kredensial default untuk server publik.
