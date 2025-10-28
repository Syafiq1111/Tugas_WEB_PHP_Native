<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login/login.php?error=unauthorized');
    exit;
}

require '../../koneksi.php';

$totalSiswaQuery = $koneksi->query("SELECT COUNT(*) AS total FROM siswa");
$totalSiswa = $totalSiswaQuery->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$belumLunasQuery = $koneksi->query("SELECT COUNT(*) AS total FROM siswa WHERE status_pembayaran = 'belum_lunas'");
$belumLunas = $belumLunasQuery->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$pemasukanQuery = $koneksi->prepare("
    SELECT SUM(jumlah_bayar) AS total_pemasukan 
    FROM pembayaraan 
    WHERE MONTH(tanggal_bayar) = MONTH(CURRENT_DATE())
      AND YEAR(tanggal_bayar) = YEAR(CURRENT_DATE())
");
$pemasukanQuery->execute();
$pemasukan = $pemasukanQuery->fetch(PDO::FETCH_ASSOC)['total_pemasukan'] ?? 0;

$pembayaranStmt = $koneksi->query("
    SELECT 
        p.id,
        s.nama AS nama_siswa,
        s.kelas,
        p.tanggal_bayar,
        p.jumlah_bayar,
        p.keterangan
    FROM pembayaraan p
    INNER JOIN siswa s ON p.siswa_id = s.id
    ORDER BY p.tanggal_bayar DESC
");
$pembayaran = $pembayaranStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Website SPP</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        table th {
            background-color: #004aad;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f7f7f7;
        }
        .no-data {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body class="body">

<header class="header">
    <div class="site-title">Website Pembayaran SPP - Admin</div>
    <nav>
        <ul class="nav-list">
            <li><span class="nav-link welcome-text">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
            <li><a href="../../logout.php" class="nav-link btn btn-primary">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="admin-layout">
    <aside class="sidebar">
        <nav>
            <ul class="sidebar-nav-list">
                <li><a href="dashboard.php" class="sidebar-nav-link active">Dashboard Utama</a></li>
                <li><a href="../../tambahdata/tambahdata.php" class="sidebar-nav-link">Tambah Siswa</a></li>
                <li><a href="../../lihatdata/lihatdata.php" class="sidebar-nav-link">Lihat Data Siswa</a></li>
                <li><a href="../../updatedata/updatedata.php" class="sidebar-nav-link">Update Data/SPP Siswa</a></li>
                <li><a href="../../hapusdata/hapusdata.php" class="sidebar-nav-link">Hapus Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-admin-content">
        <div class="dashboard-container">
            <section class="dashboard-welcome">
                <h2 class="section-title">Selamat Datang di Dashboard Admin</h2>
                <p>Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>
            </section>

            <!-- 📊 Kartu Statistik -->
            <section class="admin-overview">
                <div class="grid">
                    <div class="card">
                        <h3 class="card-title">Total Siswa</h3>
                        <p class="card-text" style="font-size: 24px; font-weight: bold; color: #004aad;">
                            <?= $totalSiswa; ?>
                        </p>
                    </div>
                    <div class="card">
                        <h3 class="card-title">Tagihan Belum Lunas</h3>
                        <p class="card-text" style="font-size: 24px; font-weight: bold; color: #ff4d4d;">
                            <?= $belumLunas; ?>
                        </p>
                    </div>
                    <div class="card">
                        <h3 class="card-title">Pemasukan Bulan <?= date('F Y'); ?></h3>
                        <p class="card-text" style="font-size: 24px; font-weight: bold; color: #0ab432;">
                            Rp <?= number_format($pemasukan, 2, ',', '.'); ?>
                        </p>
                    </div>
                </div>
            </section>

            <!-- 💰 Tabel Data Pembayaran -->
            <section class="payment-table">
                <h3>Riwayat Pembayaran</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pembayaran): ?>
                            <?php foreach ($pembayaran as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td><?= htmlspecialchars($row['kelas']); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_bayar']); ?></td>
                                    <td>Rp <?= number_format($row['jumlah_bayar'], 2, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">Belum ada data pembayaran.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
</div>

<footer class="footer">
    <p>&copy; 2025 Website Pembayaran SPP - Admin Panel</p>
</footer>

</body>
</html>
