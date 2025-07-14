<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db   = new Database();
$conn = $db->getConnection();

/* ---------- Pagination ---------- */
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

/* ---------- Pencarian ---------- */
$search      = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
$params      = [];

if ($search !== '') {
    $whereClause = "WHERE pr.namaprodi   LIKE :search
                    OR pr.kodeprodi     LIKE :search
                    OR j.namajurusan    LIKE :search";
    $params[':search'] = "%{$search}%";
}

try {
    /* --- hitung total data --- */
    $countSQL = "SELECT COUNT(*) AS total
                 FROM prodi pr
                 LEFT JOIN jurusan j ON pr.kodejurusan = j.kodejurusan
                 $whereClause";
    $stmt = $conn->prepare($countSQL);
    $stmt->execute($params);
    $totalRecords = (int) $stmt->fetch()['total'];
    $totalPages   = (int) ceil($totalRecords / $limit);

    /* --- ambil data --- */
    $dataSQL = "SELECT
                    pr.kodeprodi,
                    pr.namaprodi,
                    pr.statusakred,
                    pr.jenjang,
                    pr.namakaprodi,
                    pr.nipkaprodi,
                    pr.ket,
                    j.namajurusan
                FROM prodi pr
                LEFT JOIN jurusan j ON pr.kodejurusan = j.kodejurusan
                $whereClause
                ORDER BY pr.kodeprodi ASC
                LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($dataSQL);

    /* bind pencarian (jika ada) */
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $prodi = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Program Studi - Sistem Akademik PNL</title>
    <!-- *** STYLE (cukup satu berkas, sama dengan mahasiswa) *** -->
    <style>
        /* … seluruh style sama persis dengan halaman mahasiswa … */
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / Data Program Studi
            </div>
            <h1>Data Program Studi</h1>
            <p>Kelola data program studi Politeknik Negeri Lhokseumawe</p>
        </div>

        <?php showMessage(); ?>

        <!-- Actions: tambah + cari -->
        <div class="actions">
            <a href="create.php" class="btn btn-primary">
                ➕ Tambah Prodi
            </a>

            <form method="GET" class="search-form">
                <input type="text"
                       name="search"
                       placeholder="Cari prodi..."
                       value="<?= escape($search) ?>">
                <button type="submit" class="btn btn-success">🔍 Cari</button>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-warning">✖️ Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <?php if (empty($prodi)): ?>
                <div class="no-data">
                    <h3>Tidak ada data program studi</h3>
                    <p>Silakan tambah data prodi baru</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Prodi</th>
                        <th>Nama Prodi</th>
                        <th>Jenjang</th>
                        <th>Akreditasi</th>
                        <th>KaProdi</th>
                        <th>NIP</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = $offset + 1;
                    foreach ($prodi as $row):
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= escape($row['kodeprodi']) ?></strong></td>
                            <td><?= escape($row['namaprodi']) ?></td>
                            <td><?= escape($row['jenjang']) ?></td>
                            <td><?= escape($row['statusakred']) ?></td>
                            <td><?= escape($row['namakaprodi']) ?></td>
                            <td><?= escape($row['nipkaprodi']) ?></td>
                            <td><?= escape($row['namajurusan']) ?></td>
                            <td>
                                <a href="edit.php?kode=<?= urlencode($row['kodeprodi']) ?>"
                                   class="btn btn-warning btn-sm">✏️ Edit</a>
                                <a href="delete.php?kode=<?= urlencode($row['kodeprodi']) ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?><?= $search ? "&search=" . urlencode($search) : '' ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
