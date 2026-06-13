<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();

$upload_dir = __DIR__ . '/uploads/';
if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// Tentukan halaman redirect berdasarkan session
$redirect_page = isset($_SESSION['user_id']) ? 'login_masyarakat.php' : 'index.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){ 
    header('Location: ' . $redirect_page); 
    exit; 
}

$anon = isset($_POST['anon']) && $_POST['anon']=='1';
$nama = $anon ? 'Anonim' : trim($_POST['nama'] ?? '');
$kontak = $anon ? null : trim($_POST['kontak'] ?? null);
$kategori = trim($_POST['kategori'] ?? '');
$isi = trim($_POST['isi'] ?? '');
$user_id = $_SESSION['user_id'] ?? null;

 if(!$isi || empty($isi)){ 
    echo 'Isi laporan wajib diisi. <a href="' . $redirect_page . '">Kembali</a>'; 
    exit; 
}
// Handle foto upload
$foto_name = null;
if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK){
    $f = $_FILES['foto'];
    $max = 2 * 1024 * 1024; // 2MB
    
    if($f['size'] > $max){ 
        echo 'File terlalu besar (maksimal 2MB). <a href="' . $redirect_page . '">Kembali</a>'; 
        exit; 
    }
    
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, ['jpg','jpeg','png','webp'])){ 
        echo 'Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP. <a href="' . $redirect_page . '">Kembali</a>'; 
        exit; 
    }
    
    $foto_name = uniqid('report_') . '.' . $ext;
    move_uploaded_file($f['tmp_name'], $upload_dir . $foto_name);
}

// Generate unique report ID
do {
    $report_id = 'REP' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $stmt = $conn->prepare('SELECT id FROM reports WHERE report_id = ?');
    $stmt->bind_param('s', $report_id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
} while($exists);

// Insert ke tabel reports dengan status 'menunggu'
$stmt = $conn->prepare('INSERT INTO reports (report_id, user_id, nama, kontak, kategori, isi, foto, status, is_anonim, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
$initial_status = 'menunggu';
$is_anonim = $anon ? 1 : 0;

// Fix: Sesuaikan tipe data bind_param
$stmt->bind_param('sisssssis', $report_id, $user_id, $nama, $kontak, $kategori, $isi, $foto_name, $initial_status, $is_anonim);

if($stmt->execute()){
    $report_db_id = $conn->insert_id;
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laporan Berhasil Dikirim</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/submit.css" rel="stylesheet">
    </head>
    <body>
        <div class="success-card">
            <div class="success-icon">✅</div>
            <h2>Laporan Berhasil Dikirim!</h2>
            <p class="text-muted mb-4">
                Terima kasih atas partisipasi Anda. Tim kami akan segera menindaklanjuti laporan Anda.
            </p>
            
            <div class="report-id-box">
                <div class="report-id-label">📋 Kode Laporan Anda</div>
                <div class="report-id-text" id="reportId"><?php echo esc($report_id); ?></div>
            </div>
            
            <div class="info-box">
                <small>
                    <strong>💡 Penting!</strong> Screenshot atau catat kode di atas untuk melacak status laporan Anda kapan saja.
                </small>
            </div>

            <div class="steps-container">
                <h6 class="fw-bold mb-3 text-center">🔄 Langkah Selanjutnya</h6>
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-text">
                        <strong>Tunggu Verifikasi</strong><br>
                        <small class="text-muted">Admin akan memverifikasi laporan Anda</small>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-text">
                        <strong>Lacak Status</strong><br>
                        <small class="text-muted">Gunakan kode untuk cek progress</small>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="<?php echo $redirect_page; ?>" class="btn btn-home">
                    <?php echo isset($_SESSION['user_id']) ? '← Kembali ke Dashboard' : '🏠 Kembali ke Beranda'; ?>
                </a>
            </div>
        </div>
        
        <script>
        function copyReportId() {
            const reportId = document.getElementById('reportId').textContent.trim();
            const btn = document.getElementById('copyBtn');
            
            navigator.clipboard.writeText(reportId).then(function() {
                const originalText = btn.innerHTML;
                btn.innerHTML = '✓ Berhasil Disalin!';
                btn.style.background = '#10b981';
                
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
                }, 2000);
            }).catch(function() {
                // Fallback untuk browser lama
                const textarea = document.createElement('textarea');
                textarea.value = reportId;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '✓ Berhasil Disalin!';
                    btn.style.background = '#10b981';
                    setTimeout(function() {
                        btn.innerHTML = originalText;
                        btn.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
                    }, 2000);
                } catch(err) {
                    alert('Report ID: ' + reportId + '\n\nSilakan salin kode secara manual.');
                }
                document.body.removeChild(textarea);
            });
        }
        </script>
    </body>
    </html>
    <?php 
    exit;
} else {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error - Gagal Mengirim Laporan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/style.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-card {
                background: white;
                border-radius: 30px;
                padding: 3rem;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 500px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div style="font-size: 5rem;">❌</div>
            <h3 class="mt-3">Gagal Menyimpan Laporan</h3>
            <p class="text-muted">Terjadi kesalahan saat menyimpan data. Silakan coba lagi.</p>
            <a href="<?php echo $redirect_page; ?>" class="btn btn-primary mt-3">
                <?php echo isset($_SESSION['user_id']) ? '← Kembali ke Dashboard' : '🏠 Kembali ke Beranda'; ?>
            </a>
        </div>
    </body>
    </html>
    <?php
}
?>