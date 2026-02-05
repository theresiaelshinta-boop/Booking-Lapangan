<?php
session_start();
require '../config/database.php';

/**
 * 1. PROTEKSI AKSES
 * Memastikan hanya user yang sudah login dan memiliki role 'user' yang bisa akses
 */
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pelanggan';

/**
 * 2. LOGIKA UPLOAD & UPDATE DATABASE
 */
if (isset($_POST['submit_bukti'])) {
    $id_booking = mysqli_real_escape_string($conn, $_POST['id_booking']);
    $kode_booking = mysqli_real_escape_string($conn, $_POST['kode_booking']);
    
    // Konfigurasi Folder Tujuan (Sesuai Request: assets/images/bukti/)
    $target_dir = "../assets/images/bukti/"; 
    
    // Cek apakah folder ada, jika tidak ada maka buat otomatis
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_info = $_FILES["bukti_transfer"];
    $file_ext = strtolower(pathinfo($file_info["name"], PATHINFO_EXTENSION));
    
    // Nama file unik: BUKTI_BK01_1712345678.jpg
    $nama_file_baru = "BUKTI_" . $kode_booking . "_" . time() . "." . $file_ext;
    $target_file = $target_dir . $nama_file_baru;

    // Validasi apakah benar-benar gambar
    $check = getimagesize($file_info["tmp_name"]);
    if($check === false) {
        echo "<script>alert('File yang diunggah bukan gambar!'); window.history.back();</script>";
        exit();
    }

    // Proses pindahkan file dari folder temporary ke folder assets
    if (move_uploaded_file($file_info["tmp_name"], $target_file)) {
        
        /**
         * UPDATE DATABASE:
         * - bukti_bayar diisi nama file baru
         * - status diubah menjadi 'menunggu_konfirmasi' agar admin tahu
         */
        $sql_update = "UPDATE booking SET 
                       bukti_bayar = '$nama_file_baru', 
                       status = 'menunggu_konfirmasi' 
                       WHERE id_booking = '$id_booking' AND id_user = '$id_user'";
        
        if(mysqli_query($conn, $sql_update)) {
            // Ambil data untuk kepastian variabel tampilan
            $query_cek = mysqli_query($conn, "SELECT kode_booking FROM booking WHERE id_booking = '$id_booking'");
            $data = mysqli_fetch_assoc($query_cek);
        } else {
            echo "<script>alert('Gagal update data di database!'); window.history.back();</script>";
            exit();
        }

    } else {
        echo "<script>alert('Gagal upload! Pastikan folder assets/images/bukti/ memiliki izin akses.'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: riwayat.php");
    exit();
}

/**
 * 3. PERSIAPAN DATA NOTIFIKASI WHATSAPP
 */
$no_admin = "6285694261056"; 
$pesan_wa = "🔔 *KONFIRMASI PEMBAYARAN* 🔔%0A%0A" .
            "Halo Admin Trinity,%0A" .
            "Saya *$nama_user* baru saja mengunggah bukti bayar.%0A" .
            "📌 *Kode Booking:* " . ($data['kode_booking'] ?? $kode_booking) . "%0A" .
            "Mohon segera divalidasi. Terima kasih!";
$wa_link = "https://wa.me/$no_admin?text=$pesan_wa";

include '../includes/header.php'; 
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');
    body { background: #050505; color: white; font-family: 'Montserrat', sans-serif; }
    
    .card-sukses {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 2px solid #e91e63;
        border-radius: 40px;
        padding: 50px;
        text-align: center;
        max-width: 500px;
        margin: 120px auto;
        box-shadow: 0 10px 40px rgba(233, 30, 99, 0.2);
        animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .icon-box {
        font-size: 5rem;
        color: #e91e63;
        margin-bottom: 20px;
        filter: drop-shadow(0 0 10px rgba(233, 30, 99, 0.5));
    }

    .btn-custom {
        display: block; width: 100%; padding: 15px; border-radius: 15px;
        font-weight: 800; text-transform: uppercase; text-decoration: none;
        margin-bottom: 12px; transition: 0.3s; text-align: center;
    }

    .btn-wa { background: #198754; color: white; border: none; }
    .btn-wa:hover { background: #157347; transform: translateY(-3px); filter: brightness(1.2); }

    .btn-back { background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); }
    .btn-back:hover { background: #e91e63; color: white; border-color: #e91e63; }
</style>

<div class="container">
    <div class="card-sukses">
        <div class="icon-box">
            <i class="bi bi-patch-check-fill"></i>
        </div>
        <h2 class="fw-bold mb-3">BUKTI TERKIRIM!</h2>
        <p class="text-white-50 mb-5">
            Pembayaran untuk kode <strong><?= htmlspecialchars($data['kode_booking']) ?></strong> berhasil diunggah. Klik tombol di bawah untuk konfirmasi cepat ke Admin.
        </p>
        
        <a href="<?= $wa_link ?>" target="_blank" class="btn-custom btn-wa">
            <i class="bi bi-whatsapp me-2"></i>NOTIFIKASI ADMIN
        </a>
        
        <a href="riwayat.php" class="btn-custom btn-back">
            <i class="bi bi-arrow-left me-2"></i>KEMBALI KE RIWAYAT
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>