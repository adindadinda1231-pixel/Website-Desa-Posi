<?php
require_once __DIR__ . '/../config/koneksi.php';
// Pastikan file util.php ada untuk fungsi esc() atau hapus jika tidak digunakan
require_once __DIR__ . '/../helpers/util.php'; 
session_start();

// 1. Validasi Akses: Jika tidak ada sesi reset, lempar kembali ke halaman lupa password
if(!isset($_SESSION['reset_nik'])){
    header('Location: forgot_password.php');
    exit;
}

$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $new_pass = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if(strlen($new_pass) < 6){
        $err = 'Password minimal harus 6 karakter';
    } elseif($new_pass !== $confirm_pass){
        $err = 'Konfirmasi password tidak cocok';
    } else {
        // 2. Update Password di Database
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $nik = $_SESSION['reset_nik'];

        $stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE nomor_induk_kependudukan = ?');
        $stmt->bind_param('ss', $hashed, $nik);
        
        if($stmt->execute()){
            // Hapus sesi reset agar tidak bisa akses halaman ini lagi tanpa verifikasi ulang
            unset($_SESSION['reset_nik']);
            $success = 'Password berhasil diperbarui! Silakan login kembali.';
        } else {
            $err = 'Gagal memperbarui database. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Password - Desa Posi</title>
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
            <p>Silakan masukkan password baru Anda</p>
        </div>
        
        <div class="login-body">
            <?php if($err): ?>
                <div class="alert alert-danger"><strong>❌ Gagal!</strong> <?php echo $err; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <strong>✅ Berhasil!</strong> <?php echo $success; ?>
                    <hr>
                    <a href="login_masyarakat.php" class="btn btn-sm btn-outline-success">Ke Halaman Login</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password Baru" required>
                        <label for="password">🔒 Password Baru</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Konfirmasi Password" required>
                        <label for="confirm_password">🔄 Konfirmasi Password</label>
                    </div>
                    
                    <button type="submit" class="btn w-100 py-2 text-white" style="background-color: #24ceff; border-color: #24ceff;">
                     Update Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>