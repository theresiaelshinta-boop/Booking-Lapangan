<?php 
session_start();
require '../config/database.php';

// 1. PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$jam_sekarang = date('H:i:s');
$tgl_sekarang = date('Y-m-d');

// 2. LOGIKA DATA STATISTIK
$q_hari = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE LOWER(status) = 'dikonfirmasi' AND tanggal = '$tgl_sekarang'");
$res_hari = mysqli_fetch_assoc($q_hari);

$bln_sekarang = date('m'); $thn_sekarang = date('Y');
$q_bulan = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE LOWER(status) = 'dikonfirmasi' AND MONTH(tanggal) = '$bln_sekarang' AND YEAR(tanggal) = '$thn_sekarang'");
$res_bulan = mysqli_fetch_assoc($q_bulan);

$q_total = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE LOWER(status) = 'dikonfirmasi'");
$res_total = mysqli_fetch_assoc($q_total);

// 3. QUERY DATA UTAMA
$query_recent = mysqli_query($conn, "SELECT b.*, l.nama_lapangan, u.nama FROM booking b JOIN lapangan l ON b.id_lapangan = l.id_lapangan JOIN users u ON b.id_user = u.id_user ORDER BY b.id_booking DESC LIMIT 5");
$q_lapangan = mysqli_query($conn, "SELECT * FROM lapangan");
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status = 'menunggu' OR status = 'menunggu_konfirmasi'");
$res_pending = mysqli_fetch_assoc($q_pending);

// 4. DATA GRAFIK 7 HARI
$labels = []; $data_pendapatan = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d M', strtotime($date));
    $q_grafik = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE LOWER(status) = 'dikonfirmasi' AND tanggal = '$date'");
    $res_grafik = mysqli_fetch_assoc($q_grafik);
    $labels[] = $display_date;
    $data_pendapatan[] = $res_grafik['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Trinity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }
        
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
        
        .card-stat { 
            border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); 
            position: relative; overflow: hidden; height: 100%; 
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            display: block; text-decoration: none !important;
        }
        .card-stat:hover { transform: translateY(-10px); border-color: #00f2ff; box-shadow: 0 15px 30px rgba(0, 242, 255, 0.2); }
        
        .bg-gradient-1 { background: linear-gradient(45deg, #00b0ff, #004e92); } 
        .bg-gradient-2 { background: linear-gradient(45deg, #6a11cb, #2575fc); } 
        .bg-gradient-3 { background: linear-gradient(45deg, #00c853, #1b5e20); } 
        
        .icon-bg { position: absolute; right: -10px; bottom: -10px; font-size: 4.5rem; opacity: 0.25; color: #fff; }
        
        .table-card, .chart-card { background: #111; border-radius: 20px; border: 1px solid #222; overflow: hidden; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Animasi Lonceng Real-time */
        .animate-bell { display: inline-block; animation: ring 2s infinite; }
        @keyframes ring {
            0% { transform: rotate(0); }
            10% { transform: rotate(30deg); }
            20% { transform: rotate(-28deg); }
            30% { transform: rotate(34deg); }
            40% { transform: rotate(-32deg); }
            50% { transform: rotate(30deg); }
            100% { transform: rotate(0); }
        }
    </style>
</head>
<body>

<div class="sidebar-admin p-4">
    <h5 class="fw-bold text-info mb-5">TRINITY ADMIN</h5>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link active mb-2">
            <i class="bi bi-grid-1x2-fill me-3"></i> Dashboard
        </a>
        <a href="lapangan.php" class="nav-link mb-2">
            <i class="bi bi-trophy-fill me-3"></i> Lapangan
        </a>
        <a href="konfirmasi.php" class="nav-link mb-2 justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-credit-card-2-front-fill me-3"></i> Konfirmasi
            </div>
            <span id="badge-pending" class="badge bg-danger rounded-pill" style="font-size: 0.7rem; display: <?= ($res_pending['jml'] > 0) ? 'inline-block' : 'none' ?>;">
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
    <div class="mb-5">
        <h3 class="fw-bold text-white mb-0">Selamat Datang, <span class="text-info"><?= $_SESSION['nama'] ?></span></h3>
        <p class="text-white-50 small">Ringkasan performa Trinity Sport Center hari ini.</p>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-md-4">
            <div class="card card-stat bg-gradient-1 p-4 text-white">
                <small class="fw-bold">HARI INI</small>
                <h2 class="mt-2 fw-bold">Rp <?= number_format($res_hari['total'] ?? 0, 0, ',', '.') ?></h2>
                <i class="bi bi-cash-stack icon-bg"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat bg-gradient-2 p-4 text-white">
                <small class="fw-bold">BULAN INI</small>
                <h2 class="mt-2 fw-bold">Rp <?= number_format($res_bulan['total'] ?? 0, 0, ',', '.') ?></h2>
                <i class="bi bi-wallet2 icon-bg"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat bg-gradient-3 p-4 text-white">
                <small class="fw-bold">TOTAL PENDAPATAN</small>
                <h2 class="mt-2 fw-bold">Rp <?= number_format($res_total['total'] ?? 0, 0, ',', '.') ?></h2>
                <i class="bi bi-graph-up-arrow icon-bg"></i>
            </div>
        </div>
    </div>

    <div class="chart-card p-4 mb-5 shadow-lg">
        <h6 class="fw-bold mb-3 text-white-50 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Tren Pendapatan (7 Hari Terakhir)</h6>
        <canvas id="revenueChart" style="max-height: 220px;"></canvas>
    </div>

    <h5 class="fw-bold mb-4 text-white"><i class="bi bi-clock-history me-2 text-info"></i>Transaksi Terbaru</h5>
    <div class="table-card mb-5 shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-white-50 small">
                        <th class="ps-4">PELANGGAN</th>
                        <th>LAPANGAN</th>
                        <th class="text-center">BUKTI</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query_recent)): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-white"><?= htmlspecialchars($row['nama']) ?></div>
                            <small class="text-info">#<?= $row['id_booking'] ?></small>
                        </td>
                        <td><?= $row['nama_lapangan'] ?></td>
                        <td class="text-center">
                            <?php if(!empty($row['bukti_bayar'])): ?>
                                <img src="../assets/images/<?= $row['bukti_bayar'] ?>" width="40" height="40" style="object-fit: cover;" class="rounded border border-secondary">
                            <?php else: ?> <span class="text-muted small">No File</span> <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill <?= (strtolower($row['status']) == 'dikonfirmasi') ? 'bg-success' : 'bg-warning text-dark' ?> px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h5 class="fw-bold mb-4 text-white"><i class="bi bi-trophy me-2 text-info"></i>Daftar Lapangan</h5>
    <div class="row g-4">
        <?php mysqli_data_seek($q_lapangan, 0); while($lap = mysqli_fetch_assoc($q_lapangan)): 
            $id_lap = $lap['id_lapangan'];
            $sql_monitor = "SELECT * FROM booking WHERE id_lapangan = '$id_lap' AND tanggal = '$tgl_sekarang' AND '$jam_sekarang' BETWEEN jam_mulai AND jam_selesai AND LOWER(status) = 'dikonfirmasi'";
            $is_used = mysqli_num_rows(mysqli_query($conn, $sql_monitor)) > 0;
        ?>
        <div class="col-md-4">
            <div class="card-stat p-3" style="background: #111;">
                <div class="mb-2"><span class="badge <?= $is_used ? 'bg-danger' : 'bg-success' ?> rounded-pill"><?= $is_used ? 'DIPAKAI' : 'TERSEDIA' ?></span></div>
                <img src="../assets/images/<?= $lap['foto']; ?>" class="rounded-3 mb-3 w-100" style="height: 150px; object-fit: cover; border: 1px solid #333;">
                <h6 class="fw-bold text-white mb-1"><?= $lap['nama_lapangan'] ?></h6>
                <p class="text-white-50 small mb-3">Rp <?= number_format($lap['harga_per_jam'], 0, ',', '.') ?> / Jam</p>
                <a href="edit_lapangan.php?id=<?= $lap['id_lapangan'] ?>" class="btn btn-outline-info btn-sm w-100 rounded-pill fw-bold">Edit Lapangan</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="bookingToast" class="toast align-items-center text-white bg-info border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 15px;">
        <div class="d-flex p-2">
            <div class="toast-body fs-6">
                <i class="bi bi-bell-fill me-2 animate-bell"></i> 
                <strong>Trinity Sport:</strong> Pesanan baru telah masuk!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<audio id="notifSound">
    <source src="https://cdn.pixabay.com/audio/2021/08/04/audio_0625c1539c.mp3" type="audio/mpeg">
</audio>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. LOGIKA GRAFIK ASLIMU
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(0, 242, 255, 0.2)'); gradient.addColorStop(1, 'rgba(0, 242, 255, 0)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Pendapatan', data: <?= json_encode($data_pendapatan) ?>,
                borderColor: '#00f2ff', backgroundColor: gradient, fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4, pointBackgroundColor: '#00f2ff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)', size: 10 } },
                x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.4)', size: 10 } }
            }
        }
    });

    // 2. LOGIKA REAL-TIME (LONCENG)
    let currentPendingCount = <?= (int)$res_pending['jml'] ?>;
    
    function checkNewOrders() {
        fetch('get_pending_count.php')
            .then(response => response.json())
            .then(data => {
                if (parseInt(data.jml) > currentPendingCount) {
                    // Tampilkan Toast Lonceng
                    const toast = new bootstrap.Toast(document.getElementById('bookingToast'));
                    toast.show();
                    
                    // Bunyikan Suara
                    document.getElementById('notifSound').play().catch(() => {});
                    
                    // Update Badge Sidebar
                    const badge = document.getElementById('badge-pending');
                    if(badge) {
                        badge.innerText = data.jml;
                        badge.style.display = 'inline-block';
                    }
                    
                    currentPendingCount = parseInt(data.jml);
                }
            });
    }

    // Cek setiap 5 detik
    setInterval(checkNewOrders, 5000);
</script>
</body>
</html>