<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();

if(!isset($_SESSION['user_id'])){ 
    header('Location: login.php'); 
    exit; 
}

$uid = $_SESSION['user_id'];

// Gunakan tabel reports
$stmt = $conn->prepare('SELECT * FROM reports WHERE user_id = ? ORDER BY tanggal DESC');
$stmt->bind_param('i', $uid); 
$stmt->execute(); 
$res = $stmt->get_result();

// Hitung statistik user
$stats_query = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
FROM reports WHERE user_id = ?");
$stats_query->bind_param('i', $uid);
$stats_query->execute();
$stats = $stats_query->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Laporan Saya - Desa Posi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
    <link href="assets/myreports.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="sidebar-toggle" onclick="toggleSidebar()">
  <span>☰</span>
</button>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <img src="assets/luwu.png" alt="Logo">
    <h5>Desa Posi</h5>
    <small style="color: rgba(255,255,255,0.7);">Sistem Pengaduan</small>
  </div>
  
  <ul class="sidebar-menu">
    <li>
      <a href="login_masyarakat.php">
        <span>Pengaduan</span>
      </a>
    </li>
    <li>
      <a href="myreports.php" class="active">
        <span>Riwayat</span>
      </a>
    </li>
    <li>
      <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">
        <span>Logout</span>
      </a>
    </li>
  </ul>

  <div class="sidebar-footer">
    <div class="user-info-sidebar">
      <div style="font-size: 30px; margin-bottom: 8px;">👤</div>
      <div style="font-weight: 600;">Akun Anda</div>
      <small>User ID: <?php echo htmlspecialchars($uid); ?></small>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="main-content-with-sidebar">

<div class="container-with-sidebar">
    
    <!-- Header -->
    <div class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0" style="color: var(--primary); font-weight: 700;">
                    📋 Riwayat Laporan Saya
                </h4>
                <p class="text-muted small mb-0">Pantau status semua laporan Anda</p>
            </div>
            <a href="login_masyarakat.php" class="btn btn-outline-custom btn-custom">
                ← Buat Laporan Baru
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea15, #764ba215);">
                📊
            </div>
            <div class="stat-number" style="color: var(--primary);">
                <?php echo $stats['total']; ?>
            </div>
            <div class="stat-label">Total Laporan</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                ⏳
            </div>
            <div class="stat-number" style="color: var(--warning);">
                <?php echo $stats['menunggu']; ?>
            </div>
            <div class="stat-label">Menunggu</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe);">
                🔄
            </div>
            <div class="stat-number" style="color: var(--info);">
                <?php echo $stats['diproses']; ?>
            </div>
            <div class="stat-label">Diproses</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                ✅
            </div>
            <div class="stat-number" style="color: var(--success);">
                <?php echo $stats['selesai']; ?>
            </div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>

    <!-- Reports List -->
    <?php if($res->num_rows > 0): ?>
        <?php $no = 1; while($r = $res->fetch_assoc()): ?>
            <div class="report-card" 
                 style="animation-delay: <?php echo $no * 0.1; ?>s;"
                 id="card-<?php echo $r['id']; ?>"
                 data-report-id="<?php echo esc($r['report_id']); ?>"
                 data-kategori="<?php echo esc($r['kategori'] ?: 'Tidak Berkategori'); ?>"
                 data-status="<?php echo esc($status_labels[$r['status']] ?? $r['status']); ?>"
                 data-tanggal="<?php echo date('d F Y', strtotime($r['tanggal'])); ?>"
                 data-updated="<?php echo $r['updated_at'] ? date('d F Y', strtotime($r['updated_at'])) : date('d F Y', strtotime($r['tanggal'])); ?>"
                 data-isi="<?php echo esc(addslashes($r['isi'])); ?>"
                 data-nama="<?php echo esc($_SESSION['nama_lengkap'] ?? 'Warga Desa Posi'); ?>"
                 data-nik="<?php echo esc($_SESSION['nik'] ?? '-'); ?>">
                <div class="row align-items-center">
                    <div class="col-lg-9 mb-3 mb-lg-0">
                        <div class="d-flex align-items-start">
                            <div class="category-icon me-3">
                                <?php 
                                    $icons = [
                                        'Infrastruktur' => '🏗️',
                                        'Kesehatan' => '🏥',
                                        'Keamanan' => '🚨',
                                        'Sosial' => '📚',
                                        'Kebersihan' => '🌳',
                                        'Saran atau Aspirasi' => '📌'
                                    ];
                                    echo $icons[$r['kategori']] ?? '📋';
                                ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h6 class="mb-0 fw-bold"><?php echo esc($r['kategori'] ?: 'Tidak Berkategori'); ?></h6>
                                    <span class="status-badge status-<?php echo $r['status']; ?>">
                                        <?php 
                                            $status_icons = [
                                                'menunggu' => '⏳',
                                                'diproses' => '🔄',
                                                'selesai' => '✅',
                                                'ditolak' => '❌'
                                            ];
                                            $status_labels = [
                                                'menunggu' => 'Menunggu',
                                                'diproses' => 'Diproses',
                                                'selesai' => 'Selesai',
                                                'ditolak' => 'Ditolak'
                                            ];
                                            echo ($status_icons[$r['status']] ?? '') . ' ' . ($status_labels[$r['status']] ?? $r['status']); 
                                        ?>
                                    </span>
                                </div>
                                <div class="text-muted small mb-2">
                                    <span class="me-3">🆔 <?php echo esc($r['report_id']); ?></span>
                                    <span>📅 <?php echo date('d M Y, H:i', strtotime($r['tanggal'])); ?> WIB</span>
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                    <?php 
                                        $preview = esc($r['isi']);
                                        echo strlen($preview) > 150 ? substr($preview, 0, 150) . '...' : $preview; 
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 text-lg-end">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <button class="btn btn-primary-custom btn-custom btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal<?php echo $r['id']; ?>">
                                Lihat Detail
                            </button>
                            <button class="btn btn-print-custom btn-custom btn-sm"
                                    onclick="printLaporan(<?php echo $r['id']; ?>)">
                                🖨️ Print
                            </button>
                            <?php if($r['foto']): ?>
                                <button class="btn btn-outline-custom btn-custom btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#photoModal<?php echo $r['id']; ?>">
                                    Lihat Foto
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Modal -->
            <div class="modal fade" id="detailModal<?php echo $r['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title mb-1">📄 Detail Laporan</h5>
                                <small class="text-muted"><?php echo esc($r['report_id']); ?></small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <strong class="d-block mb-2">📂 Kategori</strong>
                                <span class="badge" style="background: linear-gradient(135deg, #667eea15, #764ba215); color: var(--primary); padding: 0.5rem 1rem; font-size: 0.95rem;">
                                    <?php echo esc($r['kategori']); ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="d-block mb-2">📊 Status</strong>
                                <span class="status-badge status-<?php echo $r['status']; ?>">
                                    <?php 
                                        echo ($status_icons[$r['status']] ?? '') . ' ' . ($status_labels[$r['status']] ?? $r['status']); 
                                    ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="d-block mb-2">📝 Isi Laporan</strong>
                                <div class="note-box">
                                    <?php echo nl2br(esc($r['isi'])); ?>
                                </div>
                            </div>
                            
                            <?php if($r['note_admin']): ?>
                                <div class="mb-3">
                                    <strong class="d-block mb-2">💬 Tanggapan Admin</strong>
                                    <div class="alert alert-info" style="border-radius: 12px; border: none;">
                                        <?php echo nl2br(esc($r['note_admin'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="timeline-item">
                                <small class="text-muted">
                                    <strong>Dibuat:</strong> <?php echo date('d F Y, H:i:s', strtotime($r['tanggal'])); ?>
                                </small>
                            </div>
                            
                            <?php if($r['updated_at']): ?>
                                <div class="timeline-item">
                                    <small class="text-muted">
                                        <strong>Update Terakhir:</strong> <?php echo date('d F Y, H:i:s', strtotime($r['updated_at'])); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photo Modal -->
            <?php if($r['foto']): ?>
            <div class="modal fade" id="photoModal<?php echo $r['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">📸 Foto Bukti</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-4">
                            <img src="uploads/<?php echo esc($r['foto']); ?>" 
                                 class="img-fluid" 
                                 style="border-radius: 15px; max-height: 70vh; box-shadow: 0 8px 30px rgba(0,0,0,0.15);" 
                                 alt="Foto Laporan">
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <?php $no++; endwhile; ?>
        
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🔭</div>
            <h4 class="mb-3" style="color: #1e293b;">Belum Ada Laporan</h4>
            <p class="text-muted mb-4" style="font-size: 1.1rem;">
                Anda belum pernah mengirim laporan.<br>
                Mulai laporkan masalah yang Anda temukan di lingkungan Anda.
            </p>
            <a href="login_masyarakat.php" class="btn btn-primary-custom btn-custom btn-lg">
                📝 Buat Laporan Pertama
            </a>
        </div>
    <?php endif; ?>
</div>

</div>
<!-- End Main Content with Sidebar -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title text-muted">🖨️ Preview Print Laporan</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="printArea"><!-- diisi JS --></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Kembali</button>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Sekarang</button>
      </div>
    </div>
  </div>
</div>

<script>
// Toggle Sidebar (Mobile)
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  sidebar.classList.toggle('active');
  overlay.classList.toggle('active');
}

// Print Laporan
function printLaporan(id) {
  const card = document.getElementById('card-' + id);
  const reportId  = card.dataset.reportId;
  const kategori  = card.dataset.kategori;
  const status    = card.dataset.status;
  const tanggal   = card.dataset.tanggal;
  const updated   = card.dataset.updated;
  const isi       = card.dataset.isi.replace(/\n/g, '<br>');
  const nama      = card.dataset.nama;
  const nik       = card.dataset.nik;

  document.getElementById('printArea').innerHTML = `
    <div id="printContent">
      <div class="kop">
        <img src="assets/luwu.png" alt="Logo" onerror="this.style.display='none'">
        <div class="kop-text">
          <div class="desa">Desa Posi</div>
          <div class="sub">Kecamatan Bua</div>
          <div class="sub">Kabupaten Luwu</div>
          <div class="info">Kode Pos: 91991 &nbsp;|&nbsp; Tlp. 0823447484 &nbsp;|&nbsp; Gmail. desaposi@gmail.com</div>
        </div>
      </div>
      <div class="doc-title"><h2>Detail Laporan Pengaduan</h2></div>
      <table class="field-table">
        <tr><td>Kode Laporan</td><td>:</td><td>${reportId}</td></tr>
        <tr><td>Nama</td><td>:</td><td>${nama}</td></tr>
        <tr><td>NIK</td><td>:</td><td>${nik}</td></tr>
        <tr><td>Kategori</td><td>:</td><td>${kategori}</td></tr>
        <tr><td>Status</td><td>:</td><td>${status}</td></tr>
        <tr>
          <td style="padding-top:12px;vertical-align:top;">Isi Laporan</td>
          <td style="padding-top:12px;vertical-align:top;">:</td>
          <td style="padding-top:12px;">
            <div class="isi-box">${isi}</div>
          </td>
        </tr>
      </table>
      <div class="meta-section">
        <div class="meta-item"><span class="meta-label">Dibuat</span><span>: ${tanggal}</span></div>
        <div class="meta-item"><span class="meta-label">Update Terakhir</span><span>: ${updated}</span></div>
      </div>
    </div>
  `;

  const modal = new bootstrap.Modal(document.getElementById('printModal'));
  modal.show();
}
</script>

</body>
</html>