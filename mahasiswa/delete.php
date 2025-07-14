<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

$nim = $_GET['nim'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "DELETE FROM mahasiswa WHERE nim = :nim";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data mahasiswa berhasil dihapus'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menghapus data mahasiswa'];
        }
        
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . $e->getMessage()];
        header("Location: index.php");
        exit;
    }
}

// Ambil data mahasiswa untuk konfirmasi
$mahasiswa = $conn->query("SELECT * FROM mahasiswa WHERE nim = '$nim'")->fetch();

if (!$mahasiswa) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Data mahasiswa tidak ditemukan'];
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Mahasiswa - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / 
                <a href="index.php">Data Mahasiswa</a> / 
                Hapus Data
            </div>
            <h1>Konfirmasi Hapus Data</h1>
        </div>
        
        <div class="confirmation-box">
            <h3>Anda yakin ingin menghapus data berikut?</h3>
            
            <div class="data-details">
                <div class="detail-item">
                    <span class="detail-label">NIM:</span>
                    <span class="detail-value"><?= $mahasiswa['nim'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama:</span>
                    <span class="detail-value"><?= $mahasiswa['nama_mhs'] ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Program Studi:</span>
                    <span class="detail-value"><?= $mahasiswa['nama_prodi'] ?></span>
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