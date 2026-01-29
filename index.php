<?php 
session_start();
require 'config/database.php'; 
include 'includes/header.php'; 

// AMBIL DATA LAPANGAN (Logika tetap sama)
$query_lapangan = mysqli_query($conn, "SELECT * FROM lapangan");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');
    
    html { scroll-behavior: smooth; }
    body { font-family: 'Montserrat', sans-serif; background-color: #050505; color: white; }

    /* --- HERO --- */
    .hero-wrapper {
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        background: transparent; 
    }

    .hero-title {
        font-size: 5rem;
        font-weight: 900;
        text-transform: uppercase;
        background: linear-gradient(45deg, #fff, #e91e63);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: fadeInUp 1s both;
    }

    /* --- SECTION TITLE (PENYESUAIAN FONT BARU) --- */
    .section-title {
        font-size: 3.5rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -2px;
        margin-bottom: 10px;
    }

    .section-title span {
        background: linear-gradient(45deg, #fff, #e91e63);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .title-underline {
        width: 80px; 
        height: 5px; 
        background: #00f2ff; 
        margin: 0 auto 50px;
        border-radius: 10px;
    }

    /* --- SISANYA TETAP SAMA --- */
    .hero-subtitle { font-size: 1.4rem; opacity: 0.9; margin-bottom: 45px; animation: fadeInUp 1.2s both; animation-delay: 0.3s; }
    .btn-pink { background: #e91e63; color: white !important; padding: 18px 60px; font-weight: 800; border-radius: 12px; border: none; transition: 0.3s; text-decoration: none; display: inline-block; animation: fadeInUp 1.4s both; animation-delay: 0.6s; }
    .btn-pink:hover { transform: translateY(-8px); background: #ff2e7e; }
    .btn-outline-cyan { border: 3px solid #00f2ff; color: #00f2ff !important; padding: 18px 60px; font-weight: 800; border-radius: 12px; text-decoration: none; transition: 0.3s; display: inline-block; background: transparent; animation: fadeInUp 1.4s both; animation-delay: 0.6s; }
    .btn-outline-cyan:hover { background: #00f2ff; color: black !important; transform: translateY(-8px); }
    .court-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; overflow: hidden; transition: 0.4s; }
    .court-card:hover { transform: scale(1.03); border-color: #00f2ff; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<section class="hero-wrapper">
    <div class="container text-white">
        <h1 class="hero-title">TRINITY SPORT CENTER</h1>
        <p class="hero-subtitle">Seamless Booking for Your Ultimate Match</p>
        
        <div class="d-flex gap-4 justify-content-center">
            <a href="#lapangan" class="btn-pink">BOOK NOW</a>
            <a href="#footer-anchor" class="btn-outline-cyan">LEARN MORE</a>
        </div>
    </div>
</section>

<section id="lapangan" style="padding: 100px 0;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Arena <span>Pilihan</span></h2>
            <div class="title-underline"></div>
        </div>

        <div class="row g-4">
            <?php while($row = mysqli_fetch_assoc($query_lapangan)): ?>
                <div class="col-md-4 text-center">
                    <div class="court-card shadow-lg">
                        <img src="assets/images/<?= $row['foto']; ?>" class="w-100" style="height:220px; object-fit:cover;">
                        <div class="p-4">
                            <div class="badge bg-danger mb-2 text-uppercase"><?= $row['jenis']; ?></div>
                            <h4 class="fw-bold mb-3"><?= $row['nama_lapangan']; ?></h4>
                            <p style="color: #00f2ff;" class="fw-bold fs-4 mb-4">Rp <?= number_format($row['harga_per_jam'], 0, ',', '.') ?></p>
                            
                            <a href="user/booking.php?id=<?= $row['id_lapangan'] ?>" class="btn-pink w-100">BOOK NOW</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<div id="footer-anchor"></div>
<?php include 'includes/footer.php'; ?>