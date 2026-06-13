<?php
require_once __DIR__ . '/../helpers/util.php'; 
require_once __DIR__ . '/../config/koneksi.php';
session_start();
 
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_logged_in'])) {
    $_SESSION['error_message'] = 'Anda harus login sebagai admin terlebih dahulu.';
    header('Location: login.php');
    exit;
}
 
$content = [];
try {
    $result = @$conn->query("SELECT content_data FROM page_content WHERE page_name = 'contact'");
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $content = json_decode($row['content_data'], true) ?? [];
    }
} catch (Exception $e) { $content = []; }
 
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
 
$content = array_merge($defaults, $content);
 
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content['schedule_mon_thu'] = $_POST['schedule_mon_thu'] ?? $content['schedule_mon_thu'];
    $content['schedule_fri'] = $_POST['schedule_fri'] ?? $content['schedule_fri'];
    $content['schedule_sat'] = $_POST['schedule_sat'] ?? $content['schedule_sat'];
    $content['schedule_sat_note'] = $_POST['schedule_sat_note'] ?? $content['schedule_sat_note'];
    $json_content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    try {
        $stmt_check = $conn->prepare("SELECT 1 FROM page_content WHERE page_name = 'contact'");
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $stmt_check->close();
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE page_content SET content_data = ? WHERE page_name = 'contact'");
            $stmt->bind_param('s', $json_content);
        } else {
            $stmt = $conn->prepare("INSERT INTO page_content (page_name, content_data) VALUES ('contact', ?)");
            $stmt->bind_param('s', $json_content);
        }
        if ($stmt->execute()) {
            header('Location: ../public/contact.php');
            exit;
        } else {
            $error_message = 'Gagal menyimpan data: ' . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $error_message = 'Terjadi error: ' . $e->getMessage();
    }
}
 
$site_title = 'Edit Jam Operasional - Admin';
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
            --cyan: #0891B2;
            --cyan-light: #F0F9FF;
            --nav-bg: #0F172A;
            --bg: #F1F5F9;
            --surface: #FFFFFF;
            --border: #E2E8F0;
            --text: #0F172A;
            --text-muted: #64748B;
            --radius: 14px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.06);
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
 
        /* NAV */
        .topnav {
            background: var(--nav-bg);
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
        .brand-dot {
            width: 28px; height: 28px;
            background: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .topnav-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .topnav-right a {
            color: #94A3B8;
            text-decoration: none;
            font-size: 13px;
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .topnav-right a:hover { background: rgba(255,255,255,0.08); color: #fff; }
 
        /* PAGE HEADER */
        .page-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideDown 0.4s ease both;
        }
        .page-header h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .breadcrumb-trail {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .breadcrumb-trail a { color: var(--text-muted); text-decoration: none; }
        .topnav-badge {
            background: var(--cyan-light);
            color: var(--cyan);
            font-size: 11px;
            padding: 3px 11px;
            border-radius: 20px;
            font-weight: 600;
        }
 
        /* MAIN */
        .main-content {
            flex: 1;
            padding: 28px;
            max-width: 680px;
            margin: 0 auto;
            width: 100%;
        }
 
        /* ALERTS */
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
 
        /* INFO CARD (readonly email/phone) */
        .info-card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            overflow: hidden;
            animation: fadeUp 0.4s 0.05s ease both;
        }
        .info-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
        }
        .sicon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .ic-cyan { background: var(--cyan-light); color: var(--cyan); }
        .ic-blue { background: var(--primary-light); color: var(--primary); }
        .section-title { font-family: 'Sora', sans-serif; font-weight: 600; font-size: 0.9rem; color: var(--text); letter-spacing: -0.2px; }
        .section-subtitle { font-size: 12px; color: var(--text-muted); margin-top: 1px; }
        .info-body { padding: 18px 22px; }
 
        /* INFO ROW */
        .info-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-row:first-child { padding-top: 0; }
        .info-icon-wrap {
            width: 36px; height: 36px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .ii-email { background: #F5F3FF; color: #7C3AED; }
        .ii-phone { background: #ECFDF5; color: #059669; }
        .info-label { font-size: 11.5px; color: var(--text-muted); margin-bottom: 2px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
        .info-value { font-size: 14px; font-weight: 600; color: var(--text); }
        .readonly-badge {
            margin-left: auto;
            background: #F1F5F9;
            color: #94A3B8;
            font-size: 10.5px;
            padding: 3px 9px;
            border-radius: 20px;
            font-weight: 600;
        }
 
        /* SCHEDULE CARD */
        .section-card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            overflow: hidden;
            animation: fadeUp 0.4s 0.1s ease both;
            transition: box-shadow 0.25s;
        }
        .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.09); }
        .section-body { padding: 22px; }
 
        /* DAY ROWS */
        .day-row {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }
        .day-row:last-child { border-bottom: none; padding-bottom: 0; }
        .day-row:first-child { padding-top: 0; }
        .day-info { display: flex; align-items: center; gap: 10px; }
        .day-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px; height: 34px;
            border-radius: 9px;
            font-size: 14px;
            flex-shrink: 0;
        }
        .db-week { background: #EFF6FF; color: #2563EB; }
        .db-fri  { background: #FFF7ED; color: #EA580C; }
        .db-sat  { background: #F5F3FF; color: #7C3AED; }
        .day-name { font-weight: 700; font-size: 13.5px; color: var(--text); }
        .day-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }
 
        .field-control {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            color: var(--text);
            background: #FAFAFA;
            outline: none;
            transition: all 0.2s;
        }
        .field-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
 
        /* NOTE ROW */
        .note-row {
            margin-top: 14px;
            padding: 14px;
            background: #FFFBEB;
            border-radius: 10px;
            border: 1px solid #FDE68A;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .note-row i { color: #D97706; font-size: 18px; flex-shrink: 0; }
        .note-label { font-size: 11.5px; font-weight: 700; color: #92400E; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
 
        /* ACTION BAR */
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
            animation: fadeUp 0.4s 0.18s ease both;
        }
        .action-bar-left { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; }
        .action-bar-left i { color: var(--primary); }
        .btn-group-actions { display: flex; gap: 10px; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            border: 1.5px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            transition: all 0.2s; cursor: pointer;
        }
        .btn-back:hover { border-color: #94A3B8; color: var(--text); background: #F8FAFC; }
        .btn-save {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 10px;
            font-size: 14px; font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: none; background: var(--primary); color: #fff;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }
        .btn-save:hover { background: #1D4ED8; box-shadow: 0 4px 16px rgba(37,99,235,0.4); transform: translateY(-1px); }
        .btn-save:active { transform: translateY(0); }
 
        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
 
        @media(max-width:600px) {
            .day-row { grid-template-columns: 1fr; }
            .main-content { padding: 16px; }
            .action-bar { flex-direction: column; gap: 14px; align-items: stretch; }
            .btn-group-actions { justify-content: flex-end; }
        }
    </style>
</head>
<body>
 
<nav class="topnav">
    <a class="topnav-brand" href="dashboard.php">
        <div class="brand-dot"><i class="bi bi-shield-check" style="color:#fff;font-size:13px;"></i></div>
        Admin Panel
    </a>
</nav>
 
<div class="page-header">
    <div>
        <h1><i class="bi bi-clock-history" style="color:var(--cyan);"></i> Edit Jam Operasional</h1>
        <div class="breadcrumb-trail">
            <span style="color:var(--border);">›</span>
            <span>Edit Jam Operasional</span>
        </div>
    </div>
    <span class="topnav-badge"><i class="bi bi-telephone"></i> Halaman Kontak</span>
</div>
 
<div class="main-content">
 
    <?php if ($success_message): ?>
    <div class="alert-modern alert-success-mod">
        <i class="bi bi-check-circle-fill" style="font-size:18px;"></i>
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
    <div class="alert-modern alert-danger-mod">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:18px;"></i>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
 
    <!-- INFO KONTAK (read only) -->
    <div class="info-card">
        <div class="info-header">
            <div class="sicon ic-cyan"><i class="bi bi-info-circle"></i></div>
            <div>
                <div class="section-title">Informasi Kontak</div>
                <div class="section-subtitle">Data kontak yang tampil di halaman publik</div>
            </div>
        </div>
        <div class="info-body">
            <div class="info-row">
                <div class="info-icon-wrap ii-email"><i class="bi bi-envelope"></i></div>
                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo esc($content['email']); ?></div>
                </div>
                <span class="readonly-badge"><i class="bi bi-lock"></i> Read-only</span>
            </div>
            <div class="info-row">
                <div class="info-icon-wrap ii-phone"><i class="bi bi-telephone"></i></div>
                <div>
                    <div class="info-label">Nomor Telepon</div>
                    <div class="info-value"><?php echo esc($content['phone']); ?></div>
                </div>
                <span class="readonly-badge"><i class="bi bi-lock"></i> Read-only</span>
            </div>
        </div>
    </div>
 
    <!-- FORM JAM OPERASIONAL -->
    <form action="edit_contact.php" method="POST">
        <div class="section-card">
            <div class="info-header">
                <div class="sicon ic-blue"><i class="bi bi-calendar-week"></i></div>
                <div>
                    <div class="section-title">Jam Operasional Kantor</div>
                    <div class="section-subtitle">Atur jadwal buka kantor desa per hari</div>
                </div>
            </div>
            <div class="section-body">
 
                <!-- Senin - Kamis -->
                <div class="day-row">
                    <div class="day-info">
                        <div class="day-badge db-week"><i class="bi bi-calendar3"></i></div>
                        <div>
                            <div class="day-name">Senin – Kamis</div>
                            <div class="day-sub">Hari kerja utama</div>
                        </div>
                    </div>
                    <input type="text" class="field-control" id="schedule_mon_thu" name="schedule_mon_thu"
                           placeholder="contoh: 08:00 - 16:00"
                           value="<?php echo esc($content['schedule_mon_thu']); ?>">
                </div>
 
                <!-- Jumat -->
                <div class="day-row">
                    <div class="day-info">
                        <div class="day-badge db-fri"><i class="bi bi-calendar3"></i></div>
                        <div>
                            <div class="day-name">Jumat</div>
                            <div class="day-sub">Jam lebih pendek</div>
                        </div>
                    </div>
                    <input type="text" class="field-control" id="schedule_fri" name="schedule_fri"
                           placeholder="contoh: 08:00 - 15:00"
                           value="<?php echo esc($content['schedule_fri']); ?>">
                </div>
 
                <!-- Sabtu -->
                <div class="day-row">
                    <div class="day-info">
                        <div class="day-badge db-sat"><i class="bi bi-calendar3"></i></div>
                        <div>
                            <div class="day-name">Sabtu</div>
                            <div class="day-sub">Hari terbatas</div>
                        </div>
                    </div>
                    <input type="text" class="field-control" id="schedule_sat" name="schedule_sat"
                           placeholder="contoh: 09:00 - 12:00"
                           value="<?php echo esc($content['schedule_sat']); ?>">
                </div>
 
                <!-- Catatan Sabtu -->
                <div class="note-row">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div style="flex:1;">
                        <div class="note-label">Catatan Hari Sabtu</div>
                        <input type="text" class="field-control" id="schedule_sat_note" name="schedule_sat_note"
                               style="background:#fff;border-color:#FCD34D;margin-top:0;"
                               placeholder="contoh: Hanya untuk urusan mendesak"
                               value="<?php echo esc($content['schedule_sat_note']); ?>">
                    </div>
                </div>
 
            </div>
        </div>
 
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
    document.querySelectorAll('.field-control').forEach(el => {
        el.addEventListener('focus', () => el.closest('.day-row, .note-row')?.classList.add('focused'));
        el.addEventListener('blur',  () => el.closest('.day-row, .note-row')?.classList.remove('focused'));
    });
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.querySelector('.btn-save');
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        btn.style.opacity = '0.8';
        btn.disabled = true;
    });
</script>
</body>
</html>