<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db   = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idkelas   = $_POST['idkelas'];
    $namakelas = $_POST['namakelas'];
    $ket       = $_POST['ket'] ?? null;

    try {
        $query = "INSERT INTO kelas (idkelas, namakelas, ket)
                  VALUES (:idkelas, :namakelas, :ket)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':idkelas', $idkelas);
        $stmt->bindParam(':namakelas', $namakelas);
        $stmt->bindParam(':ket', $ket);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data kelas berhasil ditambahkan'];
            header('Location: index.php');
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
    <title>Tambah Kelas - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Sesuaikan jika perlu -->
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / 
                <a href="index.php">Data Kelas</a> / 
                Tambah Data
            </div>
            <h1>Tambah Data Kelas</h1>
        </div>

        <?php showMessage(); ?>

        <form method="POST" class="form-container">
            <div class="form-grid">
                <div class="form-section">
                    <h3>Data Kelas</h3>

                    <div class="form-group">
                        <label for="idkelas">ID Kelas</label>
                        <input type="text" name="idkelas" id="idkelas" maxlength="2" required>
                    </div>

                    <div class="form-group">
                        <label for="namakelas">Nama Kelas</label>
                        <input type="text" name="namakelas" id="namakelas" maxlength="20" required>
                    </div>

                    <div class="form-group">
                        <label for="ket">Keterangan</label>
                        <input type="text" name="ket" id="ket" maxlength="10">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="index.php" class="btn btn-secondary">↩️ Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
