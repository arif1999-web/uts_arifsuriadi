<?php
session_start(); // Mulai session di awal file

// Sertakan file koneksi database
include_once 'config.php';

$message_script = '';
// Cek apakah ada pesan sukses di session
if (isset($_SESSION['status_message']) && $_SESSION['status_message'] == 'success_add') {
    $message_script = '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript">
            Swal.fire({
                title: "Berhasil!",
                text: "Data siswa berhasil ditambahkan.",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>';
    unset($_SESSION['status_message']); // Hapus pesan dari session agar tidak muncul lagi
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Data Siswa</h2>
        <?php echo $message_script; // Tampilkan SweetAlert jika ada pesan sukses ?>
        <table>
            <thead>
                <tr>
                    <th>NISN</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Asal Sekolah</th>
                    <th>Jurusan</th>
                    <th>Nomor HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query untuk mengambil semua data siswa
                $sql = "SELECT * FROM siswa ORDER BY nama_lengkap ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data setiap baris
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["nisn"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nama_lengkap"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["jenis_kelamin"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["tanggal_lahir"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["asal_sekolah"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["jurusan"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nomor_handphone"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["alamat"]) . "</td>";
                        echo "<td class='actions'>";
                        echo "<a href='edit.php?id=" . $row["id"] . "' class='btn btn-edit'>Edit</a>";
                        echo "<a href='delete.php?id=" . $row["id"] . "' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>Hapus</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' style='text-align: center;'>Tidak ada data siswa.</td></tr>";
                }
                $conn->close(); // Tutup koneksi setelah semua operasi selesai
                ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn btn-secondary" style="background-color: #4300FF; color: white;">Tambah Data</a>
        </div>
    </div>
</body>
</html>
