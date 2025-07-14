<?php
require_once '../config/db.php';
require_once '../config/helpers.php';


$db   = new Database();
$conn = $db->getConnection();

/* ---------- Pagination ---------- */
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

/* ---------- Pencarian ---------- */
$search      = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
$params      = [];

if ($search !== '') {
    $whereClause = "WHERE m.nama_mhs LIKE :search 
                    OR m.nim LIKE :search 
                    OR p.namaprodi LIKE :search";
    $params[':search'] = "%{$search}%";
}

try {
    /* --- hitung total data --- */
    $countSQL = "SELECT COUNT(*) AS total
                 FROM mahasiswa m
                 LEFT JOIN prodi p   ON m.nama_prodi   = p.namaprodi
                 LEFT JOIN jurusan j ON m.nama_jurusan = j.namajurusan
                 $whereClause";
    $stmt = $conn->prepare($countSQL);
    $stmt->execute($params);
    $totalRecords = (int)$stmt->fetch()['total'];
    $totalPages   = (int)ceil($totalRecords / $limit);

    /* --- ambil data --- */
    $dataSQL = "SELECT 
                    m.nim,
                    m.nama_mhs,
                    m.alamat,
                    m.nama_kelas,
                    p.namaprodi      AS nama_prodi,
                    j.namajurusan    AS nama_jurusan
                FROM mahasiswa m
                LEFT JOIN prodi p   ON m.nama_prodi   = p.namaprodi
                LEFT JOIN jurusan j ON m.nama_jurusan = j.namajurusan
                $whereClause
                ORDER BY m.nim DESC
                LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($dataSQL);

    /* bind pencarian (jika ada) */
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $mahasiswa = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - Sistem Akademik PNL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            margin-bottom: 20px;
        }
        
        .breadcrumb a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
        }
        
        .breadcrumb a:hover {
            opacity: 1;
        }
        
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #1e7e34;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
        }
        
        .search-form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            width: 300px;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .table tr:hover {
            background-color: #f8f9fa;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #007bff;
        }
        
        .pagination .current {
            background-color: #007bff;
            color: white;
        }
        
        .pagination a:hover {
            background-color: #e9ecef;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .close {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-form input {
                width: 100%;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="../">Dashboard</a> / Data Mahasiswa
            </div>
            <h1>Data Mahasiswa</h1>
            <p>Kelola data mahasiswa Politeknik Negeri Lhokseumawe</p>
        </div>
        
        <?php showMessage(); ?>
        
        <div class="actions">
            <a href="create.php" class="btn btn-primary">
                ➕ Tambah Mahasiswa
            </a>
            
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari mahasiswa..." value="<?= escape($search) ?>">
                <button type="submit" class="btn btn-success">🔍 Cari</button>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-warning">✖️ Reset</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="table-container">
            <?php if (empty($mahasiswa)): ?>
                <div class="no-data">
                    <h3>Tidak ada data mahasiswa</h3>
                    <p>Silakan tambah data mahasiswa baru</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Alamat</th>
                            <th>Kelas</th>
                            <th>Program Studi</th>
                            <th>Jurusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $offset + 1;
                        foreach ($mahasiswa as $row): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= escape($row['nim']) ?></strong></td>
                            <td><?= escape($row['nama_mahasiswa']) ?></td>
                            <td><?= escape($row['alamat'] ?? '-') ?></td>
                            <td><?= escape($row['nama_kelas'] ?? '-') ?></td>
                            <td><?= escape($row['nama_prodi'] ?? '-') ?></td>
                            <td><?= escape($row['nama_jurusan'] ?? '-') ?></td>
                            <td>
                                <a href="edit.php?nim=<?= urlencode($row['nim']) ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                <a href="delete.php?nim=<?= urlencode($row['nim']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️ Hapus</a>
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