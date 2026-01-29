<?php
// LOGIKA DINAMIS: Deteksi folder agar link tidak 404 (Sesuai logika sebelumnya)
$current_dir = dirname($_SERVER['PHP_SELF']);
$base_url = (basename($current_dir) == 'user' || basename($current_dir) == 'admin') ? "../" : "";
?>

<style>
    .main-footer {
        background: rgba(0, 0, 0, 0.95);
        border-top: 1px solid rgba(233, 30, 99, 0.3); /* Garis pink tipis */
        padding: 80px 0 30px;
        margin-top: 50px;
        position: relative; /* Agar z-index berfungsi */
        z-index: 1000;      /* Memastikan footer di lapisan depan */
    }
    .footer-logo {
        font-size: 1.8rem;
        font-weight: 900;
        letter-spacing: -1px;
    }
    .footer-link {
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        transition: 0.3s;
        display: block;
        margin-bottom: 12px;
        font-weight: 500;
        position: relative;
        z-index: 1001;
    }
    .footer-link:hover {
        color: #e91e63;
        padding-left: 8px;
    }
    .social-icon {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.05);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: white;
        margin-right: 10px;
        transition: 0.3s;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .social-icon:hover {
        background: #00f2ff;
        color: black;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 242, 255, 0.3);
    }
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding-top: 30px;
        margin-top: 50px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.9rem;
    }
</style>

<footer class="main-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-logo mb-4 text-white">
                    TRINITY <span style="color: #e91e63;">SPORT</span>
                </div>
                <p class="text-white-50 mb-4 pe-lg-5">
                    Penyedia layanan booking lapangan olahraga terbaik dan tercepat. Kami mengutamakan kualitas lapangan dan kemudahan akses bagi seluruh pecinta olahraga.
                </p>
                <div class="d-flex">
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="https://wa.me/6285694261056?text=Halo%20Admin%20Trinity%20Sport,%20saya%20ingin%20tanya%20seputar%20booking%20lapangan." target="_blank" class="social-icon">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="fw-bold mb-4 text-white">Navigasi</h5>
                <a href="<?= $base_url ?>index.php" class="footer-link">Home</a>
                <a href="<?= $base_url ?>index.php#lapangan" class="footer-link">Arena</a>
                
                <?php if (isset($_SESSION['id_user'])): ?>
                    <a href="<?= $base_url ?>user/dashboard.php" class="footer-link">Dashboard</a>
                    <a href="<?= $base_url ?>user/riwayat.php" class="footer-link">Riwayat</a>
                <?php else: ?>
                    <a href="<?= $base_url ?>login.php" class="footer-link">Login</a>
                    <a href="<?= $base_url ?>register.php" class="footer-link">Daftar</a>
                <?php endif; ?>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5 class="fw-bold mb-4 text-white">Jam Buka</h5>
                <p class="text-white-50 mb-2">Senin - Minggu:</p>
                <p class="text-info fw-bold mb-4">08:00 AM - 23:00 PM</p>
                <h5 class="fw-bold mb-2 text-white">Lokasi</h5>
                <p class="text-white-50 small">Jl. Sport Center No. 12, Kota Olahraga, Indonesia</p>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5 class="fw-bold mb-4 text-white">Kontak Kami</h5>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-envelope-at text-info me-3 mt-1"></i>
                    <div>
                        <p class="mb-0 text-white small">Email Kami</p>
                        <p class="text-white-50 small">support@trinitysport.id</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <i class="bi bi-telephone-inbound text-info me-3 mt-1"></i>
                    <div>
                        <p class="mb-0 text-white small">Hubungi Kami</p>
                        <a href="https://wa.me/6285694261056" target="_blank" class="text-white-50 small text-decoration-none">
                            +62 856-9426-1056
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom text-center">
            <p class="mb-0">&copy; <?= date('Y'); ?> Trinity Sport Center. All Rights Reserved. Built with <i class="bi bi-heart-fill text-danger mx-1"></i> by Trinity Dev.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>