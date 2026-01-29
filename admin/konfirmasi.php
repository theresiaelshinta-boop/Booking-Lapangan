<?php 
session_start();
require '../config/database.php';

// 1. PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// 2. LOGIKA UPDATE STATUS
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_booking = $_GET['id'];
    $status_baru = ($_GET['aksi'] == 'setuju') ? 'dikonfirmasi' : 'batal';
    
    mysqli_query($conn, "UPDATE booking SET status = '$status_baru' WHERE id_booking = '$id_booking'");
    echo "<script>alert('Status Berhasil Diperbarui!'); window.location='konfirmasi.php';</script>";
}

// 3. LOGIKA FILTER WIDGET
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$where_sql = "";
if ($filter == 'menunggu') {
    $where_sql = "WHERE b.status = 'menunggu' OR b.status = 'menunggu konfirmasi' OR b.status = 'menunggu_konfirmasi'";
} elseif ($filter == 'berhasil') {
    $where_sql = "WHERE b.status = 'dikonfirmasi' OR b.status = 'berhasil'";
} elseif ($filter == 'batal') {
    $where_sql = "WHERE b.status = 'batal'";
}

// 4. QUERY DATA
$query = mysqli_query($conn, "SELECT b.*, l.nama_lapangan, u.nama AS nama_pelanggan 
                              FROM booking b 
                              JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                              JOIN users u ON b.id_user = u.id_user 
                              $where_sql
                              ORDER BY b.id_booking ASC");

// 5. HITUNG STATISTIK
$res_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status LIKE 'menunggu%'"));
$res_berhasil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status = 'dikonfirmasi' OR status = 'berhasil'"));
$res_batal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status = 'batal'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi - Trinity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }
        .sidebar-admin { background: #000; min-height: 100vh; border-right: 1px solid #222; position: fixed; width: 16%; z-index: 100; }
        .nav-link { color: rgba(255, 255, 255, 0.4) !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); padding: 12px 15px; border-radius: 12px; margin-bottom: 5px; text-decoration: none; display: flex; align-items: center; }
        .nav-link:hover { color: #00f2ff !important; background: rgba(0, 242, 255, 0.05); transform: translateX(8px); }
        .nav-link.active { color: #00f2ff !important; background: rgba(0, 242, 255, 0.1); font-weight: 600; }

        /* --- EFEK HOVER & KLIK LOGOUT --- */
        .nav-link.text-danger:hover { 
            color: #ff4d4d !important; 
            background: rgba(255, 77, 77, 0.1) !important; 
            transform: translateX(8px); 
        }
        .nav-link.text-danger:active { 
            color: #ff0000 !important; 
            background: rgba(255, 77, 77, 0.2) !important; 
            transform: scale(0.95);
        }

        .main-content { margin-left: 16%; padding: 40px; animation: fadeIn 0.8s ease-out; }
        .card-stat { border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); position: relative; overflow: hidden; height: 100%; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: block; text-decoration: none !important; }
        .card-stat:hover { transform: translateY(-10px); border-color: #00f2ff; box-shadow: 0 15px 30px rgba(0, 242, 255, 0.2); }
        .card-stat h2, .card-stat small { color: #ffffff !important; font-weight: 700; }
        .bg-gradient-pending { background: linear-gradient(45deg, #ff9800, #ff5722); }
        .bg-gradient-success { background: linear-gradient(45deg, #00b0ff, #004e92); }
        .bg-gradient-batal { background: linear-gradient(45deg, #333, #111); }
        .icon-bg { position: absolute; right: -10px; bottom: -10px; font-size: 4.5rem; opacity: 0.25; color: #fff; }
        .table-card { background: #111; border-radius: 20px; border: 1px solid #222; overflow: hidden; }
        .img-proof { width: 45px; height: 45px; object-fit: cover; border-radius: 10px; border: 2px solid #00f2ff; transition: 0.3s; }
        .img-proof:hover { transform: scale(1.1); }
        .modal-content { background: #111; border: 1px solid #333; border-radius: 25px; color: white; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="sidebar-admin p-4">
    <h5 class="fw-bold text-info mb-5">TRINITY ADMIN</h5>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link mb-2"><i class="bi bi-grid-1x2-fill me-3"></i> Dashboard</a>
         <a href="lapangan.php" class="nav-link mb-2"><i class="bi bi-trophy-fill me-3"></i> Lapangan
        <a href="konfirmasi.php" class="nav-link active mb-2 justify-content-between">
            <div class="d-flex align-items-center"><i class="bi bi-credit-card-2-front-fill me-3"></i> Konfirmasi</div>
            <?php if($res_pending['jml'] > 0): ?>
                <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;"><?= $res_pending['jml'] ?></span>
            <?php endif; ?>
        </a>
        <a href="laporan.php" class="nav-link mb-2"><i class="bi bi-file-earmark-bar-graph-fill me-3"></i> Laporan</a>
        <hr class="border-secondary my-4">
        <a href="../logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="mb-5">
        <h3 class="fw-bold text-white mb-0">Manajemen <span class="text-info">Konfirmasi</span></h3>
        <p class="text-white-50 small">Verifikasi pembayaran yang masuk dari user secara real-time.</p>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-md-4">
            <a href="konfirmasi.php?filter=menunggu" class="card-stat bg-gradient-pending p-4">
                <small>PERLU DICEK</small>
                <h2 class="mt-2 text-white"><?= $res_pending['jml'] ?> Booking</h2>
                <i class="bi bi-hourglass-split icon-bg"></i>
            </a>
        </div>
        <div class="col-md-4">
            <a href="konfirmasi.php?filter=berhasil" class="card-stat bg-gradient-success p-4">
                <small>DISETUJUI</small>
                <h2 class="mt-2 text-white"><?= $res_berhasil['jml'] ?> Booking</h2>
                <i class="bi bi-check-circle-fill icon-bg"></i>
            </a>
        </div>
        <div class="col-md-4">
            <a href="konfirmasi.php?filter=batal" class="card-stat bg-gradient-batal p-4">
                <small>DIBATALKAN</small>
                <h2 class="mt-2 text-white"><?= $res_batal['jml'] ?> Booking</h2>
                <i class="bi bi-x-circle icon-bg"></i>
            </a>
        </div>
    </div>

    <div class="table-card shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-white-50 small">
                        <th class="ps-4">PELANGGAN</th>
                        <th>JADWAL</th>
                        <th class="text-center">BUKTI</th>
                        <th>TOTAL</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-white"><?= htmlspecialchars($row['nama_pelanggan']) ?></div>
                                <small class="text-info">#<?= $row['kode_booking'] ?></small>
                            </td>
                            <td>
                                <small class="text-white-50 d-block"><?= date('d M Y', strtotime($row['tanggal'])) ?></small>
                                <span class="badge bg-dark border border-secondary text-info"><?= $row['jam_mulai'] ?> WIB</span>
                            </td>
                            <td class="text-center">
                                <?php if($row['bukti_bayar']): ?>
                                    <img src="../assets/images/<?= $row['bukti_bayar'] ?>" class="img-proof shadow" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $row['id_booking'] ?>" style="cursor:pointer">
                                <?php else: ?>
                                    <span class="text-white-50 small">No File</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-info">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <?php if(strpos($row['status'], 'menunggu') !== false): ?>
                                    <a href="konfirmasi.php?aksi=setuju&id=<?= $row['id_booking'] ?>" class="btn btn-info btn-sm px-3 rounded-pill text-dark fw-bold" onclick="return confirm('Setujui pesanan ini?')">Terima</a>
                                    <a href="konfirmasi.php?aksi=tolak&id=<?= $row['id_booking'] ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill ms-1" onclick="return confirm('Tolak pesanan ini?')">Tolak</a>
                                <?php else: ?>
                                    <span class="badge rounded-pill <?= ($row['status']=='dikonfirmasi' || $row['status']=='berhasil') ? 'bg-success' : 'bg-danger' ?> px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                        <?= $row['status'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalDetail<?= $row['id_booking'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content shadow-lg">
                                    <div class="modal-header border-secondary">
                                        <h5 class="fw-bold text-white mb-0">Verifikasi <span class="text-info">#<?= $row['kode_booking'] ?></span></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        <img src="../assets/images/<?= $row['bukti_bayar'] ?>" class="img-fluid rounded-4 shadow mb-3 border border-secondary">
                                        <div class="text-start bg-dark p-3 rounded-4 border border-secondary">
                                            <div class="mb-3">
                                                <small class="text-white-50 d-block">Arena Dipesan:</small>
                                                <b class="text-info" style="font-size: 1.1rem;"><?= strtoupper($row['nama_lapangan']) ?></b>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-white-50 d-block">Nama Pelanggan</small>
                                                    <b class="text-white"><?= $row['nama_pelanggan'] ?></b>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <small class="text-white-50 d-block">Total Bayar</small>
                                                    <b class="text-info">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-white-50">Data tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>