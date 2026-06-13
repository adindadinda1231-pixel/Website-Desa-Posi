<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';

// Cek login admin
if(!isset($_SESSION['admin_id'])){ 
    header('Location: login.php'); 
    exit; 
}

// Ambil report_id dari URL
$report_id = $_GET['report_id'] ?? '';

if(!$report_id) {
    $_SESSION['error_msg'] = 'Report ID tidak valid';
    header('Location: dashboard.php');
    exit;
}

// Ambil data laporan dari database
$stmt = $conn->prepare('SELECT id, foto, status FROM reports WHERE report_id = ? LIMIT 1');
$stmt->bind_param('s', $report_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    $_SESSION['error_msg'] = 'Laporan tidak ditemukan';
    header('Location: dashboard.php');
    exit;
}

$report = $result->fetch_assoc();
$stmt->close();

// Hapus foto jika ada
if($report['foto'] && !empty($report['foto'])) {
    $foto_path = __DIR__ . '/../public/uploads/' . $report['foto'];
    if(file_exists($foto_path)) {
        @unlink($foto_path);
    }
}

// Hapus laporan dari database
$delete_stmt = $conn->prepare('DELETE FROM reports WHERE id = ? LIMIT 1');
$delete_stmt->bind_param('i', $report['id']);

if($delete_stmt->execute()) {
    $_SESSION['success_msg'] = 'Laporan berhasil dihapus';
} else {
    $_SESSION['error_msg'] = 'Gagal menghapus laporan: ' . $conn->error;
}

$delete_stmt->close();
$conn->close();

// Redirect kembali ke dashboard
header('Location: dashboard.php');
exit;
?>