<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
if(!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }

// Get statistics - Gunakan tabel reports
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
FROM reports";
$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : ['total' => 0, 'menunggu' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0];

// Pastikan nilai tidak null
$stats['total'] = $stats['total'] ?? 0;
$stats['menunggu'] = $stats['menunggu'] ?? 0;
$stats['diproses'] = $stats['diproses'] ?? 0;
$stats['selesai'] = $stats['selesai'] ?? 0;
$stats['ditolak'] = $stats['ditolak'] ?? 0;

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori'] ?? 'all';

$kategori_list = ['Infrastruktur', 'Sosial', 'Kebersihan', 'Kesehatan', 'Keamanan', 'Saran atau Aspirasi Masyarakat'];
$kategori_icons = [
  'Infrastruktur' => '🗂️', 'Sosial' => '📚', 'Kebersihan' => '🌳',
  'Kesehatan' => '🏥', 'Keamanan' => '🚨', 'Saran atau Aspirasi Masyarakat' => '📌',
];

// Build query - Gunakan tabel reports
$query = 'SELECT id, report_id, kategori, status, tanggal, nama FROM reports WHERE 1=1';
$params = [];
$types = '';

if($filter !== 'all') {
    $query .= ' AND status = ?';
    $params[] = $filter;
    $types .= 's';
}

if($kategori_filter !== 'all') {
    $query .= ' AND kategori = ?';
    $params[] = $kategori_filter;
    $types .= 's';
}

if($search) {
    $query .= ' AND (report_id LIKE ? OR kategori LIKE ? OR nama LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

$query .= ' ORDER BY tanggal DESC LIMIT 50';

if(count($params) > 0) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query($query);
}

$current_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - Desa Posi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="admin_styles.css" rel="stylesheet">
  <style>
    .filter-tabs .filter-btn {
      flex: 1 1 auto;
      min-width: 0;
      text-align: center;
      white-space: nowrap;
    }
    .filter-tabs .d-flex {
      overflow: visible !important;
    }
    .stats-card-clickable {
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stats-card-clickable:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important;
    }
    .stats-card-active {
      border: 2px solid #0ea5e9 !important;
      box-shadow: 0 4px 16px rgba(14,165,233,0.2) !important;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="admin-navbar">
  <div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link d-lg-none text-white" type="button" onclick="toggleSidebar()">
          ☰
        </button>
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
          <img src="luwu.png" style="height: 35px;" class="me-2">
          <strong>Admin Dashboard</strong>
        </a>
      </div>
      
      <div class="d-flex gap-2 align-items-center">
        <a href="../public/index.php" class="btn btn-outline-primary btn-sm" target="_blank">
          Lihat Website
        </a>
        <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin logout?')">
          Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <ul class="sidebar-menu">
    <li class="sidebar-item">
      <a href="dashboard.php" class="sidebar-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
        Dashboard
      </a>
    </li>
    <li class="sidebar-item">
      <a href="data_penduduk.php" class="sidebar-link <?php echo $current_page === 'penduduk' ? 'active' : ''; ?>">
        Data Penduduk
      </a>
    </li>
    <li class="sidebar-item">
      <a href="edit_about.php" class="sidebar-link <?php echo $current_page === 'about' ? 'active' : ''; ?>">
        Tentang
      </a>
    </li>
    <li class="sidebar-item">
      <a href="edit_contact.php" class="sidebar-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>">
        Kontak
      </a>
    </li>
  </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
  <div class="container-fluid px-4 pb-5">
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-2 col-4">
        <a href="dashboard.php" class="text-decoration-none">
        <div class="stats-card stats-card-clickable">
          <div class="stats-icon" style="background: linear-gradient(135deg, #667eea15, #764ba215);">
            📊
          </div>
          <div class="stats-number" style="color: var(--primary);">
            <?php echo $stats['total']; ?>
          </div>
          <div class="stats-label">Total Laporan</div>
        </div>
        </a>
      </div>
      
      <div class="col-md-2 col-4">
        <a href="dashboard.php?filter=menunggu" class="text-decoration-none">
        <div class="stats-card stats-card-clickable <?php echo $filter === 'menunggu' ? 'stats-card-active' : ''; ?>">
          <div class="stats-icon" style="background: #fef3c7;">
            ⏳
          </div>
          <div class="stats-number" style="color: var(--warning);">
            <?php echo $stats['menunggu']; ?>
          </div>
          <div class="stats-label">Menunggu</div>
        </div>
        </a>
      </div>
      
      <div class="col-md-2 col-4">
        <a href="dashboard.php?filter=diproses" class="text-decoration-none">
        <div class="stats-card stats-card-clickable <?php echo $filter === 'diproses' ? 'stats-card-active' : ''; ?>">
          <div class="stats-icon" style="background: #dbeafe;">
            🔄
          </div>
          <div class="stats-number" style="color: var(--info);">
            <?php echo $stats['diproses']; ?>
          </div>
          <div class="stats-label">Diproses</div>
        </div>
        </a>
      </div>
      
      <div class="col-md-2 col-4">
        <a href="dashboard.php?filter=selesai" class="text-decoration-none">
        <div class="stats-card stats-card-clickable <?php echo $filter === 'selesai' ? 'stats-card-active' : ''; ?>">
          <div class="stats-icon" style="background: #d1fae5;">
            ✅
          </div>
          <div class="stats-number" style="color: var(--success);">
            <?php echo $stats['selesai']; ?>
          </div>
          <div class="stats-label">Selesai</div>
        </div>
        </a>
      </div>

      <div class="col-md-2 col-4">
        <a href="dashboard.php?filter=ditolak" class="text-decoration-none">
        <div class="stats-card stats-card-clickable <?php echo $filter === 'ditolak' ? 'stats-card-active' : ''; ?>">
          <div class="stats-icon" style="background: #fee2e2;">
            ❌
          </div>
          <div class="stats-number" style="color: #ef4444;">
            <?php echo $stats['ditolak']; ?>
          </div>
          <div class="stats-label">Ditolak</div>
        </div>
        </a>
      </div>
    </div>
    
    <!-- Search & Filter -->
    <div class="row mb-4">
      <!-- Search Box -->
      <div class="col-md-8 mb-6 mb-md-0">
        <form method="GET" action="dashboard.php" class="search-box">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari Report ID,Kategori, Nama..." value="<?php echo htmlspecialchars($search);?>">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_filter); ?>">
            <button class="btn" type="submit">Cari</button>
          </div>
        </form>
      </div>

      <!-- Dropdown Kategori -->
      <div class="col-md-4 mb-4 mb-md-0">
        <?php
          $kat_label = $kategori_filter === 'all' ? '🗃️ Semua Kategori' : ($kategori_icons[$kategori_filter] . ' ' . $kategori_filter);
          function buildKatUrl($filter, $search, $kat) {
            $p = ['filter' => $filter, 'search' => $search, 'kategori' => $kat];
            $q = http_build_query(array_filter($p, fn($v) => $v !== '' && $v !== 'all' || $v === 'all' && $p['filter'] !== 'all'));
            // rebuild cleanly
            $parts = [];
            if ($filter !== 'all') $parts[] = 'filter=' . urlencode($filter);
            if ($search)           $parts[] = 'search=' . urlencode($search);
            if ($kat !== 'all')    $parts[] = 'kategori=' . urlencode($kat);
            return 'dashboard.php' . ($parts ? '?' . implode('&', $parts) : '');
          }
        ?>
        <div class="dropdown w-90">
          <button class="btn btn-white dropdown-toggle w-100 text-start d-flex align-items-center justify-content-between"
                  type="button" data-bs-toggle="dropdown" aria-expanded="false"
                  style="background:#fff; border:1.5px solid <?php echo $kategori_filter !== 'all' ? '#0ea5e9' : '#d1d5db'; ?>; border-radius:9px; padding:9px 14px; font-size:16px; color:<?php echo $kategori_filter !== 'all' ? '#0ea5e9' : '#444'; ?>; font-weight:<?php echo $kategori_filter !== 'all' ? '600' : '400'; ?>;">
            <span><?php echo $kat_label; ?></span>
          </button>
          <ul class="dropdown-menu w-90 shadow-sm" style="border-radius:10px; border:0.5px solid #e5e7eb; padding:6px; margin-top:4px;">
            <li>
              <a class="dropdown-item d-flex align-items-center gap-2 <?php echo $kategori_filter === 'all' ? 'active' : ''; ?>"
                 href="<?php echo buildKatUrl($filter, $search, 'all'); ?>"
                 style="border-radius:7px; font: size 16px; padding:8px 12px;">
                🗃️ <span>Semua Kategori</span>
              </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <?php foreach($kategori_list as $kat): ?>
            <li>
              <a class="dropdown-item d-flex align-items-center gap-2 <?php echo $kategori_filter === $kat ? 'active' : ''; ?>"
                 href="<?php echo buildKatUrl($filter, $search, $kat); ?>"
                 style="border-radius:6px; font-size:14px; padding:8px 12px;">
                <?php echo $kategori_icons[$kat]; ?> <span><?php echo $kat; ?></span>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div><!-- end row Search & Filter -->

    <!-- Reports List -->
    <?php if($res && $res->num_rows > 0): ?>
      <?php while($row = $res->fetch_assoc()): ?>
        <div class="report-card">
          <div class="row align-items-center">
            <div class="col-md-8 mb-4 mb-md-0">
              <div class="d-flex align-items-start">
                <div class="me-3" style="font-size: 2rem;">
                  <?php 
                    $icons = [
                      'Infrastruktur' => '🗂️',
                      'Sosial' => '📚',
                      'Kebersihan' => '🌳',
                      'Kesehatan' => '🏥',
                      'Keamanan' => '🚨',
                      'Saran atau Aspirasi Masyarakat' => '📌'
                    ];
                    echo $icons[$row['kategori']] ?? '📋';
                  ?>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-bold"><?php echo esc($row['kategori'] ?: 'Tidak Berkategori'); ?></h6>
                  <div class="text-muted small mb-2">
                    <span class="me-3">🆔 <?php echo esc($row['report_id']); ?></span>
                    <span class="me-3">👤 <?php echo esc($row['nama'] ?: 'Anonim'); ?></span>
                    <span>📅 <?php echo date('d M Y H:i', strtotime($row['created_at'] ?? $row['tanggal'])); ?></span>
                  </div>
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
            </div>
            <div class="col-md-4 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
              <a href="view.php?report_id=<?php echo urlencode($row['report_id']); ?>" 
                 class="btn btn-primary btn-action">
                📄 Lihat Detail
              </a>
              <a href="print_laporan.php?report_id=<?php echo urlencode($row['report_id']); ?>" 
                 target="_blank"
                 class="btn btn-action btn-print-laporan">
                🖨️ Print
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">🔭</div>
        <h5 class="mb-2">Tidak Ada Laporan</h5>
        <p class="text-muted mb-0">
          <?php if($search): ?>
            Tidak ditemukan laporan dengan kata kunci "<?php echo htmlspecialchars($search); ?>"
          <?php else: ?>
            Belum ada laporan masuk saat ini
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}
</script>
</body>
</html>