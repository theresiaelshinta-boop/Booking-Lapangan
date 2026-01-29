<?php
session_start();
require '../config/database.php';

// 1. PERBAIKAN PROTEKSI: Arahkan ke luar folder jika belum login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php"); // Tambah ../ agar kembali ke root
    exit();
}

$id_user = $_SESSION['id_user'];
// Ambil nama dari session yang benar (sesuaikan dengan login.php mu, biasanya 'nama')
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Sobat Trinity';

/** * STATISTIK REAL-TIME
 */
$stat_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE id_user = '$id_user' AND status = 'menunggu'"));
$stat_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE id_user = '$id_user' AND status = 'menunggu_konfirmasi'"));

include '../includes/header.php';
?>

<style>
    /* CSS kamu sudah bagus, Seng. Saya tambahkan sedikit agar navigasi tidak tertutup */
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap');
    
    body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }

    /* Pastikan Header (Navbar) selalu di depan dan bisa diklik */
    header, .navbar {
        position: relative;
        z-index: 9999 !important;
    }

    /* Sisanya kode CSS animasimu (sudah mantap) */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes floating { 0% { transform: translateY(0px); } 50% { transform: translateY(-12px); } 100% { transform: translateY(0px); } }
    @keyframes borderGlow { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

    .animate-in { animation: fadeInUp 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; }
    .delay-1 { animation-delay: 0.1s; opacity: 0; }
    .delay-2 { animation-delay: 0.3s; opacity: 0; }
    .delay-3 { animation-delay: 0.5s; opacity: 0; }
    .floating-icon { animation: floating 3s ease-in-out infinite; }

    .glow-box { position: relative; z-index: 1; height: 100%; transition: 0.4s; border-radius: 32px; }
    .glow-box::before {
        content: ""; position: absolute; top: -2px; left: -2px; right: -2px; bottom: -2px;
        z-index: -1; border-radius: 32px; animation: borderGlow 6s linear infinite; opacity: 0.6; background-size: 400%;
    }
    .glow-pink::before { background: linear-gradient(45deg, #e91e63, #111, #e91e63, #ff6090); }
    .glow-blue::before { background: linear-gradient(45deg, #00f2ff, #111, #00f2ff, #004e92); }
    .glow-green::before { background: linear-gradient(45deg, #4caf50, #111, #4caf50, #00ff88); }
    .glow-orange::before { background: linear-gradient(45deg, #ff9800, #111, #ff9800, #ff5722); }

    .welcome-banner {
        background: linear-gradient(135deg, #121416 0%, #000000 100%);
        border: 1px solid #222; border-radius: 35px; padding: 40px; margin-bottom: 40px;
        border-left: 6px solid #e91e63; box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .card-menu {
        background: #121416; border-radius: 30px; padding: 35px; transition: 0.4s;
        text-decoration: none !important; display: block; height: 100%; border: 1px solid rgba(255,255,255,0.05);
    }
    .glow-box:hover { transform: translateY(-15px); }
    .glow-box:hover::before { opacity: 1; filter: brightness(1.2); }
    .icon-box { width: 70px; height: 70px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 25px; }
    .text-pink { color: #e91e63 !important; }
    .fw-black { font-weight: 900; }
</style>

<div class="container mt-5 pt-4 mb-5">
    <div class="welcome-banner animate-in">
        <div class="row align-items-center">
            <div class="col-md-8 text-center text-md-start">
                <h1 class="fw-black mb-1 text-uppercase">Halo, <span class="text-pink"><?= htmlspecialchars($nama_user) ?></span>!</h1>
                <p class="text-white-50 fs-5 mb-0">Siap untuk pamer skill di lapangan hari ini?</p>
            </div>
            <div class="col-md-4 text-md-end d-none d-md-block">
                <div class="small text-white-50">TRINITY SPORT SYSTEM</div>
                <div class="fw-bold text-pink"><?= date('l, d F Y') ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="glow-box glow-pink animate-in delay-1">
                <a href="booking.php" class="card-menu">
                    <div class="icon-box floating-icon" style="background: rgba(233, 30, 99, 0.1); color: #e91e63;">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </div>
                    <h3 class="text-white fw-black text-uppercase">Pesan Arena</h3>
                    <p class="text-white-50">Cek jadwal kosong dan amankan slot mainmu.</p>
                    <div class="mt-4 text-pink fw-bold">BOOKING SEKARANG <i class="bi bi-arrow-right ms-1"></i></div>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glow-box glow-blue animate-in delay-2">
                <a href="riwayat.php" class="card-menu">
                    <div class="icon-box floating-icon" style="background: rgba(0, 242, 255, 0.1); color: #00f2ff;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="text-white fw-black text-uppercase d-flex align-items-center justify-content-between">
                        Riwayat
                        <?php if($stat_proses['jml'] > 0): ?>
                            <span class="badge rounded-pill bg-info text-dark" style="font-size: 0.7rem;"><?= $stat_proses['jml'] ?> PROSES</span>
                        <?php endif; ?>
                    </h3>
                    <p class="text-white-50">Pantau verifikasi admin jadwalmu.</p>
                    <div class="mt-4 fw-bold" style="color:#00f2ff">CEK JADWAL <i class="bi bi-arrow-right ms-1"></i></div>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glow-box <?= ($stat_pending['jml'] > 0) ? 'glow-orange' : 'glow-green' ?> animate-in delay-3">
                <?php if($stat_pending['jml'] > 0): ?>
                    <a href="riwayat.php" class="card-menu">
                        <div class="icon-box floating-icon" style="background: rgba(255, 152, 0, 0.1); color: #ff9800;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h3 class="text-white fw-black text-uppercase">Butuh Bayar</h3>
                        <p class="text-white-50">Kamu punya <b><?= $stat_pending['jml'] ?></b> tagihan tertunda.</p>
                        <div class="mt-4 fw-bold" style="color:#ff9800">SELESAIKAN <i class="bi bi-arrow-right ms-1"></i></div>
                    </a>
                <?php else: ?>
                    <div class="card-menu">
                        <div class="icon-box floating-icon" style="background: rgba(76, 175, 80, 0.1); color: #4caf50;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h3 class="text-white fw-black text-uppercase">Siap Main!</h3>
                        <p class="text-white-50">Semua pesanan aman. Stamina sudah siap?</p>
                        <div class="mt-4 text-success fw-bold">LUNAS <i class="bi bi-check2-all ms-1"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>