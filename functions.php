<?php
/**
 * Helper Functions untuk Sistem Akademik
 * File ini berisi fungsi-fungsi bantuan yang digunakan di seluruh aplikasi
 */

/**
 * Fungsi untuk escape HTML entities
 * Mencegah XSS attacks
 */
function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fungsi untuk menampilkan pesan (success/error)
 * Menggunakan session untuk menyimpan pesan sementara
 */
function showMessage() {
    // Tampilkan pesan dari parameter URL
    if (isset($_GET['message'])) {
        echo '<div class="alert alert-success">';
        echo '<strong>Sukses!</strong> ' . escape($_GET['message']);
        echo '<span class="close" onclick="this.parentElement.style.display=\'none\'">&times;</span>';
        echo '</div>';
    }
    
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-error">';
        echo '<strong>Error!</strong> ' . escape($_GET['error']);
        echo '<span class="close" onclick="this.parentElement.style.display=\'none\'">&times;</span>';
        echo '</div>';
    }
    
    // Jika menggunakan session (opsional)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">';
        echo '<strong>Sukses!</strong> ' . escape($_SESSION['success_message']);
        echo '<span class="close" onclick="this.parentElement.style.display=\'none\'">&times;</span>';
        echo '</div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-error">';
        echo '<strong>Error!</strong> ' . escape($_SESSION['error_message']);
        echo '<span class="close" onclick="this.parentElement.style.display=\'none\'">&times;</span>';
        echo '</div>';
        unset($_SESSION['error_message']);
    }
}

/**
 * Fungsi untuk set pesan success ke session
 */
function setSuccessMessage($message) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['success_message'] = $message;
}

/**
 * Fungsi untuk set pesan error ke session
 */
function setErrorMessage($message) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['error_message'] = $message;
}

/**
 * Fungsi untuk validasi NIM
 * Pastikan NIM sesuai format yang diinginkan
 */
function validateNIM($nim) {
    // Contoh validasi: NIM harus berupa angka dan panjang tertentu
    if (empty($nim)) {
        return "NIM tidak boleh kosong";
    }
    
    if (!is_numeric($nim)) {
        return "NIM harus berupa angka";
    }
    
    if (strlen($nim) < 8 || strlen($nim) > 15) {
        return "NIM harus memiliki panjang 8-15 digit";
    }
    
    return true; // Valid
}

/**
 * Fungsi untuk validasi nama
 */
function validateName($name) {
    if (empty(trim($name))) {
        return "Nama tidak boleh kosong";
    }
    
    if (strlen(trim($name)) < 2) {
        return "Nama minimal 2 karakter";
    }
    
    if (strlen(trim($name)) > 100) {
        return "Nama maksimal 100 karakter";
    }
    
    return true; // Valid
}

/**
 * Fungsi untuk membersihkan input
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Fungsi untuk redirect dengan pesan
 */
function redirectWithMessage($url, $message, $type = 'message') {
    $separator = (strpos($url, '?') !== false) ? '&' : '?';
    header("Location: {$url}{$separator}{$type}=" . urlencode($message));
    exit;
}

/**
 * Fungsi untuk format tanggal Indonesia
 */
function formatTanggalIndonesia($date) {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $hari = date('d', $timestamp);
    $bulanIndex = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return $hari . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
}

/**
 * Fungsi untuk generate pagination links
 */
function generatePagination($currentPage, $totalPages, $baseUrl, $searchParam = '') {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $prevPage . $searchParam . '">&laquo; Sebelumnya</a>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=1' . $searchParam . '">1</a>';
        if ($startPage > 2) {
            $html .= '<span>...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="current">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . $searchParam . '">' . $i . '</a>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<span>...</span>';
        }
        $html .= '<a href="' . $baseUrl . '?page=' . $totalPages . $searchParam . '">' . $totalPages . '</a>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $nextPage . $searchParam . '">Selanjutnya &raquo;</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Fungsi untuk log aktivitas (opsional)
 */
function logActivity($action, $description, $user_id = null) {
    // Implementasi logging jika diperlukan
    // Bisa disimpan ke database atau file log
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'description' => $description,
        'user_id' => $user_id,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    // Contoh: simpan ke file log
    $log_string = json_encode($log_entry) . "\n";
    file_put_contents(__DIR__ . '/../logs/activity.log', $log_string, FILE_APPEND | LOCK_EX);
}

/**
 * Fungsi untuk cek apakah data sudah ada
 */
function isDataExists($conn, $table, $column, $value, $excludeId = null) {
    try {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value";
        $params = [':value' => $value];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    } catch (PDOException $e) {
        return false;
    }
}
?>