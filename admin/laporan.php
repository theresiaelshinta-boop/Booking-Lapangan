<?php 
session_start();
require '../config/database.php';

// 1. PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// --- LOGIKA DATA LAMA (TETAP SAMA) ---
$filter_tgl = isset($_GET['tgl']) ? $_GET['tgl'] : '';
$filter_bln = isset($_GET['bln']) ? $_GET['bln'] : '';
$filter_thn = isset($_GET['thn']) ? $_GET['thn'] : '';

$where_clause = "WHERE b.status = 'dikonfirmasi'";

if (!empty($filter_tgl)) {
    $where_clause .= " AND b.tanggal = '$filter_tgl'";
} elseif (!empty($filter_bln) && !empty($filter_thn)) {
    $where_clause .= " AND MONTH(b.tanggal) = '$filter_bln' AND YEAR(b.tanggal) = '$filter_thn'";
} elseif (!empty($filter_thn)) {
    $where_clause .= " AND YEAR(b.tanggal) = '$filter_thn'";
}

$total_pendapatan = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking b $where_clause");
$rekap = mysqli_fetch_assoc($total_pendapatan);

$query = mysqli_query($conn, "SELECT b.*, u.nama, l.nama_lapangan 
                              FROM booking b 
                              LEFT JOIN users u ON b.id_user = u.id_user 
                              LEFT JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                              $where_clause
                              ORDER BY b.tanggal DESC, b.jam_mulai DESC");

$q_pending = mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status = 'menunggu' OR status = 'menunggu_konfirmasi'");
$res_pending = mysqli_fetch_assoc($q_pending);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan - Trinity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }
        
        /* --- SIDEBAR SYNC --- */
        .sidebar-admin { background: #000; min-height: 100vh; border-right: 1px solid #222; position: fixed; width: 16%; z-index: 100; }
        .nav-link { 
            color: rgba(255, 255, 255, 0.4) !important; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            padding: 12px 15px; border-radius: 12px; margin-bottom: 5px; 
            text-decoration: none; display: flex; align-items: center; 
        }
        .nav-link:hover { color: #00f2ff !important; background: rgba(0, 242, 255, 0.05); transform: translateX(8px); }
        .nav-link.active { color: #00f2ff !important; background: rgba(0, 242, 255, 0.1); font-weight: 600; }
        .nav-link.text-danger:hover { color: #ff4d4d !important; background: rgba(255, 77, 77, 0.05); }

        .main-content { margin-left: 16%; padding: 40px; animation: fadeIn 0.8s ease-out; }
        
        /* --- WIDGETS SYNC --- */
        .card-stat { 
            border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); 
            position: relative; overflow: hidden; height: 100%; 
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            display: block; text-decoration: none !important;
        }
        .card-stat:hover { transform: translateY(-10px); border-color: #00f2ff; box-shadow: 0 15px 30px rgba(0, 242, 255, 0.2); }
        
        .icon-bg { position: absolute; right: -10px; bottom: -10px; font-size: 4.5rem; opacity: 0.25; color: #fff; }
        
        .filter-card, .table-card { background: #111; border: 1px solid #222; border-radius: 20px; }
        .form-control-custom { 
            background: #000 !important; border: 1px solid #333 !important; 
            color: white !important; border-radius: 12px; padding: 10px 15px;
        }
        .form-control-custom:focus { border-color: #00f2ff !important; box-shadow: none; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        @media print { 
            .sidebar-admin, .no-print, .filter-card { display: none !important; } 
            .main-content { margin: 0; padding: 0; width: 100%; }
            body { background: white; color: black; }
            .table-card { border: none; }
            .table { color: black !important; }
        }
    </style>
</head>
<body>

<div class="sidebar-admin p-4 no-print">
    <h5 class="fw-bold text-info mb-5">TRINITY ADMIN</h5>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link mb-2">
            <i class="bi bi-grid-1x2-fill me-3"></i> Dashboard
        </a>
         <a href="lapangan.php" class="nav-link mb-2">
            <i class="bi bi-trophy-fill me-3"></i> Lapangan
        <a href="konfirmasi.php" class="nav-link mb-2 justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-credit-card-2-front-fill me-3"></i> Konfirmasi
            </div>
            <?php if($res_pending['jml'] > 0): ?>
                <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;"><?= $res_pending['jml'] ?></span>
            <?php endif; ?>
        </a>
        <a href="laporan.php" class="nav-link active mb-2">
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
            <h3 class="fw-bold mb-0">Laporan <span class="text-info">Pendapatan</span></h3>
            <p class="text-white-50 small">Kelola dan pantau arus kas masuk Trinity.</p>
        </div>
        <div class="d-flex gap-2 no-print">
            <a href="export_excel.php?tgl=<?= $filter_tgl ?>&bln=<?= $filter_bln ?>&thn=<?= $filter_thn ?>" class="btn btn-success btn-sm px-4 rounded-pill fw-bold">
                <i class="bi bi-file-earmark-excel me-2"></i> Excel
            </a>
            <button onclick="window.print()" class="btn btn-outline-info btn-sm px-4 rounded-pill fw-bold">
                <i class="bi bi-printer me-2"></i> Cetak PDF
            </button>
        </div>
    </div>

    <div class="filter-card p-4 mb-5 no-print shadow-sm">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="small text-white-50 mb-2">Filter Harian</label>
                <input type="date" name="tgl" class="form-control form-control-custom" value="<?= $filter_tgl ?>">
            </div>
            <div class="col-md-3">
                <label class="small text-white-50 mb-2">Filter Bulanan</label>
                <select name="bln" class="form-select form-control-custom">
                    <option value="">Semua Bulan</option>
                    <?php 
                    $bulan = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                    for($i=1; $i<=12; $i++) {
                        $selected = ($filter_bln == $i) ? 'selected' : '';
                        echo "<option value='$i' $selected>".$bulan[$i-1]."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="small text-white-50 mb-2">Tahun</label>
                <input type="number" name="thn" class="form-control form-control-custom" placeholder="2026" value="<?= $filter_thn ? $filter_thn : date('Y') ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-info w-100 fw-bold rounded-pill text-dark">TERAPKAN</button>
                <a href="laporan.php" class="btn btn-outline-secondary w-100 rounded-pill fw-bold">RESET</a>
            </div>
        </form>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-md-6">
            <div class="card-stat p-4" style="background: linear-gradient(45deg, #004e92, #000428);">
                <small class="text-white-50 fw-bold text-uppercase" style="letter-spacing: 1px;">TOTAL PENDAPATAN</small>
                <h2 class="fw-bold text-white mt-2">Rp <?= number_format($rekap['total'] ?? 0, 0, ',', '.'); ?></h2>
                <i class="bi bi-wallet2 icon-bg" style="opacity: 0.2;"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-stat p-4" style="background: #111;">
                <small class="text-white-50 fw-bold text-uppercase" style="letter-spacing: 1px;">TRANSAKSI BERHASIL</small>
                <h2 class="fw-bold text-white mt-2"><?= mysqli_num_rows($query); ?> Pesanan</h2>
                <i class="bi bi-clipboard-check icon-bg"></i>
            </div>
        </div>
    </div>

    <div class="table-card shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-white-50 small">
                        <th class="ps-4 py-3">NO</th>
                        <th class="py-3">PELANGGAN</th>
                        <th class="py-3">LAPANGAN</th>
                        <th class="py-3">JADWAL</th>
                        <th class="py-3 text-end pe-4">TOTAL BAYAR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): $no=1; ?>
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td class="ps-4 text-white-50"><?= $no++; ?></td>
                            <td>
                                <div class="fw-bold text-white"><?= htmlspecialchars($row['nama'] ?? 'User'); ?></div>
                                <small class="text-info">#<?= $row['kode_booking']; ?></small>
                            </td>
                            <td><span class="badge bg-dark border border-secondary text-info"><?= $row['nama_lapangan']; ?></span></td>
                            <td>
                                <small class="d-block text-white-50"><?= date('d/m/Y', strtotime($row['tanggal'])); ?></small>
                                <span class="small"><?= $row['jam_mulai']; ?>:00 WIB</span>
                            </td>
                            <td class="text-end pe-4 fw-bold text-info">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-white-50">Data tidak ditemukan untuk filter ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>