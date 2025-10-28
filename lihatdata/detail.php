<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("ID siswa tidak ditemukan.");
}

$id = $_GET['id'];

try {
    $stmt = $koneksi->prepare("SELECT * FROM siswa WHERE id = ?");
    $stmt->execute([$id]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$siswa) {
        die("Data siswa tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Siswa</title>
    <link rel="stylesheet" href="detail.css">
</head>
<body class="body">
    <header class="header">
        <div class="site-title">Sistem Administrasi Sekolah</div>
        <ul class="nav-list">
            <li class="welcome-text">Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</li>
            <li><a href="../logout.php" class="nav-link btn-logout">Logout</a></li>
        </ul>
    </header>
    <main class="main-admin-content">
        <div class="dashboard-container">
            <div class="dashboard-welcome">
                <h1 class="section-title">Detail Data Siswa</h1>
                <p>Berikut adalah informasi lengkap tentang siswa yang dipilih.</p>
            </div>

            <table class="detail-table">
                <tr><th>ID</th><td><?= htmlspecialchars($siswa['id']) ?></td></tr>
                <tr><th>NISN</th><td><?= htmlspecialchars($siswa['nisn']) ?></td></tr>
                <tr><th>Nama</th><td><?= htmlspecialchars($siswa['nama']) ?></td></tr>
                <tr><th>Tanggal Lahir</th><td><?= date('d-m-Y', strtotime($siswa['tanggal_lahir'])) ?></td></tr>
                <tr><th>Tempat Lahir</th><td><?= htmlspecialchars($siswa['tempat_lahir']) ?></td></tr>
                <tr><th>Kelas</th><td><?= htmlspecialchars($siswa['kelas']) ?></td></tr>
                <tr><th>Semester</th><td><?= htmlspecialchars($siswa['semester']) ?></td></tr>
                <tr><th>Nominal SPP</th><td><?= "Rp " . number_format($siswa['nominal_spp'], 2, ',', '.') ?></td></tr>
                <tr><th>Status Pembayaran</th><td><?= ucfirst($siswa['status_pembayaran']) ?></td></tr>
                <?php if (isset($siswa['created_at'])): ?>
                <tr><th>Dibuat Pada</th><td><?= htmlspecialchars($siswa['created_at']) ?></td></tr>
                <?php endif; ?>
            </table>

            <div style="text-align: center;">
                <a href="lihatdata.php" class="back-link">← Kembali ke Daftar</a>
            </div>
        </div>
    </main>
    <footer class="footer">
        &copy; 2023 Sistem Administrasi Sekolah. All rights reserved.
    </footer>
</body>
</html>
