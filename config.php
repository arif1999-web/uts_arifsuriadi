<?php
// Konfigurasi koneksi database
define('DB_SERVER', 'localhost'); // Ganti dengan host database Anda jika berbeda
define('DB_USERNAME', 'root');     // Ganti dengan username database Anda
define('DB_PASSWORD', '');         // Ganti dengan password database Anda
define('DB_NAME', 'db_siswa');     // Ganti dengan nama database yang Anda buat

// Buat koneksi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
