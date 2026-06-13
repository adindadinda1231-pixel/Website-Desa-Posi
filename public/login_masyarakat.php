<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$site_title = 'Sistem Pengaduan Masyarakat - Desa Posi';

// Ambil statistik dari database
$query = "SELECT 
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak,
    COUNT(*) as total
FROM reports";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    if ($stats['total'] == 0) {
        $stats = ['menunggu' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0, 'total' => 0];
    }
} else {
    $stats = ['menunggu' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0, 'total' => 0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $site_title; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
  <link href="assets/login_masyarakat.css" rel="stylesheet">
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
      <a href="login_masyarakat.php" class="active">
        <span>Pengaduan</span>
      </a>
    </li>
    <li>
      <a href="myreports.php">
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
      <small>User ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></small>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="main-content-with-sidebar">

<!-- Background Wrapper -->
<div class="main-wrapper">

<!-- Navbar (Simplified) -->
<nav class="navbar navbar-expand-lg navbar-with-sidebar">
  <div class="container">
    <span class="navbar-brand">
      <strong>Selamat Datang di Sistem Pengaduan</strong>
    </span>
    <div class="ms-auto">
      <span class="badge bg-light text-dark px-3 py-2">
        👤 User ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?>
      </span>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="container my-5">
  <div class="row g-4">
    
    <!-- Left Column - Form Laporan -->
    <div class="col-lg-7">
      <div class="card card-main" id="kirim-laporan">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-primary">
              <span>📝</span>
            </div>
            <div class="ms-3">
              <h5 class="card-title mb-0">Kirim Laporan Baru</h5>
              <p class="text-muted small mb-0">Laporkan masalah atau keluhan Anda</p>
            </div>
          </div>

          <div class="alert alert-info mb-4">
            <strong>💡 Tips:</strong> Centang "Anonim" jika tidak ingin identitas ditampilkan
          </div>

          <form action="submit.php" method="POST" enctype="multipart/form-data">
            
            <!-- Checkbox Anonim -->
            <div class="form-check form-switch mb-4 p-3" style="background: #f8f9fa; border-radius: 12px;">
              <input class="form-check-input" type="checkbox" id="anon" name="anon" value="1" onchange="toggleAnonFields()">
              <label class="form-check-label fw-semibold" for="anon">
                🕵️ Kirim sebagai Anonim
              </label>
            </div>

            <div id="identity-fields">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">👤 Nama Lengkap</label>
                  <input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama Anda">
                </div>
                <div class="col-md-6">
                  <label class="form-label">📱 Kontak (Email/HP)</label>
                  <input type="text" class="form-control" name="kontak" id="kontak" placeholder="Email atau No. HP">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">📂 Kategori Laporan</label>
              <select class="form-select" name="kategori">
                <option value="">-- Pilih Kategori --</option>
                <option value="Infrastruktur">🏗️ Infrastruktur (Jalan, Jembatan, dll)</option>
                <option value="Kesehatan">🏥 Kesehatan & Kebersihan</option>
                <option value="Keamanan">🚨 Keamanan & Ketertiban</option>
                <option value="Lingkungan">🌳 Lingkungan</option>
                <option value="Lainnya">📌 Lainnya</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">📄 Isi Laporan <span class="text-danger">*</span></label>
              <textarea class="form-control" name="isi" rows="5" required placeholder="Jelaskan masalah yang ingin Anda laporkan secara detail..."></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label">📸 Foto Bukti (Opsional)</label>
              <input class="form-control" type="file" name="foto" accept="image/*">
              <div class="form-text">Format: JPG, PNG. Maksimal 2MB</div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg">
                <span class="me-2">🚀</span> Kirim Laporan Sekarang
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Column - Statistics -->
    <div class="col-lg-5">
      
      <!-- Tracking Card -->
      <div class="card card-main mb-4" id="lacak-laporan">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-info">
              <span>🔍</span>
            </div>
            <div class="ms-3">
              <h5 class="card-title mb-0">Lacak Laporan</h5>
              <p class="text-muted small mb-0">Cek status laporan Anda</p>
            </div>
          </div>

          <form id="track-form" onsubmit="event.preventDefault(); trackReport();">
            <div class="input-group mb-3">
              <input type="text" id="report_id" class="form-control" placeholder="Contoh: REP20250001" required>
              <button class="btn btn-info text-white" type="submit">
                <span class="me-1">🔎</span> Lacak
              </button>
            </div>
          </form>

          <div id="report-info"></div>
          
          <div class="status-badge-container mt-3">
            <small class="text-muted">Status Saat Ini:</small>
            <span id="current-status" class="badge bg-secondary ms-2">Belum Dilacak</span>
          </div>
        </div>
      </div>

      <!-- Statistics Card -->
      <div class="card card-main mt-4">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-4">
            <div class="icon-box bg-warning">
              <span>📊</span>
            </div>
            <div class="ms-3">
              <h5 class="card-title mb-0">Statistik Laporan</h5>
              <p class="text-muted small mb-0">Data real-time dari sistem</p>
            </div>
          </div>

          <?php if($stats['total'] > 0): ?>
            
            <!-- Total Laporan -->
            <div class="total-reports mb-4 text-center p-3" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border-radius: 15px;">
              <div class="stats-number"><?php echo $stats['total']; ?></div>
              <small class="text-muted">Total Laporan Masuk</small>
            </div>

            <!-- Status Progress Bars -->
            <div class="stats-list">
              
              <!-- Menunggu -->
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="stat-label">⏳ Menunggu</span>
                  <strong class="text-warning"><?php echo $stats['menunggu']; ?></strong>
                </div>
                <div class="progress" style="height: 10px;">
                  <div class="progress-bar bg-warning" style="width: <?php echo ($stats['menunggu']/$stats['total'])*100; ?>%"></div>
                </div>
              </div>

              <!-- Diproses -->
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="stat-label">🔄 Diproses</span>
                  <strong class="text-primary"><?php echo $stats['diproses']; ?></strong>
                </div>
                <div class="progress" style="height: 10px;">
                  <div class="progress-bar bg-primary" style="width: <?php echo ($stats['diproses']/$stats['total'])*100; ?>%"></div>
                </div>
              </div>

              <!-- Selesai -->
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="stat-label">✅ Selesai</span>
                  <strong class="text-success"><?php echo $stats['selesai']; ?></strong>
                </div>
                <div class="progress" style="height: 10px;">
                  <div class="progress-bar bg-success" style="width: <?php echo ($stats['selesai']/$stats['total'])*100; ?>%"></div>
                </div>
              </div>

              <!-- Ditolak -->
              <div class="stat-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="stat-label">❌ Ditolak</span>
                  <strong class="text-danger"><?php echo $stats['ditolak']; ?></strong>
                </div>
                <div class="progress" style="height: 10px;">
                  <div class="progress-bar bg-danger" style="width: <?php echo ($stats['ditolak']/$stats['total'])*100; ?>%"></div>
                </div>
              </div>

            </div>

          <?php else: ?>
            <div class="text-center py-5">
              <div style="font-size: 80px; opacity: 0.3;">📊</div>
              <p class="text-muted mb-0">Belum ada laporan masuk</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</main>
</div>
<!-- End Wrapper -->

</div>
<!-- End Main Content with Sidebar -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle Anonymous Fields
function toggleAnonFields() {
  const anonCheckbox = document.getElementById('anon');
  const identityFields = document.getElementById('identity-fields');
  const namaInput = document.getElementById('nama');
  const kontakInput = document.getElementById('kontak');
  
  if (anonCheckbox.checked) {
    identityFields.style.opacity = '0.5';
    identityFields.style.pointerEvents = 'none';
    namaInput.value = '';
    kontakInput.value = '';
    namaInput.removeAttribute('required');
    kontakInput.removeAttribute('required');
  } else {
    identityFields.style.opacity = '1';
    identityFields.style.pointerEvents = 'auto';
  }
}

// Toggle Sidebar (Mobile)
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  sidebar.classList.toggle('active');
  overlay.classList.toggle('active');
}

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// Track Report Function
function trackReport() {
  const reportId = document.getElementById('report_id').value.trim();
  const infoDiv = document.getElementById('report-info');
  const statusBadge = document.getElementById('current-status');
  
  if (!reportId) {
    infoDiv.innerHTML = '<div class="alert alert-warning">⚠️ Masukkan Report ID terlebih dahulu</div>';
    return;
  }
  
  infoDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Mencari laporan...</span></div>';
  
  fetch('track_api.php?id=' + encodeURIComponent(reportId))
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const statusConfig = {
          'menunggu': { icon: '⏳', color: 'warning', text: 'Menunggu Verifikasi' },
          'diproses': { icon: '🔄', color: 'primary', text: 'Sedang Diproses' },
          'selesai': { icon: '✅', color: 'success', text: 'Selesai Ditangani' },
          'ditolak': { icon: '❌', color: 'danger', text: 'Ditolak' }
        };
        
        const config = statusConfig[data.status] || { icon: '❓', color: 'secondary', text: data.status };
        
        statusBadge.className = 'badge bg-' + config.color + ' ms-2';
        statusBadge.innerHTML = config.icon + ' ' + config.text;
        
        infoDiv.innerHTML = `
          <div class="alert alert-${config.color} alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <h6 class="alert-heading">${config.icon} Laporan Ditemukan!</h6>
            <hr>
            <p class="mb-1"><strong>Report ID:</strong> ${data.report_id}</p>
            <p class="mb-1"><strong>Kategori:</strong> ${data.kategori || '-'}</p>
            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${config.color}">${config.text}</span></p>
            <p class="mb-0"><strong>Tanggal:</strong> ${data.tanggal}</p>
          </div>
        `;
      } else {
        statusBadge.className = 'badge bg-secondary ms-2';
        statusBadge.textContent = 'Tidak Ditemukan';
        infoDiv.innerHTML = '<div class="alert alert-danger">❌ ' + data.message + '</div>';
      }
    })
    .catch(error => {
      statusBadge.className = 'badge bg-secondary ms-2';
      statusBadge.textContent = 'Error';
      infoDiv.innerHTML = '<div class="alert alert-danger">⚠️ Terjadi kesalahan. Silakan coba lagi.</div>';
      console.error('Error:', error);
    });
}
</script>

</body>
</html>