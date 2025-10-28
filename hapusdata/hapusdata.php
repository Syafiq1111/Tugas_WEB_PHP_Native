<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

$pesan_sukses = $pesan_error = "";

if (isset($_GET['hapus'])) {
    $nisn_to_delete = trim($_GET['hapus']);

    if (!preg_match('/^[0-9]+$/', $nisn_to_delete)) {
        $pesan_error = "Format NISN tidak valid.";
    } else {
        try {
            $stmt_delete = $koneksi->prepare("DELETE FROM siswa WHERE nisn = :nisn");
            $stmt_delete->bindParam(':nisn', $nisn_to_delete, PDO::PARAM_STR);
            $stmt_delete->execute();

            if ($stmt_delete->rowCount() > 0) {
                $pesan_sukses = "Data siswa berhasil dihapus.";
            } else {
                $pesan_error = "Data siswa tidak ditemukan atau sudah dihapus.";
            }
        } catch (PDOException $e) {
            $pesan_error = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
        }
    }
}

try {
    $stmt = $koneksi->query("SELECT nisn, nama, kelas FROM siswa ORDER BY nama ASC");
    $data_siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data_siswa = [];
    $pesan_error = "Tidak dapat mengambil data siswa saat ini.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Data Siswa - Website SPP</title>
    <link rel="stylesheet" href="hapusdata.css">
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
                <li><a href="../tambahdata/tambahdata.php" class="sidebar-nav-link">Tambah Siswa</a></li>
                <li><a href="../lihatdata/lihatdata.php" class="sidebar-nav-link">Lihat Data Siswa</a></li>
                <li><a href="../updatedata/updatedata.php" class="sidebar-nav-link">Update Data/SPP Siswa</a></li>
                <li><a href="hapusdata.php" class="sidebar-nav-link active">Hapus Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-admin-content">
        <div class="dashboard-container">
            <section class="dashboard-welcome">
                <h2 class="section-title">Hapus Data Siswa</h2>
                <p>Pilih siswa yang datanya ingin Anda hapus secara permanen.</p>
            </section>

            <?php if ($pesan_sukses): ?>
                <div class="message success-message"><?= htmlspecialchars($pesan_sukses) ?></div>
            <?php endif; ?>
            <?php if ($pesan_error): ?>
                <div class="message error-message"><?= htmlspecialchars($pesan_error) ?></div>
            <?php endif; ?>

            <div class="table-container">
                <?php if (!empty($data_siswa)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1; foreach ($data_siswa as $row): ?>
                        <tr>
                            <td><?= $nomor++ ?></td>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td>
                                <a href="hapusdata.php?hapus=<?= urlencode($row['nisn']) ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini secara permanen?');">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="no-data-message">Tidak ada data siswa yang dapat dihapus.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<footer class="footer">
    <p>&copy; 2025 Website Pembayaran SPP - Admin Panel</p>
</footer>

</body>
</html>
