<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';

// Redirect if already logged in
if(isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Create default admin if none exists
$c = $conn->query('SELECT COUNT(*) as c FROM admins')->fetch_assoc();
if($c['c']==0){
    $u='admin'; 
    $p=password_hash('admin123', PASSWORD_DEFAULT);
    $s = $conn->prepare('INSERT INTO admins (username, password_hash) VALUES (?,?)');
    $s->bind_param('ss',$u,$p); 
    $s->execute(); 
    $s->close();
}

$err='';
$success='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $u = $_POST['username'] ?? ''; 
    $p = $_POST['password'] ?? '';
    
    if($u && $p) {
        $stmt = $conn->prepare('SELECT id,password_hash FROM admins WHERE username=? LIMIT 1');
        $stmt->bind_param('s',$u); 
        $stmt->execute(); 
        $res=$stmt->get_result();
        
        if($res->num_rows===1){ 
            $row=$res->fetch_assoc(); 
            if(password_verify($p,$row['password_hash'])){ 
                $_SESSION['admin_id']=$row['id']; 
                header('Location: dashboard.php'); 
                exit; 
            }
        }
        $err='Username atau password salah';
    } else {
        $err='Mohon isi semua field';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - Desa Posi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="login_styles.css" rel="stylesheet">
</head>
<body>

<div class="login-container">
  <div class="login-card">
    
    <div class="login-header">
      <div class="login-icon" style="background: none; border-radius: 0;">
        <img src="luwu.png" alt="Logo Luwu" style="width: 150px; height: auto;">
      </div>
      <h1 class="login-title">Admin Panel</h1>
      <p class="login-subtitle">Sistem Pengaduan Masyarakat - Desa Posi</p>
    </div>

    <?php if($err): ?>
      <div class="alert alert-danger">
        <strong>❌ Login Gagal!</strong><br>
        <?php echo htmlspecialchars($err); ?>
      </div>
    <?php endif; ?>

    <?php if($c['c']==1 && !$err && $_SERVER['REQUEST_METHOD']!=='POST'): ?>
      <div class="alert alert-info">
        <strong>ℹ️ Akun Default Dibuat</strong><br>
        Gunakan kredensial di bawah untuk login pertama kali
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      
      <div class="form-floating">
        <input type="text" 
               class="form-control" 
               id="username" 
               name="username" 
               placeholder="Username"
               required
               autofocus
               autocomplete="username">
        <label for="username">👤 Username</label>
      </div>

      <div class="form-floating">
        <input type="password" 
               class="form-control" 
               id="password" 
               name="password" 
               placeholder="Password"
               required
               autocomplete="current-password">
        <label for="password">🔒 Password</label>
      </div>

      <button type="submit" class="btn btn-login">
        Masuk ke Dashboard
      </button>

    </form>


  </div>

  <div class="back-link">
    <a href="../public/index.php">
      ← Kembali ke Beranda
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>