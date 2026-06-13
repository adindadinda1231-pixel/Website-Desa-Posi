<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';

// Hanya admin yang bisa mengakses
if(!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }

$report_id = $_GET['report_id'] ?? '';
if(empty($report_id)){
    die('Report ID tidak valid.');
}

// Ambil data laporan beserta info user
$stmt = $conn->prepare('
    SELECT r.*, u.nama_lengkap, u.nomor_induk_kependudukan
    FROM reports r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.report_id = ?
    LIMIT 1
');
$stmt->bind_param('s', $report_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows === 0){
    die('Laporan tidak ditemukan.');
}

$laporan = $res->fetch_assoc();

// Format tanggal
$tgl_dibuat    = date('d F Y', strtotime($laporan['tanggal'] ?? $laporan['created_at']));
$tgl_update    = isset($laporan['updated_at']) && $laporan['updated_at']
                    ? date('d F Y', strtotime($laporan['updated_at']))
                    : $tgl_dibuat;

// Label status
$status_labels = [
    'menunggu' => 'Menunggu',
    'diproses' => 'Diproses',
    'selesai'  => 'Selesai',
    'ditolak'  => 'Ditolak',
];
$status_text = $status_labels[$laporan['status']] ?? ucfirst($laporan['status']);

// Nama & NIK — fallback ke kolom di tabel reports jika ada
$nama = $laporan['nama_lengkap'] ?? $laporan['nama'] ?? 'Tidak diketahui';
$nik  = $laporan['nomor_induk_kependudukan'] ?? $laporan['nik'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Laporan - <?php echo esc($laporan['report_id']); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }

        /* ===== Kontrol tombol (tidak ikut terprint) ===== */
        .print-controls {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .btn-ctrl {
            padding: 10px 28px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-print {
            background: #2563eb;
            color: white;
        }

        .btn-print:hover { background: #1d4ed8; }

        .btn-back {
            background: white;
            color: #374151;
            border: 2px solid #d1d5db;
        }

        .btn-back:hover { background: #f9fafb; }

        /* ===== Kertas ===== */
        .paper {
            width: 210mm;
            min-height: 297mm;
            background: white;
            padding: 20mm 20mm 20mm 25mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
        }

        /* ===== Kop Surat ===== */
        .kop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding-bottom: 12px;
            border-bottom: 3px solid #1e293b;
            margin-bottom: 18px;
        }

        .kop img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text .desa {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-text .sub {
            font-size: 13pt;
            font-weight: bold;
        }

        .kop-text .info {
            font-size: 9pt;
            margin-top: 4px;
            color: #374151;
        }

        /* ===== Judul Dokumen ===== */
        .doc-title {
            text-align: center;
            margin: 18px 0 20px;
        }

        .doc-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-underline-offset: 4px;
            letter-spacing: 0.5px;
        }

        /* ===== Isi Laporan ===== */
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .field-table tr td {
            padding: 7px 6px;
            vertical-align: top;
            font-size: 12pt;
        }

        .field-table tr td:first-child {
            width: 38%;
            font-weight: normal;
        }

        .field-table tr td:nth-child(2) {
            width: 4%;
            text-align: center;
        }

        .field-table tr td:last-child {
            width: 58%;
        }

        /* ===== Kotak Isi Laporan ===== */
        .isi-laporan-box {
            background: #f8f8f8;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 12pt;
            line-height: 1.6;
            margin-top: 2px;
        }

        /* ===== Section bawah (Dibuat / Update) ===== */
        .meta-section {
            margin-top: 10px;
            padding-left: 4px;
        }

        .meta-section .meta-item {
            display: flex;
            align-items: baseline;
            gap: 6px;
            font-size: 11pt;
            margin-bottom: 6px;
        }

        .meta-section .meta-item::before {
            content: "•";
            font-size: 14pt;
            line-height: 1;
            color: #374151;
        }

        .meta-label {
            width: 160px;
            flex-shrink: 0;
        }

        /* ===== Print Media ===== */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-controls {
                display: none !important;
            }

            .paper {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 15mm 15mm 15mm 20mm;
            }

            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<!-- Tombol Kontrol -->
<div class="print-controls">
    <button class="btn-ctrl btn-back" onclick="window.close()">← Kembali</button>
    <button class="btn-ctrl btn-print" onclick="window.print()">🖨️ Print</button>
</div>

<!-- Kertas A4 -->
<div class="paper">

    <!-- Kop Surat -->
    <div class="kop">
        <img src="../public/assets/luwu.png" alt="Logo Desa" onerror="this.style.display='none'">
        <div class="kop-text">
            <div class="desa">Desa Posi</div>
            <div class="sub">Kecamatan Bua</div>
            <div class="sub">Kabupaten Luwu</div>
            <div class="info">
                Kode Pos: 91991 &nbsp;|&nbsp; Tlp. 0823447484 &nbsp;|&nbsp; Gmail. desaposi@gmail.com
            </div>
        </div>
    </div>

    <!-- Judul -->
    <div class="doc-title">
        <h2>Detail Laporan Pengaduan</h2>
    </div>

    <!-- Data Laporan -->
    <table class="field-table">
        <tr>
            <td>Kode Laporan</td>
            <td>:</td>
            <td><?php echo esc($laporan['report_id']); ?></td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><?php echo esc($nama); ?></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td><?php echo esc($nik); ?></td>
        </tr>
        <tr>
            <td>Kategori</td>
            <td>:</td>
            <td><?php echo esc($laporan['kategori'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td><?php echo esc($status_text); ?></td>
        </tr>
        <tr>
            <td style="padding-top: 12px;">Isi Laporan</td>
            <td style="padding-top: 12px;">:</td>
            <td style="padding-top: 12px;">
                <div class="isi-laporan-box">
                    <?php echo nl2br(esc($laporan['isi_laporan'] ?? $laporan['isi'] ?? $laporan['deskripsi'] ?? '-')); ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Meta Info -->
    <div class="meta-section">
        <div class="meta-item">
            <span class="meta-label">Dibuat</span>
            <span>: <?php echo $tgl_dibuat; ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Update Terakhir</span>
            <span>: <?php echo $tgl_update; ?></span>
        </div>
    </div>

</div><!-- /paper -->

<script>
// Opsional: langsung tampilkan print dialog saat halaman dibuka
// Uncomment baris di bawah jika ingin otomatis print tanpa klik tombol
// window.onload = function() { window.print(); }
</script>

</body>
</html>