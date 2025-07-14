<?php
require_once '../config/db.php';
require_once '../config/helpers.php';

$db = new Database();
$conn = $db->getConnection();

/* ---------- Pagination ---------- */
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

/* ---------- Pencarian ---------- */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
$params = [];

if ($search !== '') {
    $whereClause = "WHERE kodejurusan LIKE :search 
                    OR namajurusan LIKE :search 
                    OR kajur LIKE :search";
    $params[':search'] = "%{$search}%";
}

try {
    /* --- hitung total data --- */
    $countSQL = "SELECT COUNT(*) AS total FROM jurusan $whereClause";
    $stmt = $conn->prepare($countSQL);
    $stmt->execute($params);
    $totalRecords = (int)$stmt->fetch()['total'];
    $totalPages = (int)ceil($totalRecords / $limit);

    /* --- ambil data --- */
    $dataSQL = "SELECT * FROM jurusan $whereClause 
                ORDER BY namajurusan ASC
                LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($dataSQL);

    /* bind pencarian (jika ada) */
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $jurusan = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jurusan - Sistem Akademik PNL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / Data Jurusan
            </div>
            <h1>Data Jurusan</h1>
            <p>Kelola data jurusan di Politeknik Negeri Lhokseumawe</p>
        </div>
        
        <?php showMessage(); ?>
        
        <div class="actions">
            <a href="create.php" class="btn btn-primary">
                ➕ Tambah Jurusan
            </a>
            
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari jurusan..." value="<?= escape($search) ?>">
                <button type="submit" class="btn btn-success">🔍 Cari</button>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-warning">✖️ Reset</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="table-container">
            <?php if (empty($jurusan)): ?>
                <div class="no-data">
                    <h3>Tidak ada data jurusan</h3>
                    <p>Silakan tambah data jurusan baru</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Jurusan</th>
                            <th>Nama Jurusan</th>
                            <th>Ketua Jurusan</th>
                            <th>NIP Ketua</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $offset + 1;
                        foreach ($jurusan as $row): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= escape($row['kodejurusan']) ?></strong></td>
                            <td><?= escape($row['namajurusan']) ?></td>
                            <td><?= escape($row['kajur']) ?></td>
                            <td><?= escape($row['nipkajur']) ?></td>
                            <td><?= escape($row['ket'] ?? '-') ?></td>
                            <td>
                                <a href="edit.php?kode=<?= urlencode($row['kodejurusan']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                <a href="delete.php?kode=<?= urlencode($row['kodejurusan']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jurusan ini?')">🗑️ Hapus</a>
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