<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "INSERT INTO jurusan SET
                  kodejurusan = :kode,
                  namajurusan = :nama,
                  kajur = :kajur,
                  nipkajur = :nip,
                  ket = :ket";
        
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':kode', $_POST['kode']);
        $stmt->bindParam(':nama', $_POST['nama']);
        $stmt->bindParam(':kajur', $_POST['kajur']);
        $stmt->bindParam(':nip', $_POST['nip']);
        $stmt->bindParam(':ket', $_POST['ket']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data jurusan berhasil ditambahkan'];
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menambahkan data: ' . $e->getMessage()];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jurusan - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / 
                <a href="index.php">Data Jurusan</a> / 
                Tambah Data
            </div>
            <h1>Tambah Data Jurusan</h1>
        </div>
        
        <?php showMessage(); ?>
        
        <form method="POST" class="form-container">
            <div class="form-grid">
                <div class="form-section">
                    <h3>Informasi Jurusan</h3>
                    <div class="form-group">
                        <label>Kode Jurusan</label>
                        <input type="text" name="kode" required maxlength="6" 
                               pattern="[A-Z0-9]{6}" title="6 karakter huruf/angka (contoh: JRS001)">
                    </div>
                    <div class="form-group">
                        <label>Nama Jurusan</label>
                        <input type="text" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="ket">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Ketua Jurusan</h3>
                    <div class="form-group">
                        <label>Nama Ketua Jurusan</label>
                        <input type="text" name="kajur" required>
                    </div>
                    <div class="form-group">
                        <label>NIP Ketua Jurusan</label>
                        <input type="text" name="nip" required pattern="\d{18}" 
                               title="NIP harus 18 digit angka">
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>