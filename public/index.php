<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();
$site_title = 'Website Resmi Desa Posi';
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

<!-- Background Wrapper -->
<div class="main-wrapper">

<!-- Navbar Floating -->
<nav class="navbar navbar-expand-lg" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/luwu.png" alt="Logo Desa Posi">
      <strong>Desa Posi</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">Tentang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php">Kontak</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/login.php">Admin</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Header -->
<header class="hero-header" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/background.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; color: white; min-height: 350px; display: flex; align-items: center; justify-content: center;">
  <div class="container text-center">
    <h1 class="display-5 fw-bold mb-3">Website Resmi Desa Posi</h1>
    <p class="lead mb-0">Sumber Informasi dan Pelaporan Aspirasi Masyarakat</p>
    <p class="mt-2" style="opacity: 0.85;">Kecamatan Bua, Kabupaten Luwu, Sulawesi Selatan</p>
  </div>
</header>

<!-- Main Content -->
<main class="container my-5">

  <!-- Sambutan Kepala Desa Section -->
  <section class="mb-5">
    <div class="sambutan-box">
      <div class="row align-items-center">
        <div class="col-md-4 text-center">
          <div class="kepala-desa-photo">
            <img src="assets/Ibu_desa.jpeg" alt="Ibu Desa" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
          </div>
        </div>
        <div class="col-md-8">
          <h2 class="sambutan-title">Sambutan Kepala Desa Posi</h2>
          <div class="kepala-desa-info">
            <h5>Hj. SANAWIA</h5>
            <p>Pj Kepala Desa Posi</p>
            <p class="text-muted mb-3"><em>Selamat pagi/siang/sore,</em></p>
            <p class="text-muted">
              Di era digital yang terus berkembang pesat ini, teknologi telah mengubah bagaimana kita berinteraksi dan berkomunikasi. Situs website ini hadir dengan tujuan untuk memberikan informasi, serta membantu pelayanan terkait dengan berbagai aspek kehidupan, baik itu dalam dunia pendidikan, bisnis, maupun kehidupan sehari-hari. Namun, dengan kemajuan ini, kita juga dihadapkan pada tantangan untuk menjaga keamanan data dan mengoptimalkan penggunaan teknologi secara bijak...
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Peta Desa Section -->
  <section class="mb-5">
    <div class="map-container">
      <h2 class="sambutan-title">PETA DESA</h2>
      <p class="text-muted mb-4">Menampilkan Peta Desa Dengan Interasi Point Desa Posi</p>
      <div class="map-box" style="background: white;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63744.47896466616!2d120.13389477351596!3d-3.0866848942253187!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d915810c3975f47%3A0xd5b7d2b3ee1d568b!2sPosi%2C%20Kec.%20Bua%2C%20Kabupaten%20Luwu%2C%20Sulawesi%20Selatan!5e0!3m2!1sid!2sid!4v1765710900469!5m2!1sid!2sid" 
        width="100%"
        height="400"
        style="border:0; border-radius: 15px;"
        allowfullscreen=""
        loading="lazy">
        </iframe>
      </div>
    </div>
  </section>

  <!-- SOTK Section -->
  <section class="mb-5">
    <div class="sotk-box">
      <h2 class="sambutan-title">SOTK</h2>
      <p class="text-muted mb-4">Struktur Organisasi dan Tata Kerja Desa Posi</p>
      <div class="row g-4">
        <div class="col-md-3 col-6">
          <div class="staff-card">
            <div class="staff-photo">  
            <img src="assets/dewi irfan.jpg" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
            </div>
            <div class="staff-name">DEWI IRFAN</div>
            <div class="staff-position">Sekretaris Desa</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="staff-card">
            <div class="staff-photo">
               <img src="assets/wahida.jpeg" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
            </div>
            <div class="staff-name">WAHIDA</div>
            <div class="staff-position">Kaur Pemerintahan</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="staff-card">
            <div class="staff-photo">
              <img src="assets/sudarman.jpeg" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
            </div>
            <div class="staff-name">SUDARMAN</div>
            <div class="staff-position">Kaur Umum</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="staff-card">
            <div class="staff-photo">
              <img src="assets/mas anja'pabuang.jpeg" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
            </div>
            <div class="staff-name">MAS ANJA'PABUANG</div>
            <div class="staff-position">Kaur Kesra</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Administrasi Penduduk Section -->
  <section class="mb-5">
    <div class="admin-box">
      <h2 class="sambutan-title">Administrasi Penduduk</h2>
      <p class="text-muted mb-4">Sistem digital yang berfungsi mempermudah pengelolaan data dan informasi terkait dengan kependudukan dan pendayagunaannya untuk pelayanan publik yang efektif dan efisien.</p>
      <div class="stat-grid">
        <div class="stat-box">
          <div class="number">1,956</div>
          <div class="label">Penduduk</div>
        </div>
        <div class="stat-box">
          <div class="number">981</div>
          <div class="label">Laki-Laki</div>
        </div>
        <div class="stat-box">
          <div class="number">563</div>
          <div class="label">Kepala Keluarga</div>
        </div>
        <div class="stat-box">
          <div class="number">967</div>
          <div class="label">Perempuan</div>
        </div>
        <div class="stat-box">
          <div class="number">32</div>
          <div class="label">Penduduk Sementara</div>
        </div>
        <div class="stat-box">
          <div class="number">271</div>
          <div class="label">Mutasi Penduduk</div>
        </div>
      </div>
    </div>
  </section>
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
            📞 (62) 82335193786
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
<!-- End Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
</script>

</body>
</html>
