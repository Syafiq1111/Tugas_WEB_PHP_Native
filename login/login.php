<?php
session_start();
include '../koneksi.php';

if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../dashboard/admin/dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'siswa') {
        header('Location: ../dashboard/siswa/dashboard.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Harap isi semua kolom!';
    } else {
        try {
            $stmt = $koneksi->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $password_input_hash = hash('sha256', $password);
                if ($password_input_hash === $user['password']) {
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    if ($user['role'] === 'admin') {
                        header('Location: ../dashboard/admin/dashboard.php');
                        exit;
                    } elseif ($user['role'] === 'siswa') {
                        header('Location: ../dashboard/siswa/dashboard.php');
                        exit;
                    }
                } else {
                    $error = 'Password salah!';
                }
            } else {
                $error = 'Username tidak ditemukan!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Website Pembayaran SPP</title>
    <link rel="stylesheet" href="login.css">
</head>
<body class="body">

<header class="header" style="background-color: #004aad;">
    <h1 class="site-title">Login Website Pembayaran SPP</h1>
    <nav class="nav">
        <ul class="nav-list">
            <li><a href="../index.php" class="nav-link">Beranda</a></li>
            <li><a href="../about/about.php" class="nav-link">Tentang</a></li>
        </ul>
    </nav>
</header>

<main class="main">
    <section class="login-section">
        <h2 class="section-title">Form Login</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'logout_success'): ?>
            <p style="color: green; text-align: center;">Anda berhasil logout!</p>
        <?php endif; ?>

        <form class="login-form" action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </section>
</main>

<footer class="footer" style="background-color: #004aad;">
    <p>2025 Website Pembayaran SPP</p>
</footer>

</body>
</html>
