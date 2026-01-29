<?php
session_start();
// SET ZONA WAKTU INDONESIA
date_default_timezone_set('Asia/Jakarta'); 

require '../config/database.php';

$id_lapangan = isset($_GET['id']) ? $_GET['id'] : 0;
$tgl_pilih = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
$hari_ini = date('Y-m-d');
$waktu_sekarang = date('H:i'); // Mengambil jam:menit sekarang (Contoh: 20:27)

$query_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan = '$id_lapangan'");
$lap = mysqli_fetch_assoc($query_lap);

if (!$lap) { header("Location: ../index.php"); exit(); }

$is_logged_in = isset($_SESSION['id_user']);
include '../includes/header.php';
?>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(48%) sepia(79%) saturate(2476%) hue-rotate(86deg) brightness(118%) contrast(119%);
        cursor: pointer;
        padding: 5px;
    }
    .slot-jam { border: 2px solid #333; background: #111; border-radius: 12px; transition: all 0.2s ease; }
    .slot-available { border-color: #28a745 !important; cursor: pointer; }
    .slot-available:hover { background: #28a745 !important; transform: scale(1.05); }
    .slot-full { border-color: #dc3545 !important; background: #1a0505; }
    .slot-expired { border-color: #444 !important; background: #080808; opacity: 0.5; }
    .text-jam { font-size: 1.2rem; color: #fff; font-weight: 800; margin: 0; }
    .status-tag { font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
    .img-booking { width: 100%; height: 350px; object-fit: cover; border-radius: 20px; border: 2px solid #333; }
</style>

<div class="container mt-5 pt-5">
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-7">
            <h1 class="fw-bold text-white text-uppercase m-0"><?= $lap['nama_lapangan']; ?></h1>
            <h4 class="text-success fw-bold m-0">Rp <?= number_format($lap['harga_per_jam'], 0, ',', '.'); ?> <span class="text-white-50 fs-6 fw-normal">/ Jam</span></h4>
        </div>
        <div class="col-md-5 text-md-end">
            <div class="d-inline-block text-start" style="max-width: 250px;">
                <label class="small text-white-50 mb-1 fw-bold">TANGGAL MAIN:</label>
                <form action="" method="GET">
                    <input type="hidden" name="id" value="<?= $id_lapangan ?>">
                    <input type="date" name="tgl" class="form-control bg-dark text-success border-success fw-bold py-2 shadow-none" 
                           value="<?= $tgl_pilih ?>" min="<?= $hari_ini ?>" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>

    <hr class="border-secondary opacity-25 mb-5">

    <div class="row g-5">
        <div class="col-md-5">
            <img src="/booking-lapangan/assets/images/<?= $lap['foto']; ?>" class="img-booking shadow-lg">
        </div>

        <div class="col-md-7">
            <h5 class="fw-bold mb-4 text-white">JADWAL: <span class="text-success"><?= date('d M Y', strtotime($tgl_pilih)); ?></span></h5>
            
            <div class="row g-3">
               <?php 
                    $jam_list = [
                        '08:00' => '08:00:00', '09:00' => '09:00:00', '10:00' => '10:00:00', 
                        '11:00' => '11:00:00', '12:00' => '12:00:00', '13:00' => '13:00:00', 
                        '14:00' => '14:00:00', '15:00' => '15:00:00', '16:00' => '16:00:00', 
                        '17:00' => '17:00:00', '18:00' => '18:00:00', '19:00' => '19:00:00',
                        '20:00' => '20:00:00', '21:00' => '21:00:00', '22:00' => '22:00:00'
                    ];

                foreach ($jam_list as $label => $kode): 
                    $sql_cek = "SELECT * FROM booking WHERE id_lapangan = '$id_lapangan' AND tanggal = '$tgl_pilih' AND jam_mulai = '$kode' AND status != 'batal'";
                    $res_cek = mysqli_query($conn, $sql_cek);
                    $is_booked = mysqli_num_rows($res_cek) > 0;
                    
                    // PERBANDINGAN WAKTU LEBIH PRESISI (Jam & Menit)
                    // strtotime mengubah label (15:00) dan waktu sekarang (20:27) jadi detik untuk dibandingin
                    $is_past = ($tgl_pilih == $hari_ini && strtotime($label) <= strtotime($waktu_sekarang));
                ?>
                    <div class="col-4 col-sm-4 col-md-3">
                        <?php if ($is_booked): ?>
                            <div class="slot-jam slot-full py-3 text-center">
                                <p class="text-jam"><?= $label ?></p>
                                <span class="text-danger status-tag">FULL</span>
                            </div>
                        <?php elseif ($is_past): ?>
                            <div class="slot-jam slot-expired py-3 text-center">
                                <p class="text-jam text-white-50"><?= $label ?></p>
                                <span class="text-secondary status-tag">LEWAT</span>
                            </div>
                        <?php else: ?>
                            <div onclick="<?= $is_logged_in ? "location.href='konfirmasi.php?id=$id_lapangan&jam=$kode&tgl=$tgl_pilih'" : "alertLogin()" ?>" 
                                 class="slot-jam slot-available py-3 text-center">
                                <p class="text-jam"><?= $label ?></p>
                                <span class="text-success status-tag">PESAN</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function alertLogin() {
    Swal.fire({
        title: 'Booking Lapangan',
        text: "Login dulu ya buat amanin jadwalmu!",
        icon: 'info',
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Login Sekarang',
        showCancelButton: true,
        cancelButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) { window.location.href = '../login.php'; }
    })
}
</script>

<?php include '../includes/footer.php'; ?>