<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
session_start();

if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}

$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nik = trim($_POST['nomor_induk_kependudukan'] ?? '');
    $nama_input = trim($_POST['nama_lengkap'] ?? ''); // Nama yang diinput user saat daftar
    
    if(empty($nik) || empty($password) || empty($nama_input)){
        $err = 'Semua data wajib diisi!';
    } elseif($password !== $confirm_password){
        $err = 'Konfirmasi password tidak cocok!';
    } else {
        // 1. Cek apakah NIK terdaftar di tabel master 'penduduk'
        // Gunakan kolom 'nama', bukan 'nama_lengkap' sesuai file data_penduduk.php
        $stmt_master = $conn->prepare('SELECT nama FROM penduduk WHERE nik = ? LIMIT 1');
        $stmt_master->bind_param('s', $nik);
        $stmt_master->execute();
        $res_master = $stmt_master->get_result();

        if($res_master->num_rows === 0){
            $err = 'Maaf, NIK Anda tidak terdaftar sebagai warga resmi.';
        } else {
            // 2. Cek apakah NIK sudah punya akun di tabel 'users'
            $stmt_check = $conn->prepare('SELECT id FROM users WHERE nomor_induk_kependudukan = ? LIMIT 1');
            $stmt_check->bind_param('s', $nik);
            $stmt_check->execute();
            
            if($stmt_check->get_result()->num_rows > 0){
                $err = 'NIK ini sudah memiliki akun. Silakan login.';
            } else {
                // 3. Simpan ke tabel 'users' 
                // Pastikan jumlah '?' (3) sama dengan jumlah kolom (3)
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO users (password_hash, nomor_induk_kependudukan, nama_lengkap) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $password_hash, $nik, $nama_input);
                
                if($stmt->execute()){
                    $success = 'Pendaftaran berhasil! Silakan login.';
                    header('refresh:2;url=login.php');
                } else {
                    $err = 'Terjadi kesalahan saat menyimpan data.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Sistem Pengaduan Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/register.css" rel="stylesheet">
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
            <h2>DAFTAR AKUN</h2>
            <p>Khusus warga desa posi</p>
        </div>
        <div class="login-body">
            <?php if($err): ?>
                <div class="alert alert-danger alert-modern"><strong>❌ Error!</strong> <?php echo esc($err); ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success alert-modern"><strong>✅ Berhasil!</strong> <?php echo esc($success); ?></div>
            <?php endif; ?>

            <form id="registerForm" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nomor_induk_kependudukan" name="nomor_induk_kependudukan" placeholder="NIK" required>
                    <label for="nomor_induk_kependudukan">📝 NIK (16 Digit)</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama" required>
                    <label for="nama_lengkap">📝 Nama Lengkap</label>
                </div>
                <div class="form-floating mb-3" style="position: relative;">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">🔒 Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi" required>
                    <label for="confirm_password">🔒 Konfirmasi Password</label>
                </div>
                <button type="submit" class="btn-login">Daftar</button>
            </form>
            <div class="register-link mt-3 text-center">
                <a href="login.php">← Login di sini</a>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const nik = document.getElementById('nomor_induk_kependudukan').value.trim();
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (nik.length !== 16) {
        e.preventDefault();
        alert('❌ NIK harus tepat 16 digit!');
    } else if (pass !== confirm) {
        e.preventDefault();
        alert('❌ Konfirmasi password tidak cocok!');
    }
});
</script>
</body>
</html>