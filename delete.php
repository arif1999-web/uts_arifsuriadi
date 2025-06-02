<?php
session_start(); // Mulai session di awal file

// Sertakan file koneksi database
include_once 'config.php';

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);

    // Query untuk menghapus data
    $sql = "DELETE FROM siswa WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Set pesan sukses di session
            $_SESSION['status_message'] = 'success_delete';
            // Redirect kembali ke halaman view_siswa.php setelah berhasil dihapus
            header("location: view_siswa.php");
            exit();
        } else {
            // Set pesan error di session jika gagal menghapus
            $_SESSION['status_message'] = 'error_delete';
            $_SESSION['error_details'] = $stmt->error; // Simpan detail error
            header("location: view_siswa.php");
            exit();
        }
        $stmt->close();
    } else {
        // Set pesan error di session jika persiapan statement gagal
        $_SESSION['status_message'] = 'error_delete_prepare';
        $_SESSION['error_details'] = $conn->error; // Simpan detail error
        header("location: view_siswa.php");
        exit();
    }
} else {
    // Jika tidak ada ID, redirect kembali ke index.php
    header("location: index.php");
    exit();
}
$conn->close();
?>