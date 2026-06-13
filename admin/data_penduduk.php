<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../helpers/util.php';
if(!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }

// Handle Delete (satuan)
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM penduduk WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $success_msg = "Data berhasil dihapus!";
    } else {
        $error_msg = "Gagal menghapus data!";
    }
}


// Handle Delete Semua — Baris 38
if(isset($_POST['delete_semua'])) {
    if($conn->query("DELETE FROM penduduk")) {
        $success_msg = "Semua data penduduk berhasil dihapus!";
    } else {
        $error_msg = "Gagal menghapus semua data!";
    }
}

// Handle Add/Edit
if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_semua'])) {
    $nik = trim($_POST['nik']);
    $nama = trim($_POST['nama']);
    $jk = $_POST['jk'];
    $agama = $_POST['agama'];
    $id = $_POST['id'] ?? null;
    
    if($id) {
        // Update
        $stmt = $conn->prepare("UPDATE penduduk SET nik=?, nama=?, jk=?, agama=? WHERE id=?");
        $stmt->bind_param("ssssi", $nik, $nama, $jk, $agama, $id);
        if($stmt->execute()) {
            $success_msg = "Data berhasil diupdate!";
        } else {
            $error_msg = "Gagal mengupdate data!";
        }
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO penduduk (nik, nama, jk, agama) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nik, $nama, $jk, $agama);
        if($stmt->execute()) {
            $success_msg = "Data berhasil ditambahkan!";
        } else {
            $error_msg = "Gagal menambahkan data! NIK mungkin sudah ada.";
        }
    }
}

// Get data for edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM penduduk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}

// Get all penduduk
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM penduduk WHERE 1=1";
$params = [];
$types = '';

if($search) {
    $query .= " AND (nik LIKE ? OR nama LIKE ? OR agama LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

$query .= " ORDER BY nama ASC";

if(count($params) > 0) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query($query);
}

$current_page = 'penduduk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Penduduk - Admin Desa Posi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="admin_styles.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="admin-navbar">
  <div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link d-lg-none text-white" type="button" onclick="toggleSidebar()">
          ☰
        </button>
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
          <img src="luwu.png" style="height: 35px;" class="me-2">
          <strong>Admin Dashboard</strong>
        </a>
      </div>
      
      <div class="d-flex gap-2 align-items-center">
        <a href="../public/index.php" class="btn btn-outline-primary btn-sm" target="_blank">
          Lihat Website
        </a>
        <a href="logout.php" class="btn btn-danger btn-sm">
          Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <ul class="sidebar-menu">
    <li class="sidebar-item">
      <a href="dashboard.php" class="sidebar-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
        Dashboard
      </a>
    </li>
    <li class="sidebar-item">
      <a href="data_penduduk.php" class="sidebar-link <?php echo $current_page === 'penduduk' ? 'active' : ''; ?>">
        Data Penduduk
      </a>
    </li>
    <li class="sidebar-item">
      <a href="edit_about.php" class="sidebar-link <?php echo $current_page === 'about' ? 'active' : ''; ?>">
        Tentang
      </a>
    </li>
    <li class="sidebar-item">
      <a href="edit_contact.php" class="sidebar-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>">
        Kontak
      </a>
    </li>
  </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
  <div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Data Penduduk</h4>
      <div class="d-flex gap-2 flex-wrap">
        <a href="import_penduduk.php" class="btn btn-warning btn-sm">
          Import File
        </a>
        <button type="button" class="btn btn-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#addModal">
          + Add
        </button>
      </div>
    </div>

    <?php if(isset($success_msg)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
    <?php if(isset($error_msg)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="row mb-4">
      <div class="col-md-6">
        <form method="GET" action="data_penduduk.php" class="search-box">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari berdasarkan NIK, Nama, atau Agama..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn" type="submit">Cari</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead style="background-color: #f8f9fa;">
              <tr>
                <th class="px-4 py-3">No</th>
                <th class="py-3">NIK</th>
                <th class="py-3">Nama</th>
                <th class="py-3">JK</th>
                <th class="py-3">Agama</th>
                <th class="py-3 text-center">Tindakan</th>
              </tr>
            </thead>
            <tbody>
              <?php if($res && $res->num_rows > 0): ?>
                <?php $no = 1; while($row = $res->fetch_assoc()): ?>
                  <tr>
                    <td class="px-4"><?php echo $no++; ?></td>
                    <td><?php echo esc($row['nik']); ?></td>
                    <td><?php echo esc($row['nama']); ?></td>
                    <td><?php echo esc($row['jk']); ?></td>
                    <td><?php echo esc($row['agama']); ?></td>
                    <td class="text-center">
                      <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                        ✏️
                      </a>
                      <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        ❌
                      </a>
                      
                      <!-- Edit Modal -->
                      <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Data Penduduk</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="data_penduduk.php">
                              <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="mb-3">
                                  <label class="form-label">NIK</label>
                                  <input type="text" class="form-control" name="nik" value="<?php echo esc($row['nik']); ?>" required>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Nama</label>
                                  <input type="text" class="form-control" name="nama" value="<?php echo esc($row['nama']); ?>" required>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Jenis Kelamin</label>
                                  <select class="form-select" name="jk" required>
                                    <option value="L" <?php echo $row['jk'] === 'L' ? 'selected' : ''; ?>>L</option>
                                    <option value="P" <?php echo $row['jk'] === 'P' ? 'selected' : ''; ?>>P</option>
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Agama</label>
                                  <select class="form-select" name="agama" required>
                                    <option value="Islam" <?php echo $row['agama'] === 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                    <option value="Kristen" <?php echo $row['agama'] === 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                                    <option value="Hindu" <?php echo $row['agama'] === 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                  </select>
                                </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    Tidak ada data penduduk
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Penduduk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="data_penduduk.php">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">NIK</label>
            <input type="text" class="form-control" name="nik" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Kelamin</label>
            <select class="form-select" name="jk" required>
              <option value="">Pilih...</option>
              <option value="L">L</option>
              <option value="P">P</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Agama</label>
            <select class="form-select" name="agama" required>
              <option value="">Pilih...</option>
              <option value="Islam">Islam</option>
              <option value="Kristen">Kristen</option>
              <option value="Hindu">Hindu</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}

</script>
</body>
</html>