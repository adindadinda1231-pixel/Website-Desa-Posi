<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';

header('Content-Type: application/json');

$report_id = $_GET['id'] ?? '';

if(empty($report_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Report ID tidak boleh kosong'
    ]);
    exit;
}

// Cari di tabel reports
$stmt = $conn->prepare('SELECT report_id, kategori, status, tanggal, nama, is_anonim FROM reports WHERE report_id = ? LIMIT 1');
$stmt->bind_param('s', $report_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Laporan tidak ditemukan. Pastikan Report ID benar.'
    ]);
    exit;
}

$row = $result->fetch_assoc();

// Format tanggal
$tanggal_format = date('d F Y, H:i', strtotime($row['tanggal']));

// Status labels
$status_labels = [
    'menunggu' => 'Menunggu Verifikasi',
    'diproses' => 'Sedang Diproses',
    'selesai' => 'Selesai Ditangani',
    'ditolak' => 'Ditolak'
];

echo json_encode([
    'success' => true,
    'report_id' => $row['report_id'],
    'kategori' => $row['kategori'],
    'status' => $row['status'],
    'status_label' => $status_labels[$row['status']] ?? $row['status'],
    'tanggal' => $tanggal_format,
    'pelapor' => $row['is_anonim'] ? 'Anonim' : ($row['nama'] ?: 'Anonim')
]);
?>