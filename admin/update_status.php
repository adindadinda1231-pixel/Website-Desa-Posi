<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
if(!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }
if($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: dashboard.php'); exit; }

$report_id = $_POST['report_id'] ?? ''; 
$status = $_POST['status'] ?? ''; 
$note = $_POST['note'] ?? '';

if(!$report_id || !$status) { 
    header('Location: dashboard.php'); 
    exit;
}

// Update tabel reports (kolom updated_at sudah ada di struktur)
$stmt = $conn->prepare('UPDATE reports SET status = ?, note_admin = ?, updated_at = NOW() WHERE report_id = ? LIMIT 1');
$stmt->bind_param('sss',$status,$note,$report_id); 
$stmt->execute(); 
$stmt->close();

// Log aktivitas jika tabel ada
$get = $conn->prepare('SELECT id,kategori FROM reports WHERE report_id = ? LIMIT 1'); 
$get->bind_param('s',$report_id); 
$get->execute(); 
$g=$get->get_result()->fetch_assoc();

if($g) {
    $rid = $g['id']; 
    $status_labels = [
        'menunggu' => 'Menunggu',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak'
    ];
    $msg = 'Status diubah menjadi ' . ($status_labels[$status] ?? $status) . ($note ? '. Catatan: ' . $note : '');
    
    // Cek apakah tabel report_activities ada
    $table_check = $conn->query("SHOW TABLES LIKE 'report_activities'");
    if($table_check && $table_check->num_rows > 0) {
        $ins = $conn->prepare('INSERT INTO report_activities (report_id, type, message) VALUES (?, ?, ?)'); 
        $type = 'status_update';
        $ins->bind_param('iss',$rid,$type,$msg); 
        $ins->execute(); 
        $ins->close();
    }
    
    // Kirim email jika fungsi tersedia
    if(file_exists(__DIR__ . '/../helpers/mailer.php')) {
        require_once __DIR__ . '/../helpers/mailer.php'; 
        if(function_exists('send_admin_email')) {
            send_admin_email('Update Laporan: ' . $g['kategori'], 'Status: ' . $status . "\nCatatan: " . $note);
        }
    }
}

header('Location: view.php?report_id=' . urlencode($report_id));
exit;
?>