<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

// Ambil data prodi dan jurusan untuk dropdown
$prodi = $conn->query("SELECT kodeprodi, namaprodi FROM prodi")->fetchAll();
$jurusan = $conn->query("SELECT kodejurusan, namajurusan FROM jurusan")->fetchAll();
$kelas = $conn->query("SELECT idkelas, namakelas FROM kelas")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "INSERT INTO mahasiswa SET
                  nim = :nim,
                  nama_mhs = :nama_mhs,
                  jkel = :jkel,
                  alamat = :alamat,
                  tempat = :tempat,
                  tglLahir = :tglLahir,
                  agama = :agama,
                  noHp = :noHp,
                  noKK = :noKK,
                  nama_prodi = :nama_prodi,
                  nama_jurusan = :nama_jurusan,
                  nama_kelas = :nama_kelas";
        
        $stmt = $conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':nim', $_POST['nim']);
        $stmt->bindParam(':nama_mhs', $_POST['nama_mhs']);
        $stmt->bindParam(':jkel', $_POST['jkel']);
        $stmt->bindParam(':alamat', $_POST['alamat']);
        $stmt->bindParam(':tempat', $_POST['tempat']);
        $stmt->bindParam(':tglLahir', $_POST['tglLahir']);
        $stmt->bindParam(':agama', $_POST['agama']);
        $stmt->bindParam(':noHp', $_POST['noHp']);
        $stmt->bindParam(':noKK', $_POST['noKK']);
        
        // Ambil nama prodi/jurusan/kelas berdasarkan kode
        $selectedProdi = $conn->query("SELECT namaprodi FROM prodi WHERE kodeprodi = '".$_POST['kode_prodi']."'")->fetch();
        $selectedJurusan = $conn->query("SELECT namajurusan FROM jurusan WHERE kodejurusan = '".$_POST['kode_jurusan']."'")->fetch();
        $selectedKelas = $conn->query("SELECT namakelas FROM kelas WHERE idkelas = '".$_POST['id_kelas']."'")->fetch();
        
        $stmt->bindParam(':nama_prodi', $selectedProdi['namaprodi']);
        $stmt->bindParam(':nama_jurusan', $selectedJurusan['namajurusan']);
        $stmt->bindParam(':nama_kelas', $selectedKelas['namakelas']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Data mahasiswa berhasil ditambahkan'];
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
    <title>Tambah Mahasiswa - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / 
                <a href="index.php">Data Mahasiswa</a> / 
                Tambah Data
            </div>
            <h1>Tambah Data Mahasiswa</h1>
        </div>
        
        <?php showMessage(); ?>
        
        <form method="POST" class="form-container">
            <div class="form-grid">
                <!-- Data Pribadi -->
                <div class="form-section">
                    <h3>Data Pribadi</h3>
                    <div class="form-group">
                        <label>NIM</label>
                        <input type="text" name="nim" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_mhs" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jkel" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tglLahir" required>
                    </div>
                    <div class="form-group">
                        <label>Agama</label>
                        <input type="text" name="agama" required>
                    </div>
                </div>
                
                <!-- Data Kontak -->
                <div class="form-section">
                    <h3>Data Kontak</h3>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="noHp">
                    </div>
                    <div class="form-group">
                        <label>No. KK</label>
                        <input type="text" name="noKK">
                    </div>
                </div>
                
                <!-- Data Akademik -->
                <div class="form-section">
                    <h3>Data Akademik</h3>
                    <div class="form-group">
                        <label>Program Studi</label>
                        <select name="kode_prodi" required>
                            <?php foreach ($prodi as $p): ?>
                            <option value="<?= $p['kodeprodi'] ?>"><?= $p['namaprodi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jurusan</label>
                        <select name="kode_jurusan" required>
                            <?php foreach ($jurusan as $j): ?>
                            <option value="<?= $j['kodejurusan'] ?>"><?= $j['namajurusan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="id_kelas" required>
                            <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['idkelas'] ?>"><?= $k['namakelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
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