<?php
// ============================================================
// FILE: import_penduduk.php
// FUNGSI: Import data penduduk dari Excel "Buku Induk Penduduk"
// LETAKKAN di: admin/import_penduduk.php
// BUTUH: helpers/SimpleXLSX.php
// ============================================================

session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
require_once __DIR__ . '/../helpers/SimpleXLSX.php';

if(!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }

// ============================================================
// TABEL KONVERSI ID → TEKS
// ============================================================

// Baris 18: Konversi id_jenis_kelamin (1=Laki-laki, 2=Perempuan)
// Sesuaikan jika kode di database berbeda
$map_jk = [
    '0' => 'L',
    '1' => 'L',   // Laki-laki
    '2' => 'P',   // Perempuan
    // Jika ada nilai lain, tambahkan di sini
];

// Baris 25: Konversi id_agama (angka → teks)
// Sesuaikan dengan tabel referensi agama di database Anda
$map_agama = [
    '0' => 'Islam',
    '1' => 'Islam',
    '2' => 'Kristen',
    '3' => 'Katolik',
    '4' => 'Hindu',
    '5' => 'Buddha',
    '6' => 'Konghucu',
    // Tambahkan ID agama lain jika ada
];

// ============================================================
// FUNGSI CLEANING
// ============================================================

// Baris 38: Bersihkan NIK dari scientific notation (misal: 7.31718E+15)
function cleanNIK($val) {
    $val = trim((string)$val);
    if (stripos($val, 'E+') !== false || stripos($val, 'E-') !== false) {
        $val = number_format((float)$val, 0, '', '');
    }
    $val = preg_replace('/[^0-9]/', '', $val);
    return $val;
}

// Baris 47: Kapitalisasi proper nama (ADENG → Adeng)
function cleanNama($val) {
    return ucwords(strtolower(trim((string)$val)));
}

// ============================================================
// PROSES IMPORT
// ============================================================
$success_msg = null;
$error_msg   = null;
$preview     = [];    // untuk tampilkan 5 baris hasil parsing
$total_rows  = 0;

if (isset($_POST['import']) && isset($_FILES['file_excel'])) {

    if ($_FILES['file_excel']['error'] !== 0) {
        $error_msg = "Gagal upload file. Coba lagi.";
    } else {
        $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'xlsx') {
            $error_msg = "Hanya file .xlsx yang didukung!";
        } elseif ($xlsx = \SimpleXLSX::parse($_FILES['file_excel']['tmp_name'])) {

            $all_rows = $xlsx->rows();

            // --------------------------------------------------------
            // STEP 1: Temukan baris header (yang berisi kolom 'nik')
            // Baris 73: Sistem cari otomatis baris header meskipun ada
            // judul/sub-judul di atas (seperti "BUKU INDUK PENDUDUK")
            // --------------------------------------------------------
            $header_row_index = -1;
            $col = ['nik' => -1, 'nama' => -1, 'jk' => -1, 'agama' => -1];

            foreach ($all_rows as $i => $row) {
                if (!is_array($row)) continue;
                foreach ($row as $ci => $cell) {
                    $h = strtolower(trim((string)$cell));

                    // Baris 82: Kata kunci pencarian kolom NIK
                    // Tambahkan kata kunci lain jika nama kolom berbeda
                    if (in_array($h, ['nik', 'no_nik', 'nomor_induk', 'ktp', 'no_ktp'])) {
                        $col['nik'] = $ci;
                    }
                    // Baris 87: Kata kunci kolom Nama
                    if (in_array($h, ['nama', 'nama_lengkap', 'name', 'nama_penduduk'])) {
                        $col['nama'] = $ci;
                    }
                    // Baris 91: Kata kunci kolom Jenis Kelamin
                    // File ini pakai 'id_jenis_kelamin' berisi angka 1/2
                    if (in_array($h, ['jk', 'jenis_kelamin', 'id_jenis_kelamin', 'gender', 'sex', 'kelamin'])) {
                        $col['jk'] = $ci;
                    }
                    // Baris 96: Kata kunci kolom Agama
                    // File ini pakai 'id_agama' berisi angka 1-6
                    if (in_array($h, ['agama', 'id_agama', 'religion', 'kepercayaan'])) {
                        $col['agama'] = $ci;
                    }
                }
                // Jika NIK dan Nama sudah ketemu → baris ini adalah header
                if ($col['nik'] !== -1 && $col['nama'] !== -1) {
                    $header_row_index = $i;
                    break;
                }
            }

            // --------------------------------------------------------
            // Validasi: header tidak ditemukan
            // --------------------------------------------------------
            if ($header_row_index === -1) {
                $error_msg = "Sistem tidak menemukan kolom NIK atau Nama di file Excel Anda. 
                              Pastikan ada baris header yang mengandung kata 'nik' dan 'nama'.";
            } else {

                // --------------------------------------------------------
                // STEP 2: Proses data mulai dari baris setelah header
                // --------------------------------------------------------
                $inserted   = 0;
                $updated    = 0;
                $skipped    = 0;

                // Baris 118: Ambil baris data (setelah baris header)
                $data_rows = array_slice($all_rows, $header_row_index + 1);

                foreach ($data_rows as $row) {
                    if (!is_array($row)) continue;

                    // Ambil nilai tiap kolom dengan aman
                    $nik_raw   = isset($row[$col['nik']])   ? trim((string)$row[$col['nik']])   : '';
                    $nama_raw  = isset($row[$col['nama']])  ? trim((string)$row[$col['nama']])  : '';
                    $jk_raw    = isset($row[$col['jk']])    ? trim((string)$row[$col['jk']])    : '';
                    $agama_raw = isset($row[$col['agama']]) ? trim((string)$row[$col['agama']]) : '';

                    // Lewati baris kosong
                    if (empty($nik_raw) && empty($nama_raw)) { continue; }
                    if (empty($nik_raw)) { $skipped++; continue; }

                    // ------------------------------------------------
                    // CLEANING & KONVERSI
                    // ------------------------------------------------

                    // Baris 135: Bersihkan NIK
                    $nik = cleanNIK($nik_raw);

                    // Baris 138: Bersihkan Nama
                    $nama = cleanNama($nama_raw);

                    // Baris 141: Konversi JK: angka 1/2 → L/P
                    // Jika sudah berupa 'L'/'P' langsung, juga ditangani
                    if (isset($map_jk[$jk_raw])) {
                        $jk = $map_jk[$jk_raw];
                    } elseif (in_array(strtoupper($jk_raw), ['L', 'P'])) {
                        $jk = strtoupper($jk_raw);
                    } elseif (stripos($jk_raw, 'laki') !== false || stripos($jk_raw, 'male') !== false) {
                        $jk = 'L';
                    } elseif (stripos($jk_raw, 'perempuan') !== false || stripos($jk_raw, 'wanita') !== false || stripos($jk_raw, 'female') !== false) {
                        $jk = 'P';
                    } else {
                        $jk = 'L'; // default jika tidak dikenali
                    }

                    // Baris 155: Konversi Agama: angka → teks
                    // Jika sudah berupa teks langsung (misal "Islam"), dipakai langsung
                    if (isset($map_agama[$agama_raw])) {
                        $agama = $map_agama[$agama_raw];
                    } elseif (!empty($agama_raw) && !is_numeric($agama_raw)) {
                        $agama = ucfirst(strtolower($agama_raw));
                    } else {
                        $agama = '-'; // default jika id tidak dikenali
                    }

                    // ------------------------------------------------
                    // Simpan preview 5 baris pertama (untuk ditampilkan)
                    // ------------------------------------------------
                    if (count($preview) < 5) {
                        $preview[] = [
                            'nik'   => $nik,
                            'nama'  => $nama,
                            'jk'    => $jk,
                            'agama' => $agama,
                        ];
                    }
                    $total_rows++;

                    // ------------------------------------------------
                    // Simpan ke database
                    // Baris 175: ON DUPLICATE KEY UPDATE → jika NIK sudah
                    // ada, data diupdate. Ganti ke INSERT IGNORE jika tidak
                    // mau data lama tertimpa.
                    // ------------------------------------------------
                    $stmt = $conn->prepare(
                        "INSERT INTO penduduk (nik, nama, jk, agama)
                         VALUES (?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE
                            nama  = VALUES(nama),
                            jk    = VALUES(jk),
                            agama = VALUES(agama)"
                    );
                    $stmt->bind_param("ssss", $nik, $nama, $jk, $agama);

                    if ($stmt->execute()) {
                        // affected_rows: 1=insert baru, 2=update, 0=tidak berubah
                        if ($stmt->affected_rows >= 1) {
                            if ($conn->info && strpos($conn->info, 'Records: 1') !== false) {
                                $inserted++;
                            } else {
                                // Untuk sederhananya, hitung semua sebagai inserted
                                $inserted++;
                            }
                        }
                    } else {
                        $skipped++;
                    }
                }

                if ($total_rows === 0) {
                    $error_msg = "Tidak ada data yang berhasil dibaca. Pastikan file tidak kosong.";
                } else {
                    $success_msg = "Import selesai! 
                        <strong>$inserted</strong> data diproses dari total 
                        <strong>$total_rows</strong> baris" .
                        ($skipped > 0 ? ", <strong>$skipped</strong> baris dilewati (kosong/error)." : ".");
                }
            }

        } else {
            $error_msg = "Gagal membaca file Excel: " . \SimpleXLSX::parseError();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Import Data Penduduk - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="admin_styles.css" rel="stylesheet">
  <style>
    .preview-table th { background: #2D6A4F; color: #fff; font-size: 13px; }
    .preview-table td { font-size: 13px; vertical-align: middle; }
    .badge-l { background:#dbeafe; color:#1d4ed8; padding:3px 8px; border-radius:4px; font-size:12px; }
    .badge-p { background:#fce7f3; color:#9d174d; padding:3px 8px; border-radius:4px; font-size:12px; }
    .upload-zone {
      border: 2px dashed #b7e4c7; border-radius: 12px;
      padding: 2.5rem; text-align: center; cursor: pointer;
      background: #f8fffe; transition: all .2s;
    }
    .upload-zone:hover { border-color: #2D6A4F; background: #f0faf4; }
    .info-box { background:#f0faf4; border:1px solid #b7e4c7; border-radius:8px; padding:1rem; font-size:13px; }
    .konversi-badge { display:inline-block; background:#e8f5e9; border:1px solid #a5d6a7;
      border-radius:5px; padding:2px 8px; font-size:12px; margin:2px; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="admin-navbar">
  <div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link d-lg-none text-white" type="button" onclick="toggleSidebar()">☰</button>
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
          <img src="luwu.png" style="height:35px;" class="me-2">
          <strong>Admin Dashboard</strong>
        </a>
      </div>
      <div class="d-flex gap-2">
        <a href="../public/index.php" class="btn btn-outline-primary btn-sm" target="_blank">Lihat Website</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <ul class="sidebar-menu">
    <li class="sidebar-item"><a href="dashboard.php" class="sidebar-link">Dashboard</a></li>
    <li class="sidebar-item"><a href="data_penduduk.php" class="sidebar-link active">Data Penduduk</a></li>
    <li class="sidebar-item"><a href="edit_about.php" class="sidebar-link">Tentang</a></li>
    <li class="sidebar-item"><a href="edit_contact.php" class="sidebar-link">Kontak</a></li>
  </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
<div class="container-fluid px-4 pb-5">

  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="data_penduduk.php" class="btn btn-outline-secondary btn-sm">← Kembali</a>
    <h4 class="mb-0">Import Data Penduduk</h4>
  </div>

  <?php if($success_msg): ?>
    <!-- ============================================================
         HASIL IMPORT — tampil setelah proses selesai
         ============================================================ -->
    <div class="alert alert-success alert-dismissible fade show">
      ✅ <?= $success_msg ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <?php if(!empty($preview)): ?>
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body p-4">
        <h6 class="mb-3">Preview 5 Data Pertama yang Diimport</h6>
        <div class="table-responsive">
          <table class="table table-bordered preview-table">
            <thead>
              <tr>
                <th>NIK</th><th>Nama</th><th>JK</th><th>Agama</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($preview as $p): ?>
              <tr>
                <td><code><?= htmlspecialchars($p['nik']) ?></code></td>
                <td><?= htmlspecialchars($p['nama']) ?></td>
                <td>
                  <span class="<?= $p['jk']==='L' ? 'badge-l' : 'badge-p' ?>">
                    <?= $p['jk']==='L' ? 'L' : 'P' ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($p['agama']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <a href="data_penduduk.php" class="btn btn-success me-2">Lihat Semua Data</a>
        <a href="import_penduduk.php" class="btn btn-outline-secondary">Import Lagi</a>
      </div>
    </div>
    <?php endif; ?>

  <?php else: ?>

    <?php if($error_msg): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        ⚠️ <?= htmlspecialchars($error_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- 1 kotak upload -->
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h6 class="mb-3">📂 Upload File Excel</h6>
            <form method="POST" enctype="multipart/form-data" id="importForm" onsubmit="return validateForm()">
              <div class="upload-zone mb-3" onclick="document.getElementById('excelInput').click()">
                <div style="font-size:2.5rem" class="mb-2">📊</div>
                <div class="fw-semibold mb-1">Klik untuk pilih file Excel</div>
                <div class="text-muted small">Format: .xlsx</div>
                <div class="text-muted small mt-1" id="fileName">Belum ada file dipilih</div>
              </div>
              <input type="file" id="excelInput" name="file_excel" accept=".xlsx" class="d-none"
                     onchange="onFileChosen(this)">
              <div id="fileAlert" class="alert alert-warning py-2 small mb-3 d-none">
                ⚠️ Pilih file Excel (.xlsx) terlebih dahulu.
              </div>
              <button type="submit" name="import" class="btn btn-success w-100">
                Mulai Import
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}

// Dipanggil saat file dipilih lewat input
function onFileChosen(input) {
  if (input.files && input.files[0]) {
    document.getElementById('fileName').textContent = '📄 ' + input.files[0].name;
    document.getElementById('fileAlert').classList.add('d-none');
  }
}

// Validasi sebelum form di-submit — tampilkan peringatan jika belum pilih file
function validateForm() {
  const input = document.getElementById('excelInput');
  if (!input.files || input.files.length === 0) {
    document.getElementById('fileAlert').classList.remove('d-none');
    return false; // cegah submit
  }
  return true; // lanjut submit
}

// Drag & drop ke upload zone
const zone = document.querySelector('.upload-zone');
if (zone) {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = '#2D6A4F'; });
  zone.addEventListener('dragleave', () => { zone.style.borderColor = '#b7e4c7'; });
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor = '#b7e4c7';
    const f = e.dataTransfer.files[0];
    if (f) {
      document.getElementById('excelInput').files = e.dataTransfer.files;
      onFileChosen(document.getElementById('excelInput'));
    }
  });
}
</script>
</body>
</html>