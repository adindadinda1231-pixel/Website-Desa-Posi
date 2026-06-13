<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();

$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nik = trim($_POST['nik'] ?? '');
    
    // Validasi apakah NIK ada di database
    $stmt = $conn->prepare('SELECT id FROM users WHERE nomor_induk_kependudukan = ? LIMIT 1');
    $stmt->bind_param('s', $nik);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 1){
        // Dalam sistem nyata, biasanya dikirimkan kode OTP ke email/WA.
        // Untuk tahap pengembangan, kita bisa langsung arahkan ke halaman reset.
        $_SESSION['reset_nik'] = $nik;
        header('Location: reset_password.php');
        exit;
    } else {
        $err = 'NIK tidak terdaftar dalam sistem.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Desa Posi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/login.css" rel="stylesheet">
    <style>
        .login-header::before { display: none !important; }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }
        .logo-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="assets/luwu.png" alt="Logo Kabupaten Luwu" class="logo-img"
                     onerror="this.style.display='none';">
            </div>
            <h2>Desa Posi</h2>
            <p>Masukkan NIK Anda untuk mereset password</p>
        </div>
        <div class="login-body">
            <?php if($err): ?>
                <div class="alert alert-danger"><?php echo $err; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="nik" placeholder="NIK" required maxlength="16">
                    <label>👤 Nomor Induk Kependudukan (NIK)</label>
                </div>
                <button type="submit" class="btn w-100 py-2 text-white" style="background-color: #24ceff; border-color: #24ceff;">
                 Verifikasi NIK
                </button>
            </form>
            <div class="mt-3 text-center">
                <a href="login.php" class="text-decoration-none" style="color: #24ceff;">Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>