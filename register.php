<?php
session_start();
require 'config/database.php'; 

$success = '';
$error = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role     = 'user'; 

    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah terdaftar, Seng!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password_hash', '$role')");

        if ($query) {
            $success = "Akun berhasil dibuat! Silakan login.";
        } else {
            $error = "Gagal daftar, coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Trinity Sport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Orbitron:wght@800&display=swap');
        
        body { 
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), 
                        url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=1920&auto=format&fit=crop'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 20px;
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

        .card-register { 
            background: rgba(15, 17, 19, 0.85); 
            backdrop-filter: blur(15px);
            border: 1px solid rgba(233, 30, 99, 0.4); 
            border-radius: 25px; 
            padding: 30px; 
            width: 100%; 
            max-width: 400px; 
            margin: auto; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.7);
            animation: fadeInUp 0.8s ease forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmer {
            0% { left: -60%; }
            100% { left: 150%; }
        }

        .form-control { 
            background: rgba(255, 255, 255, 0.05) !important; 
            border: 1px solid rgba(255, 255, 255, 0.1) !important; 
            color: #ffffff !important; 
            border-radius: 12px; 
            padding: 11px 15px;
            transition: 0.3s;
        }
        
        .form-control:focus { 
            border-color: #e91e63 !important; 
            transform: scale(1.02);
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
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }

        .btn-pink::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -60%;
            width: 20%;
            height: 200%;
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(30deg);
            animation: shimmer 3s infinite;
        }

        .btn-pink:hover { 
            background: #ff2e7e; 
            transform: translateY(-3px); 
            box-shadow: 0 10px 20px rgba(233, 30, 99, 0.4); 
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.8rem;
        }

        .login-link b { color: #e91e63; }

        .msg-box { border-radius: 10px; padding: 10px; margin-bottom: 20px; text-align: center; font-size: 0.85rem; font-weight: 600; }
        .error-style { background: rgba(255, 61, 113, 0.2); color: #ff9fb8; border: 1px solid rgba(255, 61, 113, 0.3); }
        .success-style { background: rgba(0, 200, 83, 0.2); color: #69f0ae; border: 1px solid rgba(0, 200, 83, 0.3); }

        #togglePassword { cursor: pointer; color: rgba(255, 255, 255, 0.4); }
    </style>
</head>
<body>

<div class="card-register text-center" data-aos="fade-up">
    <div class="mb-4">
        <span class="brand-trinity">TRINITY</span>
        <span class="brand-sport">SPORT</span>
        <p class="text-white-50 small mt-2" style="font-size: 0.65rem; letter-spacing: 2px;">CREATE YOUR ACCOUNT</p>
    </div>

    <?php if($error): ?>
        <div class="msg-box error-style"><?= $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="msg-box success-style"><?= $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="text-start">
        <div class="mb-3">
            <label class="small mb-1 text-white-50 ms-1">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Input Nama" required>
        </div>

        <div class="mb-3">
            <label class="small mb-1 text-white-50 ms-1">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Email@kamu.com" required>
        </div>
        
        <div class="mb-4">
            <label class="small mb-1 text-white-50 ms-1">Password</label>
            <div class="position-relative">
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required style="padding-right: 45px;">
                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword"></i>
            </div>
        </div>

        <button type="submit" name="register" class="btn btn-pink">Daftar Sekarang</button>
        
        <a href="login.php" class="login-link">
            Sudah punya akun? <b>Login di Sini</b>
        </a>
    </form>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
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