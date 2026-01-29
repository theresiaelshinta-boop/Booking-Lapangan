<?php 
session_start();
require '../config/database.php'; 
include '../includes/header.php'; 

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query = mysqli_query($conn, "SELECT b.*, l.nama_lapangan, l.jenis 
                              FROM booking b
                              JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                              WHERE b.id_user = '$id_user' 
                              ORDER BY b.id_booking DESC");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');
    body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .riwayat-header { padding: 120px 0 40px; animation: fadeInUp 0.8s ease-out forwards; }
    .riwayat-title { font-size: 3.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: -2px; }
    .riwayat-title span { background: linear-gradient(45deg, #fff, #e91e63); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .history-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 25px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        opacity: 0; 
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .history-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.06);
        border-color: #e91e63;
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.15);
    }

    .status-badge { padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 0.65rem; text-transform: uppercase; }
    /* WARNA BARU DISINI */
    .status-blue { background: #007bff; color: white; box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); }
    .status-pending { background: #ffc107; color: black; box-shadow: 0 0 10px rgba(255, 193, 7, 0.3); }
    .status-default { background: #e91e63; color: white; } /* Untuk status lainnya seperti Batal dll */
    
    .price-tag { font-size: 1.6rem; font-weight: 900; color: white; margin-top: 15px; }
</style>

<div class="container riwayat-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="riwayat-title">RIWAYAT <span>BOOKING</span></h1>
            <p class="text-white-50">Cek status dan detail reservasi lapangan Anda.</p>
        </div>
        <a href="../index.php" class="btn btn-outline-light rounded-pill px-4 fw-bold shadow-sm" style="text-decoration:none; transition: 0.3s;">KEMBALI</a>
    </div>

    <div class="row g-4 mt-2">
        <?php 
        if(mysqli_num_rows($query) > 0): 
            $delay = 0.1; 
            while($row = mysqli_fetch_assoc($query)): 
                $tgl = $row['tanggal'];
                $jam_mulai_raw = $row['jam_mulai'];
                $mulai = date('H:i', strtotime($jam_mulai_raw));
                $selesai = date('H:i', strtotime($jam_mulai_raw . ' +1 hour'));
                
                $total = $row['total_harga'];
                $status = strtolower($row['status']);

                // LOGIKA WARNA BADGE SESUAI REQUEST
                if ($status == 'menunggu' || $status == 'menunggu konfirmasi') {
                    $badge_class = 'status-pending';
                } elseif ($status == 'dikonfirmasi' || $status == 'lunas') {
                    $badge_class = 'status-blue'; // JADI BIRU
                } else {
                    $badge_class = 'status-default'; // TETAP PINK
                }
            ?>
            <div class="col-md-6">
                <div class="history-card" style="animation-delay: <?= $delay; ?>s;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-white-50 fw-bold small">#<?= $row['kode_booking']; ?></div>
                        <span class="status-badge <?= $badge_class; ?>">
                            <?= $row['status']; ?>
                        </span>
                    </div>
                    
                    <h3 class="fw-bold mb-4 text-uppercase"><?= $row['nama_lapangan']; ?></h3>
                    
                    <div class="row g-0">
                        <div class="col-5 border-end border-secondary">
                            <div class="text-white-50 small mb-1">Tanggal</div>
                            <div class="fw-bold text-white"><?= date('d M Y', strtotime($tgl)); ?></div>
                        </div>
                        <div class="col-7 ps-4">
                            <div class="text-white-50 small mb-1">Durasi</div>
                            <div class="fw-bold" style="color: #e91e63;"><?= $mulai; ?> - <?= $selesai; ?> WIB</div>
                        </div>
                    </div>
                    
                    <div class="price-tag">Rp <?= number_format($total, 0, ',', '.'); ?></div>
                </div>
            </div>
            <?php 
                $delay += 0.1; 
            endwhile; 
            ?>
        <?php else: ?>
            <div class="col-12 text-center py-5" style="animation: fadeInUp 0.8s forwards;">
                <p class="text-white-50">Belum ada riwayat booking.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>