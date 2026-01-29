<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOGIKA DINAMIS: Deteksi folder agar tidak 404
$base_url = (strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? "../" : "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trinity Sport Center</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            /* URL gambar disesuaikan agar tetap muncul dari folder manapun */
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.85)), 
                        url('<?= $base_url ?>assets/images/lapangan-bg.jpg'); 
            background-size: cover; background-position: center; background-attachment: fixed;
            color: white; font-family: 'Montserrat', sans-serif;
        }

        .navbar-trinity {
            background: rgba(0,0,0,0.4); padding: 15px 0;
            transition: all 0.5s ease; position: fixed;
            width: 100%; top: 0; z-index: 9999;
        }
        
        .navbar-scrolled {
            background: rgba(0, 0, 0, 0.9) !important;
            backdrop-filter: blur(10px); border-bottom: 1px solid #e91e63;
        }

        .nav-link { 
            color: white !important; font-weight: 700; margin-left: 20px; 
            text-transform: uppercase; font-size: 0.85rem; transition: 0.3s;
        }
        
        .nav-link:hover { color: #e91e63 !important; }

        .btn-pink-nav { 
            background: #e91e63; color: white !important; padding: 8px 25px !important; 
            border-radius: 50px; text-decoration: none; font-weight: 700; 
            margin-left: 20px; transition: 0.3s; border: none;
        }

        .dropdown-menu {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid #e91e63 !important;
            backdrop-filter: blur(15px);
            margin-top: 10px !important;
            z-index: 10001;
            display: none;
        }
        
        .dropdown-menu.show {
            display: block !important;
        }

        .dropdown-item { 
            color: white !important; font-size: 0.85rem; padding: 10px 20px; 
            transition: 0.3s; font-weight: 600;
        }
        .dropdown-item:hover { background: #e91e63 !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-trinity" id="mainNav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= $base_url ?>index.php">
            <img src="<?= $base_url ?>assets/images/logo-trinity.png" alt="Logo" style="width: 40px; height: 40px; margin-right: 12px; object-fit: contain;">
    
             <span class="fw-bold text-white">TRINITY <span style="color: #e91e63;">SPORT</span></span>
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list text-white fs-1"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex align-items-center navbar-nav">
                <a class="nav-link" href="<?= $base_url ?>index.php">Home</a>
                <a class="nav-link" href="<?= $base_url ?>index.php#lapangan">Booking</a>

                <?php if (isset($_SESSION['id_user'])): ?>
                    <a class="nav-link text-info" href="<?= $base_url ?><?= $_SESSION['role'] ?>/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="javascript:void(0)" id="userDropdownTrigger" role="button">
                            <i class="bi bi-person-circle"></i> <?= strtoupper($_SESSION['nama']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" id="userDropdownContent">
                            <li><a class="dropdown-item" href="<?= (strpos($_SERVER['REQUEST_URI'], '/user/') !== false) ? 'riwayat.php' : 'user/riwayat.php' ?>"><i class="bi bi-clock-history me-2"></i> RIWAYAT</a></li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li><a class="dropdown-item text-danger fw-bold" href="<?= $base_url ?>logout.php"><i class="bi bi-power me-2"></i> LOGOUT</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="<?= $base_url ?>login.php">Login</a>
                    <a class="btn-pink-nav" href="<?= $base_url ?>register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.addEventListener('scroll', function() {
        const nav = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            nav.classList.add('navbar-scrolled');
        } else {
            nav.classList.remove('navbar-scrolled');
        }
    });

    const trigger = document.getElementById('userDropdownTrigger');
    const menu = document.getElementById('userDropdownContent');

    if (trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('show');
        });
    }

    document.addEventListener('click', function() {
        if (menu && menu.classList.contains('show')) {
            menu.classList.remove('show');
        }
    });
</script>
</body>
</html>