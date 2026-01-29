<?php 
session_start();
require '../config/database.php';

// 1. PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil jumlah pending untuk badge lonceng di sidebar
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status = 'menunggu' OR status = 'menunggu_konfirmasi'");
$res_pending = mysqli_fetch_assoc($q_pending);

// 2. LOGIKA TAMBAH
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $newName = "lapangan_" . time() . "." . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    if (move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/images/" . $newName)) {
        mysqli_query($conn, "INSERT INTO lapangan (nama_lapangan, jenis, harga_per_jam, foto) VALUES ('$nama', '$jenis', '$harga', '$newName')");
        echo "<script>window.location='lapangan.php';</script>";
    }
}

// 3. LOGIKA HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM lapangan WHERE id_lapangan = '$id'"));
    if ($data['foto'] && file_exists("../assets/images/" . $data['foto'])) { unlink("../assets/images/" . $data['foto']); }
    mysqli_query($conn, "DELETE FROM lapangan WHERE id_lapangan = '$id'");
    echo "<script>window.location='lapangan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Lapangan - Trinity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }
        
        /* SIDEBAR IDENTIK DENGAN DASHBOARD */
        .sidebar-admin { background: #000; min-height: 100vh; border-right: 1px solid #222; position: fixed; width: 16%; z-index: 100; }
        
        .nav-link { 
            color: rgba(255, 255, 255, 0.4) !important; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            padding: 12px 15px; border-radius: 12px; margin-bottom: 5px; 
            text-decoration: none; display: flex; align-items: center; 
        }
        
        .nav-link:hover { color: #00f2ff !important; background: rgba(0, 242, 255, 0.05); transform: translateX(8px); }
        .nav-link.active { color: #00f2ff !important; background: rgba(0, 242, 255, 0.1); font-weight: 600; }
        
        /* STYLE LOGOUT IDENTIK DASHBOARD */
        .nav-link.text-danger:hover { 
            color: #ff4d4d !important; 
            background: rgba(255, 77, 77, 0.1); 
            box-shadow: 0 0 15px rgba(255, 77, 77, 0.2);
            transform: translateX(8px);
        }

        .main-content { margin-left: 16%; padding: 40px; animation: fadeIn 0.8s ease-out; }
        
        /* LIST BARIS MANJANG */
        .list-row { 
            background: #111; border: 1px solid #222; border-radius: 18px; 
            margin-bottom: 12px; padding: 15px 25px; display: flex; align-items: center; 
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .list-row:hover { border-color: #00f2ff; background: #161616; transform: scale(1.01); box-shadow: 0 10px 20px rgba(0, 242, 255, 0.1); }

        .img-list { width: 85px; height: 55px; object-fit: cover; border-radius: 10px; margin-right: 25px; border: 1px solid #333; }
        .info-name { flex: 2; }
        .info-price { flex: 1; color: #00f2ff; font-weight: 700; text-align: right; margin-right: 40px; }
        
        .btn-action { 
            padding: 10px; border-radius: 12px; transition: 0.3s; 
            background: rgba(255,255,255,0.05); border: 1px solid #222; color: white;
        }
        .btn-edit:hover { color: #00f2ff; border-color: #00f2ff; background: rgba(0, 242, 255, 0.1); }
        .btn-del:hover { color: #ff4d4d; border-color: #ff4d4d; background: rgba(255, 77, 77, 0.1); }

        .modal-content { background: #111; border: 1px solid #333; color: white; border-radius: 24px; padding: 10px; }
        .form-control, .form-select { background: #000 !important; border: 1px solid #333 !important; color: white !important; border-radius: 12px; padding: 12px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="sidebar-admin p-4">
    <h5 class="fw-bold text-info mb-5">TRINITY ADMIN</h5>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link mb-2">
            <i class="bi bi-grid-1x2-fill me-3"></i> Dashboard
        </a>
        <a href="lapangan.php" class="nav-link active mb-2">
            <i class="bi bi-trophy-fill me-3"></i> Lapangan
        </a>
        <a href="konfirmasi.php" class="nav-link mb-2 justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-credit-card-2-front-fill me-3"></i> Konfirmasi
            </div>
            <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem; display: <?= ($res_pending['jml'] > 0) ? 'inline-block' : 'none' ?>;">
                <?= $res_pending['jml'] ?>
            </span>
        </a>
        <a href="laporan.php" class="nav-link mb-2">
            <i class="bi bi-file-earmark-bar-graph-fill me-3"></i> Laporan
        </a>
        <hr class="border-secondary my-4">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left me-3"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-white mb-0">Kelola <span class="text-info">Lapangan</span></h3>
            <p class="text-white-50 small">Update data dan harga lapangan Trinity.</p>
        </div>
        <button class="btn btn-info px-4 fw-bold rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-2"></i> TAMBAH BARU
        </button>
    </div>

    <div class="list-container">
        <?php
        $query = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id_lapangan DESC");
        while($row = mysqli_fetch_assoc($query)):
        ?>
        <div class="list-row">
            <img src="../assets/images/<?= $row['foto']; ?>" class="img-list">
            
            <div class="info-name">
                <div class="fw-bold text-white fs-5"><?= $row['nama_lapangan']; ?></div>
                <span class="badge rounded-pill border border-secondary text-uppercase text-white-50" style="font-size: 0.65rem; letter-spacing: 1px;">
                    <?= $row['jenis']; ?>
                </span>
            </div>

            <div class="info-price">
                <span class="small text-white-50 fw-normal">Rp</span> <?= number_format($row['harga_per_jam'], 0, ',', '.'); ?>
            </div>

            <div class="actions">
                <a href="edit_lapangan.php?id=<?= $row['id_lapangan']; ?>" class="btn-action btn-edit text-decoration-none">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="?hapus=<?= $row['id_lapangan']; ?>" class="btn-action btn-del text-decoration-none" onclick="return confirm('Hapus lapangan ini?')">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4 text-center">
                    <i class="bi bi-plus-circle text-info mb-3 d-block" style="font-size: 2.5rem;"></i>
                    <h5 class="fw-bold mb-4">Tambah Lapangan Baru</h5>
                    
                    <div class="text-start">
                        <div class="mb-3">
                            <label class="small fw-bold text-white-50 mb-2">NAMA LAPANGAN</label>
                            <input type="text" name="nama_lapangan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-white-50 mb-2">JENIS OLAHRAGA</label>
                            <select name="jenis" class="form-select">
                                <option value="futsal">Futsal</option>
                                <option value="badminton">Badminton</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-white-50 mb-2">HARGA SEWA / JAM</label>
                            <input type="number" name="harga" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-white-50 mb-2">FOTO LAPANGAN</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-dark w-100 rounded-pill py-2" data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" name="tambah" class="btn btn-info w-100 rounded-pill py-2 fw-bold">SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>