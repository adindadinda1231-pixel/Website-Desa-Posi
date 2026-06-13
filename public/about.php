<?php
require_once __DIR__ . '/../helpers/util.php';
require_once __DIR__ . '/../config/koneksi.php';
session_start();

// Get content from database with error handling
$content = [];
try {
    $result = @$conn->query("SELECT content_data FROM page_content WHERE page_name = 'about'");
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $content = json_decode($row['content_data'], true) ?? [];
    }
} catch (Exception $e) {
    // Table doesn't exist yet, use defaults
    $content = [];
}

// Default values jika belum ada di database
$defaults = [
    'hero_title' => 'Tentang Sistem Kami',
    'hero_subtitle' => 'Mengenal lebih dekat sistem pengaduan masyarakat Desa Posi',
    'main_title' => 'Sistem Pengaduan Masyarakat',
    'main_description' => 'Platform digital untuk memudahkan warga Desa Posi, Kecamatan Bua dalam menyampaikan aspirasi, keluhan, dan laporan terkait layanan publik.',
    'info_box' => 'Sistem ini dirancang khusus untuk meningkatkan transparansi dan responsivitas pemerintah desa terhadap kebutuhan masyarakat.',
    'goal_1_title' => 'Transparansi',
    'goal_1_desc' => 'Memudahkan warga melaporkan masalah secara terbuka dan transparan',
    'goal_2_title' => 'Responsif',
    'goal_2_desc' => 'Penanganan laporan yang cepat dan terorganisir',
    'goal_3_title' => 'Akuntabilitas',
    'goal_3_desc' => 'Tracking status laporan secara real-time',
    'goal_4_title' => 'Partisipasi',
    'goal_4_desc' => 'Meningkatkan partisipasi aktif masyarakat dalam pembangunan desa',
    'category_1' => 'Infrastruktur',
    'category_1_desc' => 'Jalan rusak, jembatan, fasilitas umum',
    'category_2' => 'Sosial',
    'category_2_desc' => 'Masalah sosial kemasyarakatan',
    'category_3' => 'Kebersihan',
    'category_3_desc' => 'Kebersihan lingkungan, sampah',
    'category_4' => 'Kesehatan',
    'category_4_desc' => 'Posyandu, layanan kesehatan desa',
    'category_5' => 'Keamanan',
    'category_5_desc' => 'Gangguan ketertiban dan keamanan',
    'category_6' => 'Saran atau Aspirasi Masyarakat',
    'category_6_desc' => 'Saran dan aspirasi untuk kemajuan desa'
];

// Merge dengan defaults (database override defaults, kecuali kategori yang dipaksa dari defaults)
$content = array_merge($defaults, $content);

// Paksa kategori selalu dari defaults (tidak bisa diubah lewat database lama)
$kategori_fixed = [
    'category_1' => 'Infrastruktur',      'category_1_desc' => 'Jalan rusak, jembatan, fasilitas umum',
    'category_2' => 'Sosial',             'category_2_desc' => 'Masalah sosial kemasyarakatan',
    'category_3' => 'Kesehatan',          'category_3_desc' => 'Posyandu, layanan kesehatan desa',
    'category_4' => 'Kebersihan',         'category_4_desc' => 'Kebersihan lingkungan, sampah',
    'category_5' => 'Keamanan',           'category_5_desc' => 'Gangguan ketertiban dan keamanan',
    'category_6' => 'Saran atau Aspirasi Masyarakat', 'category_6_desc' => 'Saran dan aspirasi untuk kemajuan desa',
];
$content = array_merge($content, $kategori_fixed);

$site_title = 'Tentang - Sistem Pengaduan Desa Posi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $site_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
    <style>
    .navbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: background 0.3s, box-shadow 0.3s;
    }
  </style>
</head>
<body>

<div class="main-wrapper">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/luwu.png" style="height: 35px;" class="me-2">
      <strong>Desa Posi</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link active" href="about.php">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="myreports.php">Riwayat</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="../admin/login.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Header -->
<header class="hero-header">
  <div class="container text-center">
    <div style="font-size: 80px; margin-bottom: 20px;">ℹ️</div>
    <h1 class="display-5 fw-bold mb-3"><?php echo esc($content['hero_title']); ?></h1>
    <p class="lead mb-0">
      <?php echo esc($content['hero_subtitle']); ?>
    </p>
  </div>
</header>

<!-- Main Content -->
<main class="container my-5">
  <div class="row g-4">
    
    <!-- Main Info Card -->
    <div class="col-lg-8">
      <div class="card card-main">
        <div class="card-body p-4">
          <h4 class="fw-bold mb-4">📢 <?php echo esc($content['main_title']); ?></h4>
          
          <p class="lead text-muted mb-4">
            <?php echo esc($content['main_description']); ?>
          </p>
          
          <div class="alert alert-info mb-4">
            <strong>💡 Tahukah Anda?</strong> <?php echo esc($content['info_box']); ?>
          </div>
          
          <h5 class="fw-bold mb-3">🎯 Tujuan Sistem</h5>
          <ul class="list-unstyled mb-4">
            <li class="mb-3">
              <div class="d-flex align-items-start">
                <div class="icon-box bg-primary me-3" style="width: 50px; height: 50px; font-size: 24px;">
                  <span>✅</span>
                </div>
                <div>
                  <strong><?php echo esc($content['goal_1_title']); ?></strong>
                  <p class="text-muted mb-0"><?php echo esc($content['goal_1_desc']); ?></p>
                </div>
              </div>
            </li>
            <li class="mb-3">
              <div class="d-flex align-items-start">
                <div class="icon-box bg-success me-3" style="width: 50px; height: 50px; font-size: 24px;">
                  <span>⚡</span>
                </div>
                <div>
                  <strong><?php echo esc($content['goal_2_title']); ?></strong>
                  <p class="text-muted mb-0"><?php echo esc($content['goal_2_desc']); ?></p>
                </div>
              </div>
            </li>
            <li class="mb-3">
              <div class="d-flex align-items-start">
                <div class="icon-box bg-info me-3" style="width: 50px; height: 50px; font-size: 24px;">
                  <span>📊</span>
                </div>
                <div>
                  <strong><?php echo esc($content['goal_3_title']); ?></strong>
                  <p class="text-muted mb-0"><?php echo esc($content['goal_3_desc']); ?></p>
                </div>
              </div>
            </li>
            <li>
              <div class="d-flex align-items-start">
                <div class="icon-box bg-warning me-3" style="width: 50px; height: 50px; font-size: 24px;">
                  <span>🤝</span>
                </div>
                <div>
                  <strong><?php echo esc($content['goal_4_title']); ?></strong>
                  <p class="text-muted mb-0"><?php echo esc($content['goal_4_desc']); ?></p>
                </div>
              </div>
            </li>
          </ul>
          
          <hr class="my-4">
          
          <h5 class="fw-bold mb-3">📁 Jenis Laporan</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="p-3 rounded" style="background: #f0f9ff; border-left: 6px solid #3b82f6;">
                <strong>🗂️ <?php echo esc($content['category_1']); ?></strong>
                <p class="small mb-0 text-muted"><?php echo esc($content['category_1_desc']); ?></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded" style="background: #f5f3ff; border-left: 4px solid #8b5cf6;">
                <strong>📚 <?php echo esc($content['category_2']); ?></strong>
                <p class="small mb-0 text-muted"><?php echo esc($content['category_2_desc']); ?></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded" style="background: #f0fdf4; border-left: 4px solid #10b981;">
                <strong>🏥 <?php echo esc($content['category_3']); ?></strong>
                <p class="small mb-0 text-muted"><?php echo esc($content['category_3_desc']); ?></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded" style="background: #ecfdf5; border-left: 4px solid #059669;">
                <strong>🌳 <?php echo esc($content['category_4']); ?></strong>
                <p class="small mb-0 text-muted"><?php echo esc($content['category_4_desc']); ?></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                <strong>🚨 <?php echo esc($content['category_5']); ?></strong>
                <p class="small mb-0 text-muted"><?php echo esc($content['category_5_desc']); ?></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded" style="background: #fce7f3; border-left: 4px solid #ec4899;">
                <strong>📌 <?php echo esc($content['category_6']); ?></strong>
                <p class="small mb-0 text-muted"><?php echo esc($content['category_6_desc']); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
      
      <!-- How to Use Card -->
      <div class="card card-main mb-4">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="icon-box bg-primary me-3">
              <span>📖</span>
            </div>
            <h5 class="card-title mb-0">Cara Menggunakan</h5>
          </div>
          
          <ol class="list-unstyled">
            <li class="mb-3">
              <div class="d-flex align-items-start">
                <div class="badge bg-primary rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</div>
                <div class="ms-3">
                  <strong>Isi Formulir</strong>
                  <p class="small text-muted mb-0">Lengkapi data laporan Anda di halaman beranda</p>
                </div>
              </div>
            </li>
            <li class="mb-3">
              <div class="d-flex align-items-start">
                <div class="badge bg-primary rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</div>
                <div class="ms-3">
                  <strong>Dapatkan Report ID</strong>
                  <p class="small text-muted mb-0">Simpan ID laporan untuk tracking</p>
                </div>
              </div>
            </li>
            <li>
              <div class="d-flex align-items-start">
                <div class="badge bg-primary rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</div>
                <div class="ms-3">
                  <strong>Lacak Status</strong>
                  <p class="small text-muted mb-0">Pantau perkembangan laporan secara real-time</p>
                </div>
              </div>
            </li>
          </ol>
          
          <a href="login_masyarakat.php" class="btn btn-primary w-100 mt-3">
            🚀 Mulai Lapor Sekarang
          </a>
        </div>
      </div>
      
      <!-- Statistics Card -->
      <div class="card card-main mb-4">
        <div class="card-body p-4 text-center">
          <div style="font-size: 60px; margin-bottom: 15px;">📊</div>
          <h5 class="fw-bold mb-3">Statistik Sistem</h5>
          <div class="row text-center">
            <div class="col-6 mb-3">
              <div class="stats-number" style="font-size: 2rem;">24/7</div>
              <small class="text-muted">Layanan Aktif</small>
            </div>
            <div class="col-6 mb-3">
              <div class="stats-number" style="font-size: 2rem;">100%</div>
              <small class="text-muted">Gratis</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<footer style="padding: 2rem 1rem;">
  <div class="container">
    <div style="background: white; color: #24ceff; padding: 2rem 1.5rem 1rem; border-radius: 12px;">
      
      <!-- Grid 3 Kolom -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; padding-bottom: 1.5rem; border-bottom: 0.5px solid rgba(255,255,255,0.25);">

        <!-- Kolom 1: Identitas Desa -->
        <div>
          <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
            <img src="assets/luwu.png" alt="Logo" style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.2); object-fit:cover;">
            <strong style="font-size:15px;">Pemerintah Desa Posi</strong>
          </div>
          <address style="font-style:normal; font-size:13px; color:black;">
            Desa Posi, Kecamatan Bua<br>
            Kabupaten Luwu<br>
            Sulawesi Selatan
          </address>
          <p style="font-size:13px; margin-top:8px; color:black;">
            <strong style="color:black;">Kode Wilayah:</strong> 91991
          </p>
        </div>

        <!-- Kolom 2: Hubungi Kami -->
        <div>
          <p style="font-size:15px; font-weight:500; margin:0 0 10px;">Hubungi Kami</p>
          <p style="font-size:13px; color:black; margin-bottom:8px;">
            📞 (0411) 123-4567
          </p>
          <p style="font-size:13px; color:black; margin-bottom:10px;">
            ✉️ desa.posi@gmail.com
          </p>
           <!-- Ikon Media Sosial -->
          <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
           <a href="https://www.facebook.com/pemdes.posi" target="_blank" rel="noopener noreferrer" title="Facebook" style="width:34px; height:34px; border-radius:50%; background:#1877f2; display:flex; align-items:center; justify-content:center; text-decoration:none;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 320 512" fill="white"><path d="M279.14 288l14.22-92.66h-88.91v-53.96c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
          </a>
            <a href="https://www.instagram.com/desaposi01?igsh=MTJicGptN2tzZ3l2" title="Instagram" style="width:34px; height:34px; border-radius:50%; background:radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); display:flex; align-items:center; justify-content:center; text-decoration:none;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
            <a href="https://wa.me/6282335193686" title="WhatsApp" style="width:34px; height:34px; border-radius:50%; background:#25d366; display:flex; align-items:center; justify-content:center; text-decoration:none;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
            </a>
          </div>
        </div>

       <!-- Kolom 3: Tautan Cepat -->
<div>
  <p style="font-size:15px; font-weight:500; margin:0 0 10px;">Tautan Cepat</p>
  <div style="display:flex; flex-direction:column; gap:8px;">
    <a href="index.php" style="color:black; font-size:13px; text-decoration:none;">Beranda</a>
    <a href="about.php" style="color:black; font-size:13px; text-decoration:none;">Tentang</a>
    <a href="contact.php" style="color:black; font-size:13px; text-decoration:none;">Kontak</a>
    <a href="login.php" style="color:black; font-size:13px; text-decoration:none;">Login</a>
    <a href="../admin/login.php" style="color:black; font-size:13px; text-decoration:none;">Admin</a>
  </div>
</div>
      </div>

      <!-- Copyright -->
      <div style="padding-top:1rem; text-align:center;">
        <p style="font-size:12px; color:black; margin:0;">
          © <?php echo date('Y'); ?> Desa Posi — Kecamatan Bua, Kabupaten Luwu
        </p>
      </div>

    </div>
  </div>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>