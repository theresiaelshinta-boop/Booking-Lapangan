<?php 
session_start();
require '../config/database.php';

// 1. SET TIMEZONE INDONESIA
date_default_timezone_set('Asia/Jakarta');

// PROTEKSI ADMIN
if (!isset($_SESSION['id_admin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

// LOGIKA UPDATE STATUS
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_booking = $_GET['id'];
    $status_baru = ($_GET['aksi'] == 'setuju') ? 'dikonfirmasi' : 'batal';
    
    mysqli_query($conn, "UPDATE booking SET status = '$status_baru' WHERE id_booking = '$id_booking'");
    echo "<script>alert('Status Berhasil Diperbarui!'); window.location='konfirmasi.php';</script>";
}

// LOGIKA FILTER
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$where_sql = "";
if ($filter == 'menunggu') {
    $where_sql = "WHERE b.status LIKE 'menunggu%'";
} elseif ($filter == 'berhasil') {
    $where_sql = "WHERE b.status = 'dikonfirmasi' OR b.status = 'berhasil'";
} elseif ($filter == 'batal') {
    $where_sql = "WHERE b.status = 'batal'";
}

// QUERY DATA (ORDER BY DESC: Terbaru di atas)
$query = mysqli_query($conn, "SELECT b.*, l.nama_lapangan, u.nama AS nama_pelanggan 
                              FROM booking b 
                              JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                              JOIN users u ON b.id_user = u.id_user 
                              $where_sql
                              ORDER BY b.id_booking DESC");

// HITUNG STATISTIK WIDGET
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
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap');
        body { background-color: #0d0d0d; color: white; font-family: 'Montserrat', sans-serif; }
        
        .sidebar-admin { background: #000; min-height: 100vh; border-right: 1px solid #222; position: fixed; width: 16%; z-index: 100; }
        .nav-link { color: rgba(255, 255, 255, 0.4) !important; padding: 12px 15px; transition: 0.3s; display: flex; align-items: center; text-decoration: none; border-radius: 12px; }
        .nav-link:hover:not(.text-danger) { color: #00f2ff !important; background: rgba(0, 242, 255, 0.05); }
        .nav-link.active { color: #00f2ff !important; background: rgba(0, 242, 255, 0.1); font-weight: 600; }
        .nav-link.text-danger:hover { color: #ff4d4d !important; background: rgba(255, 77, 77, 0.1) !important; transform: translateX(8px); }

        .main-content { margin-left: 16%; padding: 40px; }

        /* WIDGET MINIMALIS */
        .card-stat { border-radius: 20px; border: none; position: relative; overflow: hidden; height: 110px; display: flex; flex-direction: column; justify-content: center; padding: 20px; transition: 0.3s; text-decoration: none !important; }
        .card-stat:hover { transform: translateY(-3px); filter: brightness(1.1); }
        .card-stat small { font-weight: 700; color: #fff; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; opacity: 0.9; }
        .card-stat h2 { font-weight: 900; font-size: 1.6rem; color: #fff; margin: 0; z-index: 2; }

        .bg-orange { background: #ff7e00; }
        .bg-blue-bright { background: #00a2ff; }
        .bg-dark-grey { background: #222222; }

        /* Icon Tetap Besar & Posisi Disesuaikan */
        .icon-stat { position: absolute; right: 15px; bottom: -5px; font-size: 4rem; color: rgba(255,255,255,0.25); pointer-events: none; z-index: 1; }

        /* TABLE ROWS STYLE */
        .table-card { background: transparent; margin-top: 30px; }
        .table { border-collapse: separate; border-spacing: 0 10px; }
        .table tr { background: #161616; transition: 0.2s; }
        .table tr:hover { background: #1d1d1d; }
        .table td { border: none; padding: 15px 20px; vertical-align: middle; }
        .table td:first-child { border-radius: 12px 0 0 12px; }
        .table td:last-child { border-radius: 0 12px 12px 0; }
        
        .img-proof { width: 45px; height: 45px; object-fit: cover; border-radius: 10px; cursor: pointer; border: 1px solid #333; transition: 0.3s; }
        .img-proof:hover { transform: scale(1.1); border-color: #00f2ff; }

        /* MODAL CUSTOM */
        .modal-content { background: #111; border: 1px solid #333; border-radius: 20px; }
        .detail-box { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 12px; margin-top: 15px; border: 1px dashed #444; }
    </style>
</head>
<body>

<div class="sidebar-admin p-4">
    <a href="dashboard.php" class="text-decoration-none">
        <h5 class="fw-bold text-info mb-5">TRINITY ADMIN</h5>
    </a>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link mb-2"><i class="bi bi-grid-1x2-fill me-3"></i> Dashboard</a>
        <a href="lapangan.php" class="nav-link mb-2"><i class="bi bi-trophy-fill me-3"></i> Lapangan</a>
        <a href="konfirmasi.php" class="nav-link active mb-2"><i class="bi bi-credit-card-2-front-fill me-3"></i> Konfirmasi</a>
        <a href="laporan.php" class="nav-link mb-2"><i class="bi bi-file-earmark-bar-graph-fill me-3"></i> Laporan</a>
        <hr class="border-secondary my-4">
        <a href="logout_admin.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-3"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4">
        <h2 class="fw-bold text-white mb-0">Manajemen <span class="text-info">Konfirmasi</span></h2>
        <p class="text-white-50 small">Verifikasi data terbaru yang masuk.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="konfirmasi.php?filter=menunggu" class="card-stat bg-orange shadow-sm">
                <small>PERLU DICEK</small>
                <h2><?= $res_pending['jml'] ?> Booking</h2>
                <i class="bi bi-hourglass-split icon-stat"></i>
            </a>
        </div>
        <div class="col-md-4">
            <a href="konfirmasi.php?filter=berhasil" class="card-stat bg-blue-bright shadow-sm">
                <small>DISETUJUI</small>
                <h2><?= $res_berhasil['jml'] ?> Booking</h2>
                <i class="bi bi-check-circle-fill icon-stat"></i>
            </a>
        </div>
        <div class="col-md-4">
            <a href="konfirmasi.php?filter=batal" class="card-stat bg-dark-grey shadow-sm">
                <small>DIBATALKAN</small>
                <h2><?= $res_batal['jml'] ?> Booking</h2>
                <i class="bi bi-x-circle-fill icon-stat"></i>
            </a>
        </div>
    </div>

    <div class="table-card">
        <table class="table table-dark">
            <thead>
                <tr class="text-white-50 small" style="font-size: 0.75rem;">
                    <th class="ps-4">PELANGGAN</th>
                    <th>JADWAL</th>
                    <th>BUKTI</th>
                    <th>TOTAL</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold"><?= $row['nama_pelanggan'] ?></div>
                        <small class="text-info" style="font-size: 0.7rem;">#<?= $row['kode_booking'] ?></small>
                    </td>
                    <td>
                        <div class="fw-bold" style="font-size: 0.9rem;"><?= date('d F Y', strtotime($row['tanggal'])) ?></div>
                        <div class="text-info small" style="font-size: 0.75rem;"><?= $row['jam_mulai'] ?> WIB</div>
                    </td>
                    <td>
                        <?php if($row['bukti_bayar']): ?>
                            <img src="../assets/images/bukti/<?= $row['bukti_bayar'] ?>" class="img-proof shadow-sm" data-bs-toggle="modal" data-bs-target="#img<?= $row['id_booking'] ?>">
                        <?php else: ?>
                            <span class="text-white-50 small" style="font-size: 0.75rem;">No File</span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold text-info">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <?php if(!$row['bukti_bayar']): ?>
                            <span class="text-warning small text-uppercase fw-bold" style="font-size: 0.65rem;"><u>menunggu pembayaran</u></span>
                        <?php elseif(strpos($row['status'], 'menunggu') !== false): ?>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="konfirmasi.php?aksi=setuju&id=<?= $row['id_booking'] ?>" class="btn btn-info btn-sm rounded-pill px-3 fw-bold text-dark" style="font-size: 0.75rem;">Terima</a>
                                <a href="konfirmasi.php?aksi=tolak&id=<?= $row['id_booking'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" style="font-size: 0.75rem;">Tolak</a>
                            </div>
                        <?php else: ?>
                            <span class="badge py-2 px-3 <?= ($row['status'] == 'dikonfirmasi' || $row['status'] == 'berhasil') ? 'bg-success' : 'bg-danger' ?> text-uppercase" style="font-size: 0.65rem;">
                                <?= $row['status'] ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>

                <div class="modal fade" id="img<?= $row['id_booking'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="modal-title fw-bold text-white">Detail #<?= $row['kode_booking'] ?></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 text-center">
                                <img src="../assets/images/bukti/<?= $row['bukti_bayar'] ?>" class="img-fluid rounded-3 border border-secondary mb-3">
                                <div class="detail-box text-start">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <small class="text-white-50 d-block">Lapangan:</small>
                                            <span class="fw-bold text-info text-uppercase" style="font-size: 0.85rem;"><?= $row['nama_lapangan'] ?></span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <small class="text-white-50 d-block">Total Bayar:</small>
                                            <span class="fw-bold text-success" style="font-size: 1rem;">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(strpos($row['status'], 'menunggu') !== false): ?>
                            <div class="modal-footer border-0 justify-content-center pb-4">
                                <a href="konfirmasi.php?aksi=setuju&id=<?= $row['id_booking'] ?>" class="btn btn-info px-4 rounded-pill fw-bold text-dark">TERIMA PEMBAYARAN</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>