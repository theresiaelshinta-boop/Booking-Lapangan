<?php 
session_start();
require 'config/database.php';
include 'includes/header.php';
date_default_timezone_set('Asia/Jakarta');

$id_lapangan = $_GET['id'];
$tgl_cek = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$jam_sekarang = date('H:i:s');
$tgl_sekarang = date('Y-m-d');

$query_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan = '$id_lapangan'");
$lap = mysqli_fetch_assoc($query_lap);

$booked_query = mysqli_query($conn, "SELECT jam_mulai FROM booking WHERE id_lapangan = '$id_lapangan' AND tanggal = '$tgl_cek' AND status != 'dibatalkan'");
$booked_slots = [];
while($row = mysqli_fetch_assoc($booked_query)) { $booked_slots[] = $row['jam_mulai']; }
?>

<style>
    .img-jadwal-container { width: 100%; height: 400px; overflow: hidden; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
    .img-jadwal-container img { width: 100%; height: 100%; object-fit: cover; }
    .slot-box { padding: 15px; border-radius: 12px; font-weight: 700; text-align: center; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }
    .bg-exp { background: #222 !important; color: #555 !important; }
    .bg-booked { background: #dc3545 !important; color: white !important; }
    .bg-available { background: #198754 !important; color: white !important; cursor: pointer; }
    .bg-available:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(25, 135, 84, 0.4); }
    .input-tanggal-cyan { background: rgba(0, 242, 255, 0.05) !important; color: #00f2ff !important; border: 2px solid #00f2ff !important; font-weight: 900; border-radius: 10px; }
</style>

<div class="container" style="margin-top: 120px; padding-bottom: 100px;">
    <div class="row g-5">
        <div class="col-md-5">
            <div class="img-jadwal-container shadow-lg mb-4">
                <img src="assets/images/<?= $lap['foto'] ?>" onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=800'">
            </div>
            <h2 class="fw-bold text-uppercase"><?= $lap['nama_lapangan'] ?></h2>
            <p class="text-white-50"><?= $lap['deskripsi'] ?></p>
        </div>

        <div class="col-md-7">
            <div class="card bg-dark border-secondary p-4 rounded-4 shadow-lg">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-calendar-event-fill" style="font-size: 2.5rem; color: #00f2ff; text-shadow: 0 0 15px rgba(0,242,255,0.6);"></i>
                    <div class="ms-3">
                        <h4 class="fw-bold mb-0">Pilih Jam Main</h4>
                        <p class="text-white-50 small">Klik jam hijau untuk booking</p>
                    </div>
                </div>
                
                <form action="" method="GET" class="mb-4">
                    <input type="hidden" name="id" value="<?= $id_lapangan ?>">
                    <input type="date" name="tanggal" class="form-control input-tanggal-cyan p-3" value="<?= $tgl_cek ?>" onchange="this.form.submit()" min="<?= $tgl_sekarang ?>">
                </form>

                <div class="row g-3">
                    <?php 
                    for($i=8; $i<=22; $i++): 
                        $time_slot = sprintf("%02d:00:00", $i);
                        $is_booked = in_array($time_slot, $booked_slots);
                        $is_past = ($tgl_cek < $tgl_sekarang) || ($tgl_cek == $tgl_sekarang && $time_slot <= $jam_sekarang);
                    ?>
                    <div class="col-4 col-lg-3 text-center">
                        <?php if($is_past): ?>
                            <div class="slot-box bg-exp"><?= sprintf("%02d:00", $i) ?><br><small>PAST</small></div>
                        <?php elseif($is_booked): ?>
                            <div class="slot-box bg-booked"><?= sprintf("%02d:00", $i) ?><br><small>FULL</small></div>
                        <?php else: ?>
                            <a href="<?= isset($_SESSION['id_user']) ? 'user/booking.php' : 'login.php' ?>?id=<?= $id_lapangan ?>&tgl=<?= $tgl_cek ?>&jam=<?= $time_slot ?>" class="text-decoration-none">
                                <div class="slot-box bg-available"><?= sprintf("%02d:00", $i) ?><br><small>PILIH</small></div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>