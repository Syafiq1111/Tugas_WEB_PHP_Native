<?php
session_start();

include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}
$limit = 7;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

try {
    if ($keyword) {
        $stmt = $koneksi->prepare("
            SELECT * FROM siswa 
            WHERE nama LIKE ? OR nisn LIKE ? OR kelas LIKE ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute(["%$keyword%", "%$keyword%", "%$keyword%", $limit, $offset]);
    } else {
        $stmt = $koneksi->prepare("
            SELECT * FROM siswa 
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
    }
    $data_siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($keyword) {
        $countStmt = $koneksi->prepare("
            SELECT COUNT(*) FROM siswa 
            WHERE nama LIKE ? OR nisn LIKE ? OR kelas LIKE ?
        ");
        $countStmt->execute(["%$keyword%", "%$keyword%", "%$keyword%"]);
    } else {
        $countStmt = $koneksi->prepare("SELECT COUNT(*) FROM siswa");
        $countStmt->execute();
    }
    $total_data = $countStmt->fetchColumn();

    $total_halaman = ceil($total_data / $limit);

} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Data Siswa - Website SPP</title>
    <link rel="stylesheet" href="lihat.css">
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
                <li><a href="lihatdata.php" class="sidebar-nav-link active">Lihat Data Siswa</a></li>
                <li><a href="../updatedata/updatedata.php" class="sidebar-nav-link">Update Data/SPP Siswa</a></li>
                <li><a href="../hapusdata/hapusdata.php" class="sidebar-nav-link">Hapus Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-admin-content">
        <div class="dashboard-container">
            <section class="dashboard-welcome">
                <h2 class="section-title">Data Lengkap Siswa</h2>
                <p>Daftar semua siswa dalam sistem. Gunakan fitur pencarian untuk menemukan data spesifik.</p>
            </section>

            <form method="GET" class="search-form">
                <input type="text" name="keyword" class="search-input" placeholder="Cari nama / NISN / kelas..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" class="search-btn">Cari</button>
            </form>

            <div class="table-container">
                <?php if (!empty($data_siswa)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Semester</th>
                            <th>Nominal SPP</th>
                            <th>Status Pembayaran</th>
                            <th>Dibuat Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_siswa as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><?= ucfirst($row['semester']) ?></td>
                            <td><?= "Rp " . number_format($row['nominal_spp'], 2, ',', '.') ?></td>
                            <td>
                                <span class="status-badge <?= $row['status_pembayaran'] == 'lunas' ? 'status-lunas' : 'status-belum-lunas' ?>">
                                    <?= $row['status_pembayaran'] == 'lunas' ? 'Lunas' : 'Belum Lunas' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['created_at'] ?? '-') ?></td>
                            <td><a href="detail.php?id=<?= $row['id'] ?>" class="btn-detail">Lihat Detail</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>" class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php else: ?>
                    <p class="no-data-message">Tidak ada data siswa yang ditemukan.</p>
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
