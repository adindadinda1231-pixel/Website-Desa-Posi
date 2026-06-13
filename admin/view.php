<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
if(!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }

$report_id = $_GET['report_id'] ?? ''; 
if(!$report_id) { header('Location: dashboard.php'); exit; }

// Gunakan tabel reports
$stmt = $conn->prepare('SELECT * FROM reports WHERE report_id = ? LIMIT 1'); 
$stmt->bind_param('s',$report_id); 
$stmt->execute(); 
$res=$stmt->get_result();

if($res->num_rows===0) { 
    echo 'Laporan tidak ditemukan'; 
    exit; 
}

$row = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Laporan - <?php echo esc($row['report_id']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="admin_styles.css" rel="stylesheet">
  <style>
    /* Fix untuk navbar yang menutupi konten */
    body {
      padding-top: 80px; /* Sesuaikan dengan tinggi navbar */
    }
    
    .admin-navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px 0;
    }
    
    /* Tambahan styling untuk container */
    .container {
      margin-top: 30px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="admin-navbar">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <a href="dashboard.php" class="text-decoration-none" style="color: var(--primary); font-weight: 700; font-size: 1.2rem;">
          ← Kembali ke Dashboard
        </a>
      </div>
      <div>
        <span class="text-muted">Admin Panel</span>
      </div>
    </div>
  </div>
</nav>

<div class="container pb-5">
  <div class="row">
    
    <!-- Left Column - Report Details -->
    <div class="col-lg-8">
      <div class="main-card">
        
        <!-- Report Header -->
        <div class="report-header">
          <div class="report-id-badge">
            🆔 <?php echo esc($row['report_id']); ?>
          </div>
          
          <div class="d-flex align-items-center mb-3">
            <div class="category-icon">
              <?php 
                $icons = [
                  'Infrastruktur' => '🏗️',
                  'Kesehatan' => '🏥',
                  'Keamanan' => '🚨',
                  'Pendidikan' => '📚',
                  'Lingkungan' => '🌳',
                  'Lainnya' => '📌'
                ];
                echo $icons[$row['kategori']] ?? '📋';
              ?>
            </div>
            <div>
              <h4 class="mb-1 fw-bold"><?php echo esc($row['kategori'] ?: 'Tidak Berkategori'); ?></h4>
              <p class="text-muted mb-0">
                📅 <?php echo date('d F Y, H:i', strtotime($row['tanggal'] ?? $row['created_at'])); ?> WIB
              </p>
            </div>
          </div>
          
          <div>
            <span class="status-badge status-<?php echo str_replace(' ', '', esc($row['status'])); ?>">
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
                echo ($status_icons[$row['status']] ?? '') . ' ' . ($status_labels[$row['status']] ?? $row['status']); 
              ?>
            </span>
          </div>
        </div>

        <!-- Report Information -->
        <div class="info-row">
          <div class="info-label">👤 Pelapor</div>
          <div class="info-value"><?php echo esc($row['nama'] ?: 'Anonim'); ?></div>
        </div>
        
        <div class="info-row">
          <div class="info-label">📱 Kontak</div>
          <div class="info-value"><?php echo esc($row['kontak'] ?: '-'); ?></div>
        </div>

        <!-- Report Content -->
        <h6 class="mt-4 mb-3 fw-bold">📄 Isi Laporan</h6>
        <div class="report-content">
          <?php echo nl2br(esc($row['isi'])); ?>
        </div>

        <!-- Report Image -->
        <?php if($row['foto']): ?>
          <h6 class="mt-4 mb-3 fw-bold">📸 Foto Bukti</h6>
          <img src="/public/uploads/<?php echo esc($row['foto']); ?>" 
               class="report-image" 
               alt="Foto Laporan">
        <?php endif; ?>

        <?php if(isset($row['note_admin']) && $row['note_admin']): ?>
          <div class="alert alert-info mt-4" style="border-radius: 15px; border: none;">
            <h6 class="alert-heading">📝 Catatan Admin</h6>
            <p class="mb-0"><?php echo nl2br(esc($row['note_admin'])); ?></p>
          </div>
        <?php endif; ?>

      </div>
    </div>
    <!-- END Left Column -->

    <!-- Right Column - Action Forms -->
    <div class="col-lg-4">
      
      <!-- Update Status Form -->
      <div class="form-card">
        <h5>🔄 Update Status</h5>
        
        <form method="POST" action="update_status.php">
          <input type="hidden" name="report_id" value="<?php echo esc($row['report_id']); ?>">
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Status Laporan</label>
            <select name="status" class="form-select" required>
              <option value="menunggu" <?php if($row['status']=='menunggu') echo 'selected'; ?>>
                ⏳ Menunggu
              </option>
              <option value="diproses" <?php if($row['status']=='diproses') echo 'selected'; ?>>
                🔄 Diproses
              </option>
              <option value="selesai" <?php if($row['status']=='selesai') echo 'selected'; ?>>
                ✅ Selesai
              </option>
              <option value="ditolak" <?php if($row['status']=='ditolak') echo 'selected'; ?>>
                ❌ Ditolak
              </option>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Catatan/Keterangan</label>
            <textarea name="note" class="form-control" rows="4" 
                      placeholder="Tambahkan catatan untuk pelapor..."><?php echo esc($row['note_admin'] ?? ''); ?></textarea>
          </div>
          
          <button type="submit" class="btn btn-primary btn-action w-100">
            💾 Simpan Perubahan
          </button>
        </form>
      </div>    
  </div>

  <!-- Full Width Row for System Info -->
  <div class="row mt-3">
    <div class="col-12">
      <div class="form-card">
        <h5>ℹ️ Informasi Sistem</h5>
        <div class="row">
          <div class="col-md-3">
            <div class="small text-muted">
              <p class="mb-0">
                <strong>Dibuat:</strong><br>
                <?php echo date('d F Y, H:i:s', strtotime($row['tanggal'] ?? $row['created_at'])); ?>
              </p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="small text-muted">
              <p class="mb-0">
                <strong>Update Terakhir:</strong><br>
                <?php echo isset($row['updated_at']) ? date('d F Y, H:i:s', strtotime($row['updated_at'])) : '-'; ?>
              </p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="small text-muted">
              <p class="mb-0">
                <strong>ID Laporan:</strong><br>
                <?php echo esc($row['report_id']); ?>
              </p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="small text-muted">
              <p class="mb-0">
                <strong>Status Saat Ini:</strong><br>
                <?php 
                  $status_display = [
                    'menunggu' => 'Menunggu',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak'
                  ];
                  echo $status_display[$row['status']] ?? $row['status'];
                ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>