<?php
session_start();

include '../koneksi.php'; 

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $kelas = $_POST['kelas'];
    $semester = $_POST['semester'];
    $nominal_spp = $_POST['nominal_spp'];
    $status_pembayaran = $_POST['status_pembayaran'];

    try {
        $stmt = $koneksi->prepare("INSERT INTO siswa 
            (nisn, nama, tanggal_lahir, tempat_lahir, kelas, semester, nominal_spp, status_pembayaran)
            VALUES (:nisn, :nama, :tanggal_lahir, :tempat_lahir, :kelas, :semester, :nominal_spp, :status_pembayaran)");
        
        $stmt->execute([
            ':nisn' => $nisn,
            ':nama' => $nama,
            ':tanggal_lahir' => $tanggal_lahir,
            ':tempat_lahir' => $tempat_lahir,
            ':kelas' => $kelas,
            ':semester' => $semester,
            ':nominal_spp' => $nominal_spp,
            ':status_pembayaran' => $status_pembayaran
        ]);

        $message = '<p class="success-message">Data siswa berhasil ditambahkan!</p>';
    } catch (PDOException $e) {
        $message = '<p class="error-message">Gagal menambahkan data: ' . $e->getMessage() . '</p>';
    }
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa - Website SPP</title>
    <link rel="stylesheet" href="tambahdata.css">
</head>
<body class="body">

<header class="header">
    <div class="site-title">Website Pembayaran SPP - Admin</div>
    <nav>
        <ul class="nav-list">
            <li><span class="nav-link welcome-text">Halo, <?= htmlspecialchars($_SESSION['username']); ?></span></li>
            <li><a href="../logout.php" class="nav-link btn btn-primary">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="admin-layout">
    <aside class="sidebar">
        <nav>
            <ul class="sidebar-nav-list">
                <li><a href="../dashboard/admin/dashboard.php" class="sidebar-nav-link">Dashboard Utama</a></li>
                <li><a href="tambahdata.php" class="sidebar-nav-link active">Tambah Siswa</a></li>
                <li><a href="../lihatdata/lihatdata.php" class="sidebar-nav-link">Lihat Data Siswa</a></li>
                <li><a href="../updatedata/updatedata.php" class="sidebar-nav-link">Update Data/SPP Siswa</a></li>
                <li><a href="../hapusdata/hapusdata.php" class="sidebar-nav-link">Hapus Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-admin-content">
        <div class="dashboard-container">
            <section class="dashboard-welcome">
                <h2 class="section-title">Tambah Data Siswa Baru</h2>
                <p>Masukkan data siswa pada form di bawah untuk menambahkan siswa baru ke dalam sistem.</p>
            </section>

            <div class="form-container">
                <?php echo $message; ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="text" id="nisn" name="nisn" required placeholder="Masukkan NISN siswa">
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" required placeholder="Masukkan tempat lahir">
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" id="kelas" name="kelas" required placeholder="Contoh: XII RPL 1">
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select id="semester" name="semester" required>
                            <option value="" disabled selected>Pilih Semester</option>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nominal_spp">Nominal SPP</label>
                        <input type="text" id="nominal_spp" name="nominal_spp" required placeholder="Contoh: 150000">
                    </div>
                    <div class="form-group">
                        <label for="status_pembayaran">Status Pembayaran</label>
                        <select id="status_pembayaran" name="status_pembayaran" required>
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="lunas">Lunas</option>
                            <option value="belum_lunas">Belum Lunas</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Tambah Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<footer class="footer">
    <p>&copy; 2025 Website Pembayaran SPP - Admin Panel</p>
</footer>

</body>
</html>
