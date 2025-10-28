<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'siswa') {
    header('Location: ../../login/login.php?error=unauthorized');
    exit;
}

require '../../koneksi.php';

$stmt = $koneksi->prepare("
    SELECT s.id, s.nama, s.nisn, s.kelas, s.semester, s.nominal_spp, s.status_pembayaran
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

$riwayatStmt = $koneksi->prepare("
    SELECT tanggal_bayar, jumlah_bayar, keterangan
    FROM pembayaraan
    WHERE siswa_id = :siswa_id
    ORDER BY tanggal_bayar DESC
");
$riwayatStmt->execute(['siswa_id' => $siswa['id']]);
$riwayat = $riwayatStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Website SPP</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="body">

<header class="header">
    <div class="site-title-container">
        <h1 class="site-title">Website Pembayaran SPP - Siswa</h1>
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
                <li><a href="dashboard.php" class="sidebar-nav-link active">Dashboard Utama</a></li>
                <li><a href="profile.php" class="sidebar-nav-link">Profil Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-siswa-content">
        <div class="dashboard-container">
            <section class="dashboard-welcome">
                <h2 class="section-title">Selamat Datang di Dashboard Siswa</h2>
                <p>Kelola pembayaran SPP Anda dengan mudah. Lihat data pribadi, status pembayaran, dan riwayat transaksi di bawah ini.</p>
            </section>

            <section class="siswa-table">
                <h3>Data Siswa & Status Pembayaran</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Informasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>NISN</td>
                            <td><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td><?= htmlspecialchars($siswa['nama'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td><?= htmlspecialchars($siswa['kelas'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Semester</td>
                            <td><?= htmlspecialchars($siswa['semester'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td>Nominal SPP</td>
                            <td>Rp <?= number_format($siswa['nominal_spp'] ?? 0, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td>Status Pembayaran</td>
                            <td>
                                <strong class="status-<?= strtolower($siswa['status_pembayaran'] ?? 'belum'); ?>">
                                    <?= ($siswa['status_pembayaran'] === 'Lunas') ? '✅ Lunas' : '❌ Belum Lunas'; ?>
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h3>Riwayat Pembayaran</h3>
                <table class="riwayat-table">
                    <thead>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($riwayat): ?>
                            <?php foreach ($riwayat as $index => $row): ?>
                                <tr class="<?= ($index % 2 == 0) ? 'even' : 'odd'; ?>">
                                    <td><?= htmlspecialchars($row['tanggal_bayar']); ?></td>
                                    <td>Rp <?= number_format($row['jumlah_bayar'], 2, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="no-data">Belum ada riwayat pembayaran.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="action-buttons">
                    <a href="bayar/bayar.php?siswa_id=<?= $siswa['id']; ?>" class="btn btn-primary">Bayar SPP Sekarang</a>
                </div>
            </section>
        </div>
    </main>
</div>

<footer class="footer">
    <p>&copy; 2025 Website Pembayaran SPP - Dashboard Siswa</p>
</footer>

</body>
</html>