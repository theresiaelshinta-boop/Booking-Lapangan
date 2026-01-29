<?php
session_start();
require 'config/database.php'; 

if (isset($_SESSION['id_user'])) {
    $redirect = ($_SESSION['role'] == 'admin') ? "admin/dashboard.php" : "user/dashboard.php";
    header("Location: $redirect");
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        if (password_verify($password, $row['password'])) {
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['nama']    = $row['nama']; 
            $_SESSION['role']    = $row['role'];
            if ($row['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Trinity Sport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Orbitron:wght@800&display=swap');
        
        body { 
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.75)), 
                        url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=1920&auto=format&fit=crop'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* STYLE TULISAN TRINITY SPORT SESUAI REQUEST */
        .brand-trinity {
            font-family: 'Orbitron', sans-serif;
            font-weight: 800;
            font-style: italic;
            font-size: 2rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            background: linear-gradient(to bottom, #ffffff, #bbbbbb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
            line-height: 1;
        }

        .brand-sport {
            font-family: 'Orbitron', sans-serif;
            font-weight: 800;
            font-style: italic;
            font-size: 1.8rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            background: linear-gradient(to bottom, #e91e63, #880e4f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
            margin-top: 5px;
        }

        .card-login { 
            background: rgba(15, 17, 19, 0.85); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(233, 30, 99, 0.4); 
            border-radius: 25px; 
            padding: 35px; 
            width: 100%; 
            max-width: 360px; 
            margin: auto; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.6);
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes shimmer {
            0% { left: -60%; }
            100% { left: 150%; }
        }

        .form-control { 
            background: rgba(255, 255, 255, 0.08) !important; 
            border: 1px solid rgba(255, 255, 255, 0.15) !important; 
            color: #ffffff !important; 
            border-radius: 12px; 
            padding: 12px 15px;
            transition: 0.3s;
        }
        
        .form-control:focus { 
            border-color: #e91e63 !important; 
            transform: translateX(5px);
            box-shadow: 0 0 15px rgba(233, 30, 99, 0.2); 
        }

        .btn-pink { 
            background: #e91e63; 
            color: white; 
            border-radius: 12px; 
            font-weight: 700; 
            padding: 12px; 
            border: none; 
            width: 100%; 
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-pink::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -60%;
            width: 20%;
            height: 200%;
            background: rgba(255, 255, 255, 0.25);
            transform: rotate(30deg);
            animation: shimmer 3s infinite;
        }

        .btn-pink:hover { 
            background: #ff2e7e; 
            transform: translateY(-3px); 
            box-shadow: 0 10px 20px rgba(233, 30, 99, 0.4); 
        }

        .btn-outline-regis {
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 10px;
            text-decoration: none;
            display: block;
            transition: 0.3s;
        }

        .btn-outline-regis:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
            transform: scale(1.02);
        }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.7rem;
        }

        .forgot-link:hover { color: #e91e63; }

        .error-box {
            background: rgba(255, 61, 113, 0.2);
            color: #ff9fb8;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.85rem;
        }

        #togglePassword { cursor: pointer; color: rgba(255, 255, 255, 0.5); }
    </style>
</head>
<body>

<div class="card-login text-center" data-aos="zoom-in">
    <div class="mb-4">
        <span class="brand-trinity">TRINITY</span>
        <span class="brand-sport">SPORT</span>
        <p class="text-white-50 small mt-2" style="font-size: 0.7rem; letter-spacing: 1px;">MANAGEMENT SYSTEM</p>
    </div>

    <?php if($error): ?>
        <div class="error-box">
            <i class="bi bi-exclamation-circle-fill me-1"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="text-start">
        <div class="mb-3">
            <label class="small mb-1 text-white ms-1">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="admin@email.com" required autocomplete="off">
        </div>
        
        <div class="mb-4">
            <label class="small mb-1 text-white ms-1">Password</label>
            <div class="position-relative">
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required style="padding-right: 45px;">
                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword"></i>
            </div>
        </div>

        <button type="submit" name="login" class="btn btn-pink">Sign In</button>
        
        <div class="text-center mt-3">
            <p class="small mb-2" style="color: rgba(255,255,255,0.4); font-size: 0.75rem;">Belum punya akun?</p>
            <a href="register.php" class="btn btn-outline-regis">
                BUAT AKUN BARU
            </a>
        </div>

        <a href="https://wa.me/6285694261056?text=Halo%20Admin,%20saya%20lupa%20password." target="_blank" class="forgot-link">
            Lupa Password? Hubungi Admin
        </a>
    </form>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
</script>

</body>
</html>