<?php
session_start();
require '../config/database.php';

// Jika admin sudah login, langsung lempar ke dashboard
// Perbaikan: Cek id_admin DAN role agar sinkron
if (isset($_SESSION['id_admin']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM admin WHERE email = '$email'");
    $admin = mysqli_fetch_assoc($query);

    if ($admin) {
        if (password_verify($password, $admin['password'])) {
            // SET SESSION (KUNCI UTAMA)
            $_SESSION['id_admin']   = $admin['id_admin'];
            $_SESSION['nama_admin'] = $admin['nama_admin'];
            $_SESSION['nama']       = $admin['nama_admin']; // Untuk sapaan di dashboard
            $_SESSION['role']       = $admin['role'];       // WAJIB ADA untuk proteksi role
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password admin tidak cocok!";
        }
    } else {
        $error = "Email admin tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Trinity Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        
        body { 
            background-color: #050505; 
            color: white; 
            font-family: 'Montserrat', sans-serif; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0;
            overflow: hidden;
        }

        /* Desain Box Login */
        .login-card {
            background: #0a0a0a;
            border: 1px solid #222;
            border-radius: 30px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0, 242, 255, 0.05);
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px; right: -2px; bottom: -2px;
            background: linear-gradient(45deg, #00f2ff, transparent, #00f2ff);
            z-index: -1;
            border-radius: 32px;
            opacity: 0.1;
        }

        .form-control {
            background: #111;
            border: 1px solid #333;
            color: white;
            padding: 12px 20px;
            border-radius: 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: #151515;
            color: white;
            border-color: #00f2ff;
            box-shadow: 0 0 15px rgba(0, 242, 255, 0.2);
        }

        .btn-login {
            background: #00f2ff;
            color: #000;
            font-weight: 700;
            border: none;
            padding: 12px;
            border-radius: 15px;
            width: 100%;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            background: #00d4e0;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 242, 255, 0.3);
        }

        .icon-lock {
            width: 70px;
            height: 70px;
            background: rgba(0, 242, 255, 0.1);
            color: #00f2ff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
        }

        .alert-custom {
            background: rgba(255, 77, 77, 0.1);
            color: #ff4d4d;
            border: 1px solid rgba(255, 77, 77, 0.2);
            border-radius: 12px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center">
        <div class="icon-lock shadow">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h4 class="fw-bold mb-1">TRINITY <span class="text-info">ADMIN</span></h4>
        <p class="text-white-50 small mb-4">Panel Kendali Manajemen Lapangan</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-custom mb-3 text-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="small text-white-50 mb-2 ms-2">Email Akun Admin</label>
            <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" required>
        </div>
        <div class="mb-4">
            <label class="small text-white-50 mb-2 ms-2">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-login mb-3">Login Sekarang</button>
        
    </form>
</div>

</body>
</html>