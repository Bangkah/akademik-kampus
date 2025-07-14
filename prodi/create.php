<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

// Ambil daftar jurusan untuk dropdown
$jurusan = $conn->query("SELECT kodejurusan, namajurusan FROM jurusan")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeprodi   = $_POST['kodeprodi'];
    $namaprodi   = $_POST['namaprodi'];
    $statusakred = $_POST['statusakred'];
    $jenjang     = $_POST['jenjang'];
    $namakaprodi = $_POST['namakaprodi'];
    $nipkaprodi  = $_POST['nipkaprodi'];
    $ket         = $_POST['ket'] ?? null;
    $kodejurusan = $_POST['kodejurusan'];

    try {
        $query = "INSERT INTO prodi (
                    kodeprodi, namaprodi, statusakred, jenjang, 
                    namakaprodi, nipkaprodi, ket, kodejurusan
                  ) VALUES (
                    :kodeprodi, :namaprodi, :statusakred, :jenjang,
                    :namakaprodi, :nipkaprodi, :ket, :kodejurusan
                  )";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':kodeprodi', $kodeprodi);
        $stmt->bindParam(':namaprodi', $namaprodi);
        $stmt->bindParam(':statusakred', $statusakred);
        $stmt->bindParam(':jenjang', $jenjang);
        $stmt->bindParam(':namakaprodi', $namakaprodi);
        $stmt->bindParam(':nipkaprodi', $nipkaprodi);
        $stmt->bindParam(':ket', $ket);
        $stmt->bindParam(':kodejurusan', $kodejurusan);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data prodi berhasil ditambahkan'];
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
    <title>Tambah Prodi - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Sesuaikan jika perlu -->
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> /
                <a href="index.php">Data Prodi</a> /
                Tambah Data
            </div>
            <h1>Tambah Data Program Studi</h1>
        </div>

        <?php showMessage(); ?>

        <form method="POST" class="form-container">
            <div class="form-grid">
                <div class="form-section">
                    <h3>Informasi Prodi</h3>

                    <div class="form-group">
                        <label for="kodeprodi">Kode Prodi</label>
                        <input type="text" name="kodeprodi" id="kodeprodi" maxlength="6" required>
                    </div>

                    <div class="form-group">
                        <label for="namaprodi">Nama Prodi</label>
                        <input type="text" name="namaprodi" id="namaprodi" maxlength="50" required>
                    </div>

                    <div class="form-group">
                        <label for="statusakred">Akreditasi</label>
                        <input type="text" name="statusakred" id="statusakred" maxlength="20" required>
                    </div>

                    <div class="form-group">
                        <label for="jenjang">Jenjang</label>
                        <select name="jenjang" id="jenjang" required>
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="namakaprodi">Nama KaProdi</label>
                        <input type="text" name="namakaprodi" id="namakaprodi" maxlength="18" required>
                    </div>

                    <div class="form-group">
                        <label for="nipkaprodi">NIP KaProdi</label>
                        <input type="text" name="nipkaprodi" id="nipkaprodi" maxlength="18" required>
                    </div>

                    <div class="form-group">
                        <label for="ket">Keterangan</label>
                        <input type="text" name="ket" id="ket" maxlength="40">
                    </div>

                    <div class="form-group">
                        <label for="kodejurusan">Jurusan</label>
                        <select name="kodejurusan" id="kodejurusan" required>
                            <?php foreach ($jurusan as $j): ?>
                                <option value="<?= $j['kodejurusan'] ?>"><?= $j['namajurusan'] ?></option>
                            <?php endforeach; ?>
                        </select>
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
