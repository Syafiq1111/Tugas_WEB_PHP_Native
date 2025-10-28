<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

$edit_mode = false;
$siswa_to_edit = null;
$pesan_sukses = $pesan_error = "";

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

try {
    if ($keyword) {
        $stmt_all = $koneksi->prepare("
            SELECT * FROM siswa 
            WHERE nama LIKE ? OR nisn LIKE ? OR kelas LIKE ?
            ORDER BY nama ASC
        ");
        $stmt_all->execute(["%$keyword%", "%$keyword%", "%$keyword%"]);
    } else {
        $stmt_all = $koneksi->query("SELECT * FROM siswa ORDER BY nama ASC");
    }
    $data_siswa = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal memuat data siswa: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_siswa'])) {
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $kelas = $_POST['kelas'];
    $semester = $_POST['semester'];
    $nominal_spp = (int)$_POST['nominal_spp'];
    $status_pembayaran = $_POST['status_pembayaran'];

    try {
        $stmt = $koneksi->prepare("
            UPDATE siswa SET
                nama = :nama,
                tanggal_lahir = :tanggal_lahir,
                tempat_lahir = :tempat_lahir,
                kelas = :kelas,
                semester = :semester,
                nominal_spp = :nominal_spp,
                status_pembayaran = :status_pembayaran
            WHERE nisn = :nisn
        ");
        $stmt->execute([
            ':nama' => $nama,
            ':tanggal_lahir' => $tanggal_lahir,
            ':tempat_lahir' => $tempat_lahir,
            ':kelas' => $kelas,
            ':semester' => $semester,
            ':nominal_spp' => $nominal_spp,
            ':status_pembayaran' => $status_pembayaran,
            ':nisn' => $nisn
        ]);

        $pesan_sukses = "✅ Data siswa berhasil diperbarui!";
    } catch (PDOException $e) {
        $pesan_error = "❌ Gagal memperbarui data siswa: " . $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $nisn_to_edit = $_GET['edit'];

    $stmt = $koneksi->prepare("SELECT * FROM siswa WHERE nisn = ?");
    $stmt->execute([$nisn_to_edit]);
    $siswa_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Siswa - Website SPP</title>
    <link rel="stylesheet" href="updatedata.css"> 
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
                <li><a href="updatedata.php" class="sidebar-nav-link active">Update Data/SPP Siswa</a></li>
                <li><a href="../hapusdata/hapusdata.php" class="sidebar-nav-link">Hapus Siswa</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-admin-content">
        <div class="dashboard-container">
            <section class="dashboard-welcome">
                <h2 class="section-title">Update Data Siswa</h2>
                <p>Pilih siswa untuk memperbarui data atau status pembayaran SPP.</p>
            </section>

            <form method="GET" class="search-form">
                <input type="text" name="keyword" class="search-input" placeholder="Cari nama / NISN / kelas..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" class="search-btn">Cari</button>
            </form>

            <?php if ($pesan_sukses): ?>
                <div class="message success-message"><?= htmlspecialchars($pesan_sukses) ?></div>
            <?php endif; ?>
            <?php if ($pesan_error): ?>
                <div class="message error-message"><?= htmlspecialchars($pesan_error) ?></div>
            <?php endif; ?>

            <?php if ($edit_mode && $siswa_to_edit): ?>
            <div class="form-container">
                <h3 class="form-title">Edit Data: <?= htmlspecialchars($siswa_to_edit['nama']) ?></h3>
                <form action="updatedata.php" method="POST">
                    <input type="hidden" name="nisn" value="<?= htmlspecialchars($siswa_to_edit['nisn']) ?>">
                    
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($siswa_to_edit['nama']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($siswa_to_edit['tanggal_lahir']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" value="<?= htmlspecialchars($siswa_to_edit['tempat_lahir']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" id="kelas" name="kelas" value="<?= htmlspecialchars($siswa_to_edit['kelas']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select id="semester" name="semester" required>
                            <option value="Ganjil" <?= $siswa_to_edit['semester'] == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                            <option value="Genap" <?= $siswa_to_edit['semester'] == 'Genap' ? 'selected' : '' ?>>Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nominal_spp">Nominal SPP</label>
                        <input type="number" id="nominal_spp" name="nominal_spp" value="<?= htmlspecialchars($siswa_to_edit['nominal_spp']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status_pembayaran">Status Pembayaran</label>
                        <select id="status_pembayaran" name="status_pembayaran" required>
                            <option value="belum lunas" <?= $siswa_to_edit['status_pembayaran'] == 'belum lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="lunas" <?= $siswa_to_edit['status_pembayaran'] == 'lunas' ? 'selected' : '' ?>>Lunas</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="update_siswa" class="btn btn-submit">Update Data</button>
                        <a href="updatedata.php" class="btn btn-cancel">Batal</a>
                    </div>
                </form>
            </div>
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
                            <th>Nominal SPP</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data_siswa as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><?= "Rp " . number_format($row['nominal_spp'], 2, ',', '.') ?></td>
                            <td>
                                <span class="status-badge <?= $row['status_pembayaran'] == 'lunas' ? 'status-lunas' : 'status-belum-lunas' ?>">
                                    <?= ucfirst($row['status_pembayaran']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="updatedata.php?edit=<?= urlencode($row['nisn']) ?>" class="btn btn-edit">Update</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
