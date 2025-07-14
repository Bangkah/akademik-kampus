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
    $whereClause = "WHERE namakelas LIKE :search OR idkelas LIKE :search OR ket LIKE :search";
    $params[':search'] = "%{$search}%";
}

try {
    // Hitung total data
    $countSQL = "SELECT COUNT(*) AS total FROM kelas $whereClause";
    $stmt = $conn->prepare($countSQL);
    $stmt->execute($params);
    $totalRecords = (int)$stmt->fetch()['total'];
    $totalPages   = (int)ceil($totalRecords / $limit);

    // Ambil data
    $dataSQL = "SELECT idkelas, namakelas, ket
                FROM kelas
                $whereClause
                ORDER BY idkelas ASC
                LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($dataSQL);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $kelas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kelas - Sistem Akademik PNL</title>
    <style>
        <link rel="stylesheet" href="../assets/css/style.css">
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="breadcrumb">
            <a href="../">Dashboard</a> / Data Kelas
        </div>
        <h1>Data Kelas</h1>
        <p>Kelola data kelas Politeknik Negeri Lhokseumawe</p>
    </div>

    <?php showMessage(); ?>

    <div class="actions">
        <a href="create.php" class="btn btn-primary">
            ➕ Tambah Kelas
        </a>

        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cari kelas..." value="<?= escape($search) ?>">
            <button type="submit" class="btn btn-success">🔍 Cari</button>
            <?php if ($search): ?>
                <a href="index.php" class="btn btn-warning">✖️ Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <?php if (empty($kelas)): ?>
            <div class="no-data">
                <h3>Tidak ada data kelas</h3>
                <p>Silakan tambah data kelas baru</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Kelas</th>
                        <th>Nama Kelas</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = $offset + 1; foreach ($kelas as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= escape($row['idkelas']) ?></strong></td>
                        <td><?= escape($row['namakelas']) ?></td>
                        <td><?= escape($row['ket'] ?? '-') ?></td>
                        <td>
                            <a href="edit.php?id=<?= urlencode($row['idkelas']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="delete.php?id=<?= urlencode($row['idkelas']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️ Hapus</a>
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
