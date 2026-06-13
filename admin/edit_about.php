<?php
require_once __DIR__ . '/../helpers/util.php'; 
require_once __DIR__ . '/../config/koneksi.php';
session_start();
 
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_logged_in'])) {
    $_SESSION['error_message'] = 'Anda harus login sebagai admin terlebih dahulu.';
    header('Location: login.php');
    exit;
}
 
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
 
$success_message = '';
$error_message = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_content = [];
    foreach (array_keys($defaults) as $key) {
        $new_content[$key] = $_POST[$key] ?? $defaults[$key];
    }
    $json_content = json_encode($new_content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    try {
        $stmt_check = $conn->prepare("SELECT 1 FROM page_content WHERE page_name = 'about'");
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $stmt_check->close();
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE page_content SET content_data = ? WHERE page_name = 'about'");
            $stmt->bind_param('s', $json_content);
        } else {
            $stmt = $conn->prepare("INSERT INTO page_content (page_name, content_data) VALUES ('about', ?)");
            $stmt->bind_param('s', $json_content);
        }
        if ($stmt->execute()) {
            header('Location: ../public/about.php');
            exit;
        } else {
            $error_message = 'Gagal menyimpan data: ' . $stmt->error;
        }
        $stmt->close();
        $content = $new_content;
    } catch (Exception $e) {
        $error_message = 'Terjadi error: ' . $e->getMessage();
    }
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $error_message) {
    $content = [];
    try {
        $result = @$conn->query("SELECT content_data FROM page_content WHERE page_name = 'about'");
        if($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $content = json_decode($row['content_data'], true) ?? [];
        }
    } catch (Exception $e) {
        $content = [];
    }
    $content = array_merge($defaults, $content);
}
 
$site_title = 'Edit Halaman Tentang - Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $site_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --primary-light: #EFF6FF;
            --primary-glow: rgba(37,99,235,0.12);
            --accent: #0EA5E9;
            --emerald: #059669;
            --violet: #7C3AED;
            --amber: #D97706;
            --rose: #E11D48;
            --cyan: #0891B2;
            --sidebar-bg: #0F172A;
            --sidebar-text: #94A3B8;
            --sidebar-active: #2563EB;
            --bg: #F1F5F9;
            --surface: #FFFFFF;
            --border: #E2E8F0;
            --text: #0F172A;
            --text-muted: #64748B;
            --radius: 14px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.06);
            --shadow-hover: 0 4px 12px rgba(37,99,235,0.15), 0 8px 32px rgba(37,99,235,0.1);
        }
 
        * { box-sizing: border-box; margin: 0; padding: 0; }
 
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
 
        /* ── TOP NAV ── */
        .topnav {
            background: var(--sidebar-bg);
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        .topnav-brand {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }
        .topnav-brand .brand-dot {
            width: 28px; height: 28px;
            background: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
        }
        .topnav-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .topnav-right a {
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .topnav-right a:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .topnav-badge {
            background: var(--primary);
            color: #fff;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }
 
        /* ── PAGE HEADER ── */
        .page-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideDown 0.4s ease both;
        }
        .page-header-left h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.5px;
        }
        .page-header-left .breadcrumb-trail {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .breadcrumb-trail span { color: var(--border); }
 
        /* ── MAIN CONTENT ── */
        .main-content {
            flex: 1;
            padding: 28px;
            max-width: 960px;
            margin: 0 auto;
            width: 100%;
        }
 
        /* ── ALERTS ── */
        .alert-modern {
            border: none;
            border-radius: var(--radius);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease both;
        }
        .alert-success-mod { background: #ECFDF5; color: #065F46; }
        .alert-danger-mod { background: #FFF1F2; color: #9F1239; }
        .alert-modern i { font-size: 18px; }
 
        /* ── SECTION CARDS ── */
        .section-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            animation: fadeUp 0.4s ease both;
            transition: box-shadow 0.25s;
        }
        .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.1); }
        .section-card:nth-child(1) { animation-delay: 0.05s; }
        .section-card:nth-child(2) { animation-delay: 0.1s; }
        .section-card:nth-child(3) { animation-delay: 0.15s; }
        .section-card:nth-child(4) { animation-delay: 0.2s; }
 
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
        }
        .section-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .icon-blue { background: var(--primary-light); color: var(--primary); }
        .icon-violet { background: #F5F3FF; color: var(--violet); }
        .icon-emerald { background: #ECFDF5; color: var(--emerald); }
        .icon-amber { background: #FFFBEB; color: var(--amber); }
 
        .section-title {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
            letter-spacing: -0.2px;
        }
        .section-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }
        .section-body { padding: 22px; }
 
        /* ── FORM FIELDS ── */
        .field-group { margin-bottom: 18px; }
        .field-group:last-child { margin-bottom: 0; }
 
        .field-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 7px;
        }
        .field-control {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            color: var(--text);
            background: #FAFAFA;
            transition: all 0.2s;
            outline: none;
            resize: vertical;
        }
        .field-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .field-control::placeholder { color: #CBD5E1; }
 
        /* ── PAIR ROW ── */
        .pair-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
            padding: 16px;
            background: #F8FAFC;
            border-radius: 10px;
            border: 1px solid var(--border);
            position: relative;
        }
        .pair-row:last-child { margin-bottom: 0; }
        .pair-badge {
            position: absolute;
            top: -10px;
            left: 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.3px;
        }
 
        /* ── ACTION BAR ── */
        .action-bar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            animation: fadeUp 0.4s 0.25s ease both;
        }
        .action-bar-left {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .action-bar-left i { color: var(--primary); }
 
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            border: 1.5px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-back:hover {
            border-color: #94A3B8;
            color: var(--text);
            background: #F8FAFC;
        }
        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: none;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }
        .btn-save:hover {
            background: #1D4ED8;
            box-shadow: 0 4px 16px rgba(37,99,235,0.4);
            transform: translateY(-1px);
        }
        .btn-save:active { transform: translateY(0); }
 
        .btn-group-actions { display: flex; gap: 10px; }
 
        /* ── GOAL ICONS ── */
        .goal-icon-map {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px; height: 22px;
            border-radius: 6px;
            font-size: 12px;
            margin-right: 4px;
        }
        .gi-1 { background: #EFF6FF; color: #2563EB; }
        .gi-2 { background: #FFF7ED; color: #EA580C; }
        .gi-3 { background: #F0FDF4; color: #16A34A; }
        .gi-4 { background: #FDF4FF; color: #A21CAF; }
        .gi-cat { background: #F0F9FF; color: #0369A1; }
 
        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
 
        @media (max-width: 640px) {
            .pair-row { grid-template-columns: 1fr; }
            .main-content { padding: 16px; }
            .action-bar { flex-direction: column; gap: 14px; align-items: stretch; }
            .btn-group-actions { justify-content: flex-end; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 12px; padding: 16px; }
        }
    </style>
</head>
<body>
 
<!-- TOP NAV -->
<nav class="topnav">
    <a class="topnav-brand" href="dashboard.php">
        <div class="brand-dot"><i class="bi bi-shield-check" style="color:#fff;font-size:13px;"></i></div>
        Admin Panel
    </a>
</nav>
 
<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-left">
        <h1><i class="bi bi-pencil-square" style="color:var(--primary);margin-right:8px;"></i>Edit Halaman Tentang</h1>
        <div class="breadcrumb-trail">
            <span>›</span>
            <span>Edit Halaman Tentang</span>
        </div>
    </div>
    <span class="topnav-badge" style="background:var(--primary-light);color:var(--primary);">
        <i class="bi bi-file-earmark-text"></i> Halaman About
    </span>
</div>
 
<div class="main-content">
 
    <?php if ($success_message): ?>
    <div class="alert-modern alert-success-mod">
        <i class="bi bi-check-circle-fill"></i>
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
    <div class="alert-modern alert-danger-mod">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
 
    <form action="edit_about.php" method="POST">
 
        <!-- BAGIAN HERO -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon icon-blue"><i class="bi bi-stars"></i></div>
                <div>
                    <div class="section-title">Bagian Hero</div>
                    <div class="section-subtitle">Judul dan subjudul utama halaman</div>
                </div>
            </div>
            <div class="section-body">
                <div class="field-group">
                    <label class="field-label" for="hero_title"><i class="bi bi-type-h1"></i> Judul Hero</label>
                    <input type="text" class="field-control" id="hero_title" name="hero_title"
                           placeholder="Masukkan judul hero..."
                           value="<?php echo esc($content['hero_title']); ?>">
                </div>
                <div class="field-group">
                    <label class="field-label" for="hero_subtitle"><i class="bi bi-text-paragraph"></i> Sub-Judul Hero</label>
                    <textarea class="field-control" id="hero_subtitle" name="hero_subtitle" rows="3"
                              placeholder="Masukkan sub-judul hero..."><?php echo esc($content['hero_subtitle']); ?></textarea>
                </div>
            </div>
        </div>
 
        <!-- DESKRIPSI UTAMA -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon icon-violet"><i class="bi bi-file-richtext"></i></div>
                <div>
                    <div class="section-title">Deskripsi Utama</div>
                    <div class="section-subtitle">Penjelasan platform dan info box</div>
                </div>
            </div>
            <div class="section-body">
                <div class="field-group">
                    <label class="field-label" for="main_title"><i class="bi bi-bookmark"></i> Judul Utama</label>
                    <input type="text" class="field-control" id="main_title" name="main_title"
                           value="<?php echo esc($content['main_title']); ?>">
                </div>
                <div class="field-group">
                    <label class="field-label" for="main_description"><i class="bi bi-align-left"></i> Deskripsi Platform</label>
                    <textarea class="field-control" id="main_description" name="main_description" rows="3"><?php echo esc($content['main_description']); ?></textarea>
                </div>
                <div class="field-group">
                    <label class="field-label" for="info_box"><i class="bi bi-info-circle"></i> Teks Info Box</label>
                    <textarea class="field-control" id="info_box" name="info_box" rows="2"><?php echo esc($content['info_box']); ?></textarea>
                </div>
            </div>
        </div>
 
        <!-- TUJUAN SISTEM -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon icon-emerald"><i class="bi bi-bullseye"></i></div>
                <div>
                    <div class="section-title">Tujuan Sistem</div>
                    <div class="section-subtitle">4 pilar utama tujuan platform</div>
                </div>
            </div>
            <div class="section-body">
                <?php
                $goals = [
                    ['num'=>1,'label'=>'Transparansi','icon'=>'bi-eye','gi'=>'gi-1'],
                    ['num'=>2,'label'=>'Responsif','icon'=>'bi-lightning','gi'=>'gi-2'],
                    ['num'=>3,'label'=>'Akuntabilitas','icon'=>'bi-shield','gi'=>'gi-3'],
                    ['num'=>4,'label'=>'Partisipasi','icon'=>'bi-people','gi'=>'gi-4'],
                ];
                foreach($goals as $g): ?>
                <div class="pair-row">
                    <span class="pair-badge">
                        <i class="bi <?php echo $g['icon']; ?> <?php echo $g['gi']; ?>"></i>
                        Tujuan <?php echo $g['num']; ?> — <?php echo $g['label']; ?>
                    </span>
                    <div class="field-group" style="margin-bottom:0;margin-top:8px;">
                        <label class="field-label">Judul Tujuan</label>
                        <input type="text" class="field-control"
                               id="goal_<?php echo $g['num']; ?>_title"
                               name="goal_<?php echo $g['num']; ?>_title"
                               value="<?php echo esc($content['goal_'.$g['num'].'_title']); ?>">
                    </div>
                    <div class="field-group" style="margin-bottom:0;margin-top:8px;">
                        <label class="field-label">Deskripsi Tujuan</label>
                        <input type="text" class="field-control"
                               id="goal_<?php echo $g['num']; ?>_desc"
                               name="goal_<?php echo $g['num']; ?>_desc"
                               value="<?php echo esc($content['goal_'.$g['num'].'_desc']); ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
 
        <!-- KATEGORI LAPORAN -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon icon-amber"><i class="bi bi-tags"></i></div>
                <div>
                    <div class="section-title">Jenis Laporan / Kategori</div>
                    <div class="section-subtitle">6 kategori pengaduan masyarakat</div>
                </div>
            </div>
            <div class="section-body">
                <?php
                $cats = [
                    ['num'=>1,'label'=>'Infrastruktur','icon'=>'bi-building'],
                    ['num'=>2,'label'=>'Sosial','icon'=>'bi-people'],
                    ['num'=>3,'label'=>'Kebersihan','icon'=>'bi-trash'],
                    ['num'=>4,'label'=>'Kesehatan','icon'=>'bi-heart-pulse'],
                    ['num'=>5,'label'=>'Keamanan','icon'=>'bi-shield-lock'],
                    ['num'=>6,'label'=>'Saran / Aspirasi','icon'=>'bi-chat-quote'],
                ];
                foreach($cats as $c): ?>
                <div class="pair-row">
                    <span class="pair-badge gi-cat" style="background:#F0F9FF;color:#0369A1;border:1px solid #BAE6FD;">
                        <i class="bi <?php echo $c['icon']; ?>"></i>
                        Kategori <?php echo $c['num']; ?> — <?php echo $c['label']; ?>
                    </span>
                    <div class="field-group" style="margin-bottom:0;margin-top:8px;">
                        <label class="field-label">Judul Kategori</label>
                        <input type="text" class="field-control"
                               id="category_<?php echo $c['num']; ?>"
                               name="category_<?php echo $c['num']; ?>"
                               value="<?php echo esc($content['category_'.$c['num']]); ?>">
                    </div>
                    <div class="field-group" style="margin-bottom:0;margin-top:8px;">
                        <label class="field-label">Deskripsi Kategori</label>
                        <input type="text" class="field-control"
                               id="category_<?php echo $c['num']; ?>_desc"
                               name="category_<?php echo $c['num']; ?>_desc"
                               value="<?php echo esc($content['category_'.$c['num'].'_desc']); ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
 
        <!-- ACTION BAR -->
        <div class="action-bar">
            <div class="action-bar-left">
                <i class="bi bi-info-circle"></i>
                Perubahan akan langsung diterapkan ke halaman publik
            </div>
            <div class="btn-group-actions">
                <a href="dashboard.php" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-save">
                    <i class="bi bi-check2-circle"></i> Simpan Perubahan
                </button>
            </div>
        </div>
 
    </form>
</div>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth focus label highlight
    document.querySelectorAll('.field-control').forEach(el => {
        el.addEventListener('focus', () => {
            el.closest('.field-group')?.querySelector('.field-label')
               ?.style.setProperty('color', '#2563EB');
        });
        el.addEventListener('blur', () => {
            el.closest('.field-group')?.querySelector('.field-label')
               ?.style.setProperty('color', '');
        });
    });
 
    // Save button loading state
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.querySelector('.btn-save');
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        btn.style.opacity = '0.8';
        btn.disabled = true;
    });
</script>
</body>
</html>