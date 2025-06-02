<?php
error_reporting(E_ALL); // Aktifkan semua pelaporan error (HAPUS DI PRODUKSI)
ini_set('display_errors', 1); // Tampilkan error di browser (HAPUS DI PRODUKSI)

session_start(); // Mulai session di awal file

// Sertakan file koneksi database
include_once 'config.php';

// Inisialisasi variabel untuk pesan error (jika ada)
$message = '';

// Proses penambahan data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_siswa'])) {
    $nisn = $_POST['nisn'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $jurusan = $_POST['jurusan'];
    $nomor_handphone = $_POST['nomor_handphone'];
    $alamat = $_POST['alamat'];

    // Validasi sederhana
    if (empty($nisn) || empty($nama_lengkap) || empty($jenis_kelamin) || empty($tanggal_lahir) || empty($asal_sekolah) || empty($jurusan) || empty($nomor_handphone) || empty($alamat)) {
        $message = '<div style="color: red; text-align: center; margin-bottom: 15px;">Semua kolom harus diisi!</div>';
    } else {
        // Pastikan koneksi database berhasil sebelum melanjutkan
        if ($conn->connect_error) {
            $message = '<div style="color: red; text-align: center; margin-bottom: 15px;">Koneksi database gagal: ' . $conn->connect_error . '</div>';
        } else {
            // Query untuk memasukkan data
            $sql = "INSERT INTO siswa (nisn, nama_lengkap, jenis_kelamin, tanggal_lahir, asal_sekolah, jurusan, nomor_handphone, alamat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssssssss", $nisn, $nama_lengkap, $jenis_kelamin, $tanggal_lahir, $asal_sekolah, $jurusan, $nomor_handphone, $alamat);
                if ($stmt->execute()) { // Baris 34
                    // Set pesan sukses di session
                    $_SESSION['status_message'] = 'success_add';
                    // Redirect ke view_siswa.php
                    header("Location: view_siswa.php");
                    exit(); // Penting untuk menghentikan eksekusi script PHP setelah redirect
                } else {
                    // Cek jika error karena NISN duplikat (Error Code 1062)
                    if ($conn->errno == 1062) {
                        $message = '<div style="color: red; text-align: center; margin-bottom: 15px;">NISN sudah terdaftar. Mohol gunakan NISN lain.</div>';
                    } else {
                        // Tampilkan error spesifik dari MySQL
                        $message = '<div style="color: red; text-align: center; margin-bottom: 15px;">Error saat menambahkan data: ' . $stmt->error . '</div>';
                    }
                }
                $stmt->close();
            } else {
                $message = '<div style="color: red; text-align: center; margin-bottom: 15px;">Error mempersiapkan statement: ' . $conn->error . '</div>';
            }
        }
    }
}
$conn->close(); // Tutup koneksi setelah semua operasi selesai
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Form Pengisian Data Siswa</h1>
        <?php echo $message; // Tampilkan pesan error jika ada ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="nisn">NISN:</label>
                <input type="text" id="nisn" name="nisn" required value="<?php echo isset($_POST['nisn']) ? htmlspecialchars($_POST['nisn']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" required value="<?php echo isset($_POST['tanggal_lahir']) ? htmlspecialchars($_POST['tanggal_lahir']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="asal_sekolah">Asal Sekolah:</label>
                <input type="text" id="asal_sekolah" name="asal_sekolah" required value="<?php echo isset($_POST['asal_sekolah']) ? htmlspecialchars($_POST['asal_sekolah']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="jurusan">Jurusan:</label>
                <select id="jurusan" name="jurusan" required>
                    <option value="">Pilih Jurusan</option>
                    <option value="IPA" <?php echo (isset($_POST['jurusan']) && $_POST['jurusan'] == 'IPA') ? 'selected' : ''; ?>>IPA</option>
                    <option value="IPS" <?php echo (isset($_POST['jurusan']) && $_POST['jurusan'] == 'IPS') ? 'selected' : ''; ?>>IPS</option>
                    <option value="AGAMA" <?php echo (isset($_POST['jurusan']) && $_POST['jurusan'] == 'AGAMA') ? 'selected' : ''; ?>>AGAMA</option>
                    <option value="BAHASA" <?php echo (isset($_POST['jurusan']) && $_POST['jurusan'] == 'BAHASA') ? 'selected' : ''; ?>>BAHASA</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nomor_handphone">Nomor Handphone:</label>
                <input type="tel" id="nomor_handphone" name="nomor_handphone" required value="<?php echo isset($_POST['nomor_handphone']) ? htmlspecialchars($_POST['nomor_handphone']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" rows="4" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
            </div>
            <button type="submit" name="add_siswa" class="btn btn-primary">Tambah Siswa</button>
            <a href="view_siswa.php" class="btn btn-secondary" style="background-color:rgb(54, 100, 62); color: white;">Lihat Data</a>
        </form>
    </div>
</body>
</html>
