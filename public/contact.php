<?php
require_once __DIR__ . '/../helpers/util.php';
require_once __DIR__ . '/../config/koneksi.php';
session_start();

// Get content from database with error handling
$content = [];
try {
    $result = @$conn->query("SELECT content_data FROM page_content WHERE page_name = 'contact'");
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
    'hero_title' => 'Hubungi Kami',
    'hero_subtitle' => 'Kami siap membantu Anda. Jangan ragu untuk menghubungi kami!',
    'email' => 'desa.posi@gmail.com',
    'phone' => '+6281327806639',
    'schedule_mon_thu' => '08:00 - 16:00',
    'schedule_fri' => '08:00 - 15:00',
    'schedule_sat' => '09:00 - 12:00',
    'schedule_sat_note' => 'Hanya untuk urusan mendesak'
];

// Merge dengan defaults
$content = array_merge($defaults, $content);
$site_title = 'Kontak - Sistem Pengaduan Desa Posi';
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
        <li class="nav-item"><a class="nav-link" href="about.php">Tentang</a></li>
        <li class="nav-item"><a class="nav-link active" href="contact.php">Kontak</a></li>
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

<header class="hero-header">
  <div class="container text-center">
    <div style="font-size: 80px; margin-bottom: 20px;">📞</div>
    <h1 class="display-5 fw-bold mb-3"><?php echo esc($content['hero_title']); ?></h1>
    <p class="lead mb-0">
      <?php echo esc($content['hero_subtitle']); ?>
    </p>
  </div>
</header>

<main class="container my-5">
  <div class="row justify-content-center">
    
    <div class="col-lg-8">
      <div class="card card-main h-100">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-center mb-4">
            <div class="icon-box bg-primary me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 24px;">
              <span>🕐</span>
            </div>
            <h5 class="card-title mb-0 fw-bold">Jam Operasional</h5>
          </div>
          
          <div class="schedule-list">
            <div class="schedule-item mb-3 p-3" style="background: #f9fafb; border-radius: 12px;">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Senin - Kamis</span>
                <span class="badge bg-success" style="font-size: 0.9rem; padding: 8px 12px;"><?php echo esc($content['schedule_mon_thu']); ?></span>
              </div>
            </div>
            
            <div class="schedule-item mb-3 p-3" style="background: #f9fafb; border-radius: 12px;">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Jumat</span>
                <span class="badge bg-success" style="font-size: 0.9rem; padding: 8px 12px;"><?php echo esc($content['schedule_fri']); ?></span>
              </div>
            </div>
            
            <div class="schedule-item mb-3 p-3" style="background: #f9fafb; border-radius: 12px;">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Sabtu</span>
                <span class="badge bg-warning text-dark" style="font-size: 0.9rem; padding: 8px 12px;"><?php echo esc($content['schedule_sat']); ?></span>
              </div>
              <?php if(!empty($content['schedule_sat_note'])): ?>
              <small class="text-muted mt-2 d-block text-end"><?php echo esc($content['schedule_sat_note']); ?></small>
              <?php endif; ?>
            </div>
            
            <div class="schedule-item p-3" style="background: #fee2e2; border-radius: 12px;">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Minggu & Hari Libur</span>
                <span class="badge bg-danger" style="font-size: 0.9rem; padding: 8px 12px;">Tutup</span>
              </div>
            </div>
          </div>
          
          <div class="alert alert-info mt-4" style="border-radius: 12px; border: none; background: #e0f2fe; color: #0369a1;">
            <div class="d-flex align-items-center">
              <span class="me-2 fs-5">💡</span>
              <small><strong>Info:</strong> Sistem online tetap bisa digunakan 24/7</small>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<<!-- Footer -->
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