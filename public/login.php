<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();

if(isset($_SESSION['user_id'])){
    header('Location: login_masyarakat.php');
    exit;
}

$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $u = trim($_POST['username'] ?? ''); // Input NIK dari form
    $p = $_POST['password'] ?? '';
    
    if(empty($u) || empty($p)){
        $err = 'NIK dan password harus diisi';
    } else {
        // Gunakan nomor_induk_kependudukan, bukan username
        $stmt = $conn->prepare('SELECT id, password_hash, nama_lengkap FROM users WHERE nomor_induk_kependudukan = ? LIMIT 1');
        $stmt->bind_param('s', $u);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if($res->num_rows === 1){
            $row = $res->fetch_assoc();
            if(password_verify($p, $row['password_hash'])){
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                $_SESSION['nik'] = $u;
                header('Location: login_masyarakat.php');
                exit;
            }
        }
        $err = 'NIK atau password salah';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Pengaduan Desa Posi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/login.css" rel="stylesheet">
    <style>
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }
        .logo-img {
            width: 90px;
            height: 90px;
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
                <img 
                    src="assets/luwu.png" 
                    alt="Logo Kabupaten Luwu" 
                    class="logo-img"
                    onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block';"
                >
                <div id="logo-fallback" style="display:none; font-size:3rem;">🏛️</div>
            </div>
            <h2>Desa Posi</h2>
            <p>Masuk ke akun Anda untuk melanjutkan</p>
        </div>
        
        <div class="login-body">
            <?php if($err): ?>
                <div class="alert alert-danger alert-modern">
                    <strong>❌ Error!</strong> <?php echo esc($err); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success alert-modern">
                    <strong>✅ Berhasil!</strong> <?php echo esc($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="form-floating">
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Username" 
                           required 
                           autocomplete="username"
                           autofocus>
                    <label for="username">👤 Nomor Induk Kependudukan </label>
                </div>
                
                <div class="form-floating" style="position: relative;">
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Password" 
                           required
                           autocomplete="current-password">
                    <label for="password">🔒 Password</label>
                    <span class="password-toggle" onclick="togglePassword()">👁️</span>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
            </div>
            <a href="lupa_password.php" class="text-decoration-none small" style="color: #24ceff;">Lupa Password?</a>
            </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                     Masuk Sekarang
                </button>
            </form>
            
            <div class="divider">
                <span>atau</span>
            </div>
            
            <div class="register-link">
                <p class="text-muted mb-2">Belum punya akun?</p>
                <a href="register.php">
                    Daftar Sekarang <span>→</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="back-home">
        <a href="index.php">
            <span>←</span> Kembali ke Beranda
        </a>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}

// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    
    if (username.length < 16) {
        e.preventDefault();
        alert('❌ NIK harus terdiri dari 16 digit');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('❌ Password minimal 6 karakter');
        return false;
    }
});


// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-modern');
    alerts.forEach(function(alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(function() {
            alert.remove();
        }, 300);
    });
}, 5000);
</script>

</body>
</html>