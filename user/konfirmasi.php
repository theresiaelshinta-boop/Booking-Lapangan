<?php
session_start();
require '../config/database.php'; 

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$id_lapangan = isset($_GET['id']) ? $_GET['id'] : 0;
$jam_kode = isset($_GET['jam']) ? $_GET['jam'] : '';
$tgl_pilih = isset($_GET['tgl']) ? $_GET['tgl'] : '';

$query_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan = '$id_lapangan'");
$lap = mysqli_fetch_assoc($query_lap);

if (!$lap || empty($jam_kode) || empty($tgl_pilih)) {
    echo "<script>alert('Data tidak valid!'); window.location='index.php';</script>";
    exit();
}

include '../includes/header.php';
?>

<style>
    /* Bingkai Luar: Rounded Halus & Warna Pink Sidebar */
    .card-konfirmasi {
        background: #121416;
        border: 2px solid #e91e63; /* Warna Pink Sidebar */
        border-radius: 40px; /* Bikin sudut tumpul cantik */
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.2);
    }

    /* Gambar: Tidak persegi kaku, pakai rounded sedang */
    .img-confirm-full {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 25px; /* Rounded halus */
        border: 1px solid #333;
    }

    .info-item {
        background: #1c1f23;
        border-radius: 20px;
        padding: 15px 20px;
        margin-bottom: 12px;
        border: 1px solid #2d3238;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .label-kecil {
        color: #e91e63; /* Pink */
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
        margin-bottom: 2px;
    }

    /* Tombol Konfirmasi Warna Pink */
    .btn-pink {
        background: #e91e63;
        color: white;
        border: none;
        border-radius: 18px;
        font-weight: 700;
        padding: 15px;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-pink:hover {
        background: #c2185b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.4);
        color: white;
    }

    .text-putih { color: #ffffff; font-weight: 700; margin: 0; }
</style>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <div class="card-konfirmasi p-4">
                <div class="text-center mb-4">
                    <h4 class="text-white fw-bold mb-3">KONFIRMASI PESANAN</h4>
                    <img src="../assets/images/<?= $lap['foto']; ?>" class="img-confirm-full shadow">
                    <h5 class="text-white fw-bold mt-3 text-uppercase"><?= $lap['nama_lapangan']; ?></h5>
                </div>

                <div class="info-item">
                    <div>
                        <span class="label-kecil">Tanggal Main</span>
                        <p class="text-putih"><?= date('d F Y', strtotime($tgl_pilih)); ?></p>
                    </div>
                    <i class="bi bi-calendar-check text-white-50 fs-4"></i>
                </div>

                <div class="info-item">
                    <div>
                        <span class="label-kecil">Waktu / Jam</span>
                        <p class="text-putih"><?= $jam_kode ?>:00 WIB</p>
                    </div>
                    <i class="bi bi-clock text-white-50 fs-4"></i>
                </div>

                <div class="info-item border-pink" style="border-color: rgba(233, 30, 99, 0.5);">
                    <div>
                        <span class="label-kecil">Total Bayar</span>
                        <h4 class="text-white fw-bolder m-0">Rp <?= number_format($lap['harga_per_jam'], 0, ',', '.'); ?></h4>
                    </div>
                    <i class="bi bi-wallet2 text-white-50 fs-4"></i>
                </div>

                <form action="proses_booking.php" method="POST" class="mt-4">
                    <input type="hidden" name="id_lapangan" value="<?= $id_lapangan ?>">
                    <input type="hidden" name="tanggal" value="<?= $tgl_pilih ?>">
                    <input type="hidden" name="jam_mulai" value="<?= $jam_kode ?>">
                    <input type="hidden" name="total_harga" value="<?= $lap['harga_per_jam'] ?>">

                    <div class="d-grid gap-2">
                        <button type="submit" name="submit_booking" class="btn btn-pink shadow-sm">
                            KONFIRMASI & BAYAR
                        </button>
                        <a href="booking.php?id=<?= $id_lapangan ?>&tgl=<?= $tgl_pilih ?>" class="btn btn-link text-secondary text-decoration-none small">
                            Batal & Pilih Jam Lain
                        </a>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4 mb-5">
                <small class="text-white-50">Selesaikan pembayaran setelah klik konfirmasi.</small>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>