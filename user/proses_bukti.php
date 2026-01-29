<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nama_user = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pelanggan';

// Ambil ID dari POST atau GET
$id_booking = isset($_POST['id_booking']) ? $_POST['id_booking'] : (isset($_GET['id']) ? $_GET['id'] : 0);

// --- 1. LOGIC UPLOAD (Sesuai Struktur File Baru Kamu) ---
if (isset($_POST['submit_bukti'])) {
    $kode_booking = mysqli_real_escape_string($conn, $_POST['kode_booking']);
    
    // Folder sesuai struktur: assets/images/ (Tanpa subfolder bukti jika tidak ada di list)
    $target_dir = "../assets/images/"; 
    
    $file_ext       = pathinfo($_FILES["bukti_transfer"]["name"], PATHINFO_EXTENSION);
    $nama_file_baru = "BUKTI_" . $kode_booking . "_" . time() . "." . $file_ext;
    $target_file    = $target_dir . $nama_file_baru;

    if (move_uploaded_file($_FILES["bukti_transfer"]["tmp_name"], $target_file)) {
        // PERHATIKAN: Saya pakai 'bukti_bayar' dan status 'menunggu' 
        // agar terbaca oleh admin/dashboard.php kamu
        $sql_update = "UPDATE booking SET 
                       bukti_bayar = '$nama_file_baru', 
                       status = 'menunggu' 
                       WHERE id_booking = '$id_booking'";
        
        mysqli_query($conn, $sql_update);
    }
}

// --- 2. AMBIL DATA UNTUK TAMPILAN ---
$query_cek = mysqli_query($conn, "SELECT * FROM booking WHERE id_booking = '$id_booking'");
$data = mysqli_fetch_assoc($query_cek);

// Data Link WA
$no_admin = "6285694261056"; 
$pesan_wa = "🔔 *KONFIRMASI PEMBAYARAN* 🔔%0A%0A" .
            "Halo Admin, user *$nama_user* baru saja mengunggah bukti bayar.%0A" .
            "📌 *Kode:* " . ($data['kode_booking'] ?? 'N/A') . "%0A" .
            "Silakan cek Dashboard Admin!";
$wa_link = "https://wa.me/$no_admin?text=$pesan_wa";

include '../includes/header.php'; 
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');
    body { background: #050505; color: white; font-family: 'Montserrat', sans-serif; }
    .card-sukses {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border: 2px solid #e91e63;
        border-radius: 40px;
        padding: 50px;
        text-align: center;
        max-width: 500px;
        margin: 100px auto;
    }
    .btn-custom {
        display: block; width: 100%; padding: 15px; border-radius: 12px;
        font-weight: 800; text-transform: uppercase; text-decoration: none;
        margin-bottom: 12px; transition: 0.3s;
    }
    .btn-wa { background: #198754; color: white; }
    .btn-back { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); }
    .btn-back:hover { background: #e91e63; color: white; }
</style>

<div class="container">
    <div class="card-sukses shadow-lg">
        <i class="bi bi-shield-check" style="font-size: 5rem; color: #e91e63;"></i>
        <h2 class="fw-bold mt-3 mb-3">BUKTI TERSIMPAN</h2>
        <p class="text-white-50 mb-5">Pesanan sedang diproses. Silakan klik tombol di bawah untuk notifikasi cepat ke Admin.</p>
        
        <a href="<?= $wa_link ?>" target="_blank" class="btn-custom btn-wa">Hubungi Admin</a>
        <a href="riwayat.php" class="btn-custom btn-back">Kembali ke Riwayat</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>