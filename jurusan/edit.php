<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

$kode = $_GET['kode'] ?? null;
if (!$kode) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Kode jurusan tidak valid'];
    header("Location: index.php");
    exit;
}

$jurusan = $conn->query("SELECT * FROM jurusan WHERE kodejurusan = '$kode'")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "UPDATE jurusan SET
                  namajurusan = :nama,
                  kajur = :kajur,
                  nipkajur = :nip,
                  ket = :ket
                  WHERE kodejurusan = :kode";
        
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':kode', $kode);
        $stmt->bindParam(':nama', $_POST['nama']);
        $stmt->bindParam(':kajur', $_POST['kajur']);
        $stmt->bindParam(':nip', $_POST['nip']);
        $stmt->bindParam(':ket', $_POST['ket']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data jurusan berhasil diperbarui'];
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui data: ' . $e->getMessage()];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jurusan - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / 
                <a href="index.php">Data Jurusan</a> / 
                Edit Data
            </div>
            <h1>Edit Data Jurusan</h1>
            <p>Kode: <?= $jurusan['kodejurusan'] ?></p>
        </div>
        
        <?php showMessage(); ?>
        
        <form method="POST" class="form-container">
            <div class="form-grid">
                <div class="form-section">
                    <h3>Informasi Jurusan</h3>
                    <div class="form-group">
                        <label>Kode Jurusan</label>
                        <input type="text" value="<?= $jurusan['kodejurusan'] ?>" disabled>
                        <small class="form-text">Kode jurusan tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Jurusan</label>
                        <input type="text" name="nama" value="<?= $jurusan['namajurusan'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="ket" value="<?= $jurusan['ket'] ?>">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Ketua Jurusan</h3>
                    <div class="form-group">
                        <label>Nama Ketua Jurusan</label>
                        <input type="text" name="kajur" value="<?= $jurusan['kajur'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>NIP Ketua Jurusan</label>
                        <input type="text" name="nip" value="<?= $jurusan['nipkajur'] ?>" required 
                               pattern="\d{18}" title="NIP harus 18 digit angka">
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>