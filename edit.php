<?php
session_start(); // Mulai session di awal file

// Sertakan file koneksi database
include_once 'config.php';

$message = '';
$siswa = null; // Variabel untuk menyimpan data siswa yang akan diedit

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);

    // Ambil data siswa berdasarkan ID
    $sql_select = "SELECT * FROM siswa WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        if ($stmt_select->execute()) {
            $result_select = $stmt_select->get_result();
            if ($result_select->num_rows == 1) {
                $siswa = $result_select->fetch_assoc();
            } else {
                // Set pesan error di session jika data tidak ditemukan
                $_SESSION['status_message'] = 'error_edit_not_found';
                header("location: view_siswa.php");
                exit();
            }
        } else {
            // Set pesan error di session jika gagal mengambil data
            $_SESSION['status_message'] = 'error_edit_fetch';
            $_SESSION['error_details'] = $stmt_select->error;
            header("location: view_siswa.php");
            exit();
        }
        $stmt_select->close();
    } else {
        // Set pesan error di session jika persiapan statement gagal
        $_SESSION['status_message'] = 'error_edit_prepare_select';
        $_SESSION['error_details'] = $conn->error;
        header("location: view_siswa.php");
        exit();
    }
} else {
    // Jika tidak ada ID, redirect kembali ke index.php
    header("location: index.php");
    exit();
}

// Proses update data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_siswa'])) {
    $id_update = $_POST['id'];
    $nisn = $_POST['nisn'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $jurusan = $_POST['jurusan']; // Ambil nilai dari dropdown
    $nomor_handphone = $_POST['nomor_handphone'];
    $alamat = $_POST['alamat'];

    // Validasi sederhana
    if (empty($nisn) || empty($nama_lengkap) || empty($jenis_kelamin) || empty($tanggal_lahir) || empty($asal_sekolah) || empty($jurusan) || empty($nomor_handphone) || empty($alamat)) {
        $message = '<div style="color: red; text-align: center; margin-bottom: 15px;">Semua kolom harus diisi!</div>';
    } else {
        // Query untuk update data
        $sql_update = "UPDATE siswa SET nisn=?, nama_lengkap=?, jenis_kelamin=?, tanggal_lahir=?, asal_sekolah=?, jurusan=?, nomor_handphone=?, alamat=? WHERE id=?";

        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("ssssssssi", $nisn, $nama_lengkap, $jenis_kelamin, $tanggal_lahir, $asal_sekolah, $jurusan, $nomor_handphone, $alamat, $id_update);
            if ($stmt_update->execute()) {
                // Set pesan sukses di session
                $_SESSION['status_message'] = 'success_edit';
                // Redirect ke view_siswa.php
                header("Location: view_siswa.php");
                exit();
            } else {
                // Cek jika error karena NISN duplikat (Error Code 1062)
                if ($conn->errno == 1062) {
                    $_SESSION['status_message'] = 'error_edit_duplicate_nisn';
                    $_SESSION['error_details'] = 'NISN sudah terdaftar. Mohon gunakan NISN lain.';
                } else {
                    // Set pesan error umum di session
                    $_SESSION['status_message'] = 'error_edit';
                    $_SESSION['error_details'] = $stmt_update->error;
                }
                header("Location: view_siswa.php");
                exit();
            }
            $stmt_update->close();
        } else {
            // Set pesan error di session jika persiapan statement gagal
            $_SESSION['status_message'] = 'error_edit_prepare_update';
            $_SESSION['error_details'] = $conn->error;
            header("Location: view_siswa.php");
            exit();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Data Siswa</h1>
        <?php echo $message; // Tampilkan pesan validasi form jika ada ?>

        <?php if ($siswa): ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $siswa['id']); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($siswa['id']); ?>">
            <div class="form-group">
                <label for="nisn">NISN:</label>
                <input type="text" id="nisn" name="nisn" value="<?php echo htmlspecialchars($siswa['nisn']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($siswa['nama_lengkap']); ?>" required>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" <?php echo ($siswa['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php echo ($siswa['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($siswa['tanggal_lahir']); ?>" required>
            </div>
            <div class="form-group">
                <label for="asal_sekolah">Asal Sekolah:</label>
                <input type="text" id="asal_sekolah" name="asal_sekolah" value="<?php echo htmlspecialchars($siswa['asal_sekolah']); ?>" required>
            </div>
            <div class="form-group">
                <label for="jurusan">Jurusan:</label>
                <select id="jurusan" name="jurusan" required>
                    <option value="">Pilih Jurusan</option>
                    <option value="IPA" <?php echo ($siswa['jurusan'] == 'IPA') ? 'selected' : ''; ?>>IPA</option>
                    <option value="IPS" <?php echo ($siswa['jurusan'] == 'IPS') ? 'selected' : ''; ?>>IPS</option>
                    <option value="AGAMA" <?php echo ($siswa['jurusan'] == 'AGAMA') ? 'selected' : ''; ?>>AGAMA</option>
                    <option value="BAHASA" <?php echo ($siswa['jurusan'] == 'BAHASA') ? 'selected' : ''; ?>>BAHASA</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nomor_handphone">Nomor Handphone:</label>
                <input type="tel" id="nomor_handphone" name="nomor_handphone" value="<?php echo htmlspecialchars($siswa['nomor_handphone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" rows="4" required><?php echo htmlspecialchars($siswa['alamat']); ?></textarea>
            </div>
            <button type="submit" name="update_siswa" class="btn btn-primary">Perbarui Data</button>
            <a href="view_siswa.php" class="btn btn-secondary" style="background-color: #6c757d; color: white;">Kembali ke Data Siswa</a>
        </form>
        <?php else: ?>
            <p style="text-align: center;">Data siswa tidak dapat dimuat. Mungkin ID tidak valid atau data tidak ditemukan.</p>
            <a href="view_siswa.php" class="btn btn-secondary" style="background-color: #6c757d; color: white;">Kembali ke Data Siswa</a>
        <?php endif; ?>
    </div>
</body>
</html>
