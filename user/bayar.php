<?php
session_start();
require '../config/database.php'; 

// Pastikan user login & ID booking ada
if (!isset($_SESSION['id_user']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_booking = mysqli_real_escape_string($conn, $_GET['id']);
$id_user = $_SESSION['id_user'];

// Ambil data booking & nama lapangan
$query = mysqli_query($conn, "SELECT b.*, l.nama_lapangan 
                              FROM booking b 
                              JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                              WHERE b.id_booking = '$id_booking' AND b.id_user = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    header("Location: riwayat.php");
    exit();
}

include '../includes/header.php';
?>

<style>
    .card-bayar {
        background: #121416;
        border: 2px solid #e91e63;
        border-radius: 40px;
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.2);
    }
    .rekening-box {
        background: #1c1f23;
        border: 2px dashed rgba(233, 30, 99, 0.4);
        border-radius: 25px;
        padding: 20px;
    }
    .btn-upload-notif {
        background: #e91e63;
        color: #ffffff !important;
        border: none;
        border-radius: 20px;
        font-weight: 800;
        padding: 16px;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-upload-notif:hover {
        background: #ff2e7e;
        transform: translateY(-3px);
    }
    .label-pink { color: #e91e63; font-weight: 800; font-size: 0.75rem; }
    .form-control-dark {
        background: #121416;
        border: 1px solid #333;
        color: #fff;
        border-radius: 12px;
        padding: 10px;
    }
</style>

<div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-bayar p-4">
                <div class="text-center mb-4">
                    <span class="badge border border-pink text-pink p-2 px-3 rounded-pill" style="color: #e91e63;">
                        ID: <?= $data['kode_booking']; ?>
                    </span>
                    <h5 class="text-white-50 mt-3 mb-1 small">Total Transfer:</h5>
                    <h1 class="text-white fw-bolder">Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></h1>
                </div>

                <div class="rekening-box text-center mb-4">
                    <p class="label-pink mb-2">TRANSFER KE:</p>
                    <h4 class="text-white fw-bold mb-0">BANK MANDIRI</h4>
                    <h2 class="text-white fw-bolder my-2" style="letter-spacing: 2px;">123-000-456-789</h2>
                    <p class="text-white-50 small">A/N <span class="text-white">PT. TRINITY SPORT</span></p>
                    
                    <hr class="border-secondary opacity-25 my-3">

                    <p class="label-pink mb-2">KIRIM BUKTI PEMBAYARAN:</p>
                    <form action="proses_bukti.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_booking" value="<?= $id_booking ?>">
                        <input type="hidden" name="kode_booking" value="<?= $data['kode_booking'] ?>">
                        
                        <div class="mb-3">
                            <input type="file" name="bukti_transfer" class="form-control form-control-dark" required accept="image/*">
                        </div>
                        
                        <button type="submit" name="submit_bukti" class="btn-upload-notif w-100">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i>UNGGAH & NOTIF WA
                        </button>
                    </form>
                </div>

                <div class="text-center mt-2">
                    <a href="riwayat.php" class="btn btn-link text-white-50 text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i> Lihat Status Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>