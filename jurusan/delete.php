<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

$kode = $_GET['kode'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Cek apakah jurusan digunakan di tabel prodi
        $check = $conn->query("SELECT COUNT(*) as total FROM prodi WHERE kodejurusan = '$kode'")->fetch();
        
        if ($check['total'] > 0) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Jurusan tidak bisa dihapus karena masih digunakan oleh program studi'];
            header("Location: index.php");
            exit;
        }

        $query = "DELETE FROM jurusan WHERE kodejurusan = :kode";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':kode', $kode);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data jurusan berhasil dihapus'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menghapus data jurusan'];
        }
        
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . $e->getMessage()];
        header("Location: index.php");
        exit;
    }
}

// Ambil data jurusan untuk konfirmasi
$jurusan = $conn->query("SELECT * FROM jurusan WHERE kodejurusan = '$kode'")->fetch();

if (!$jurusan) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Data jurusan tidak ditemukan'];
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Jurusan - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / 
                <a href="index.php">Data Jurusan</a> / 
                Hapus Data
            </div>
            <h1>Konfirmasi Hapus Data</h1>
        </div>
        
        <div class="confirmation-box">
            <h3>Anda yakin ingin menghapus jurusan berikut?</h3>
            
            <div class="data-details">
                <div class="detail-item">
                    <span class="detail-label">Kode Jurusan:</span>
                    <span class="detail-value"><?= $jurusan['kodejurusan'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama Jurusan:</span>
                    <span class="detail-value"><?= $jurusan['namajurusan'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Ketua Jurusan:</span>
                    <span class="detail-value"><?= $jurusan['kajur'] ?></span>
                </div>
            </div>
            
            <form method="POST" class="confirmation-form">
                <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>