<?php
require_once "config/db.php";
$database = new Database();
$db = $database->getConnection();

function getTotal($db, $tabel) {
    $stmt = $db->query("SELECT COUNT(*) as total FROM $tabel");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}

$totalDosen = getTotal($db, "dosen");
$totalMahasiswa = getTotal($db, "mahasiswa");
$totalProdi = getTotal($db, "prodi");
$totalJurusan = getTotal($db, "jurusan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Akademik PNL</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Sistem Akademik PNL</h1>
            <nav>
                <ul>
                    <li><a href="#" class="active"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="dosen/"><i class="fas fa-chalkboard-teacher"></i> Dosen</a></li>
                    <li><a href="mahasiswa/"><i class="fas fa-user-graduate"></i> Mahasiswa</a></li>
                    <li><a href="prodi/"><i class="fas fa-book"></i> Prodi</a></li>
                    <li><a href="jurusan/"><i class="fas fa-building"></i> Jurusan</a></li>
                    <li><a href="kelas/"><i class="fas fa-users"></i> Kelas</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section>
            <h2>Statistik</h2>
            <div class="stats">
                <div class="stat-box">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <p>Dosen</p>
                    <h3><?= $totalDosen ?></h3>
                </div>
                <div class="stat-box">
                    <i class="fas fa-user-graduate"></i>
                    <p>Mahasiswa</p>
                    <h3><?= $totalMahasiswa ?></h3>
                </div>
                <div class="stat-box">
                    <i class="fas fa-book"></i>
                    <p>Prodi</p>
                    <h3><?= $totalProdi ?></h3>
                </div>
                <div class="stat-box">
                    <i class="fas fa-building"></i>
                    <p>Jurusan</p>
                    <h3><?= $totalJurusan ?></h3>
                </div>
            </div>
        </section>

        
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date("Y") ?> Politeknik Negeri Lhokseumawe. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
