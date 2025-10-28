<?php
$host = 'localhost';
$dbname = 'tes_spp';
$username = 'root';
$password = '';

try {
    $koneksi = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $koneksi->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
