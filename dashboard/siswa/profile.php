<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'siswa') {
    header('Location: ../../login/login.php?error=unauthorized');
    exit;
}

require '../../koneksi.php';
$stmt = $koneksi->prepare("
    SELECT s.id, s.nama, s.nisn, s.kelas, s.semester, s.tanggal_lahir, 
           s.nominal_spp, s.status_pembayaran
    FROM siswa s
    INNER JOIN user u ON u.username = s.nisn
    WHERE u.username = :username
");
$stmt->execute(['username' => $_SESSION['username']]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa</title>
    <link rel="stylesheet" href="../siswa/dashboard.css">
</head>
<body class="body">

<header class="header">
    <div class="site-title-container">
        <h1 class="site-title">Profil Siswa</h1>
    </div>
    <nav class="header-nav">
        <ul class="nav-list">
            <li><span class="nav-link welcome-text">Halo, <?= htmlspecialchars($_SESSION['username']); ?>!</span></li>
            <li><a href="../../logout.php" class="nav-link btn btn-logout">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="siswa-layout">
    <aside class="sidebar">
        <nav>
            <ul class="sidebar-nav-list">
                <li><a href="../siswa/dashboard.php" class="sidebar-nav-link">Dashboard Utama</a></li>
                <li><a href="profil.php" class="sidebar-nav-link active">Profil Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-siswa-content">
        <div class="dashboard-container">
            <section class="siswa-profile-section">
                <h2 class="section-title">Informasi Pribadi</h2>

                <table class="data-table">
                    <tbody>
                        <tr>
                            <td><strong>NISN</strong></td>
                            <td><?= htmlspecialchars($siswa['nisn']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Lengkap</strong></td>
                            <td><?= htmlspecialchars($siswa['nama']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas</strong></td>
                            <td><?= htmlspecialchars($siswa['kelas']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Semester</strong></td>
                            <td><?= htmlspecialchars($siswa['semester']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Lahir</strong></td>
                            <td><?= htmlspecialchars($siswa['tanggal_lahir']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nominal SPP</strong></td>
                            <td>Rp <?= number_format($siswa['nominal_spp'], 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status Pembayaran</strong></td>
                            <td>
                                <strong class="status-<?= strtolower($siswa['status_pembayaran']); ?>">
                                    <?= ($siswa['status_pembayaran'] === 'Lunas') ? '✅ Lunas' : '❌ Belum Lunas'; ?>
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="action-buttons">
                    <a href="../siswa/dashboard.php" class="btn btn-primary">← Kembali ke Dashboard</a>
                </div>
            </section>
        </div>
    </main>
</div>

<footer class="footer">
    <p>&copy; 2025 Website Pembayaran SPP - Profil Siswa</p>
</footer>

</body>
</html>
