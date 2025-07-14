<?php
require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 5;
$offset = ($page - 1) * $records_per_page;

$query = "SELECT * FROM dosen LIMIT $offset, $records_per_page";
$stmt = $db->prepare($query);
$stmt->execute();

$total_query = "SELECT COUNT(*) as total FROM dosen";
$total_stmt = $db->prepare($total_query);
$total_stmt->execute();
$total_rows = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_rows / $records_per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Daftar Dosen</h2>
        <a href="create.php" class="btn btn-primary mb-3">Tambah Dosen</a>
        
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Prodi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nidn']) ?></td>
                    <td><?= htmlspecialchars($row['nama_dosen']) ?></td>
                    <td><?= htmlspecialchars($row['jab_fungsional']) ?></td>
                    <td><?= htmlspecialchars($row['kode_prodi']) ?></td>
                    <td>
                        <a href="edit.php?nidn=<?= $row['nidn'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?nidn=<?= $row['nidn'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</body>
</html>