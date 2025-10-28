<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'siswa') {
    header('Location: ../../../login/login.php?error=unauthorized');
    exit;
}

require '../../../koneksi.php';

if (!isset($_GET['siswa_id'])) {
    echo "ID siswa tidak ditemukan.";
    exit;
}

$siswa_id = (int) $_GET['siswa_id'];

$stmt = $koneksi->prepare("SELECT * FROM siswa WHERE id = :id");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah_bayar = (float) $_POST['jumlah_bayar'];
    $tanggal_bayar = date('Y-m-d');
    $keterangan = trim($_POST['keterangan']);

    try {
        $insert = $koneksi->prepare("
            INSERT INTO pembayaraan (siswa_id, tanggal_bayar, jumlah_bayar, keterangan)
            VALUES (:siswa_id, :tanggal_bayar, :jumlah_bayar, :keterangan)
        ");
        $insert->execute([
            'siswa_id' => $siswa_id,
            'tanggal_bayar' => $tanggal_bayar,
            'jumlah_bayar' => $jumlah_bayar,
            'keterangan' => $keterangan
        ]);
        if ($jumlah_bayar >= $siswa['nominal_spp']) {
            $update = $koneksi->prepare("UPDATE siswa SET status_pembayaran = 'Lunas' WHERE id = :id");
            $update->execute(['id' => $siswa_id]);
        }

        $pesan_sukses = "✅ Pembayaran berhasil disimpan!";
    } catch (PDOException $e) {
        $pesan_error = "❌ Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar SPP - <?= htmlspecialchars($siswa['nama']); ?></title>
    <link rel="stylesheet" href="bayar.css">
</head>
<body>

<header class="header">
    <div class="site-title-container">
        <h1 class="site-title">Pembayaran SPP</h1>
    </div>
</header>

<div class="form-container">
    <h2>Form Pembayaran SPP</h2>

    <?php if ($pesan_sukses): ?>
        <div class="alert-success"><?= $pesan_sukses; ?></div>
    <?php elseif ($pesan_error): ?>
        <div class="alert-error"><?= $pesan_error; ?></div>
    <?php endif; ?>

    <table class="data-table">
        <tr><td><strong>Nama</strong></td><td><?= htmlspecialchars($siswa['nama']); ?></td></tr>
        <tr><td><strong>NISN</strong></td><td><?= htmlspecialchars($siswa['nisn']); ?></td></tr>
        <tr><td><strong>Kelas</strong></td><td><?= htmlspecialchars($siswa['kelas']); ?></td></tr>
        <tr><td><strong>Nominal SPP</strong></td><td>Rp <?= number_format($siswa['nominal_spp'], 2, ',', '.'); ?></td></tr>
    </table>

    <form action="" method="POST">
        <div class="form-group">
            <label for="jumlah_bayar">Jumlah Bayar (Rp)</label>
            <input type="number" name="jumlah_bayar" id="jumlah_bayar" required min="1000">
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Contoh: Pembayaran bulan Oktober"></textarea>
        </div>

        <button type="submit" class="btn">💰 Bayar Sekarang</button>
        <a href="../dashboard.php" class="btn" style="background:#6c757d;">⬅ Kembali</a>
    </form>
</div>

<footer class="footer">
    <p>&copy; 2025 Website Pembayaran SPP</p>
</footer>

</body>
</html>
