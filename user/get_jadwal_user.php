<?php
date_default_timezone_set('Asia/Jakarta');
require '../config/database.php';

// Pastikan parameter ID lapangan dan Tanggal ada
if (!isset($_GET['id']) || !isset($_GET['tgl'])) {
    die("Data tidak lengkap.");
}

$id_lapangan = mysqli_real_escape_string($conn, $_GET['id']);
$tanggal_pilihan = mysqli_real_escape_string($conn, $_GET['tgl']);
$tanggal_sekarang = date('Y-m-d');
$jam_sekarang = (int)date('H');

// 1. AMBIL DETAIL LAPANGAN (Termasuk Nama File Foto dari DB)
$sql_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan='$id_lapangan'");
$lap = mysqli_fetch_assoc($sql_lap);

// Path foto relatif dari folder user ke assets
$foto_path = "../assets/images/" . $lap['foto'];

// List jam operasional (8 pagi - 10 malam)
$list_jam = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'];
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-dark border-0 overflow-hidden shadow-lg" style="border-radius: 20px;">
            <div class="row g-0">
                <div class="col-md-5">
                    <img src="<?= $foto_path; ?>" 
                         class="img-fluid h-100 w-100" 
                         style="object-fit: cover; min-height: 250px;" 
                         alt="<?= $lap['nama_lapangan']; ?>"
                         onerror="this.src='https://via.placeholder.com/500x300?text=Foto+Lapangan+Tidak+Ada'">
                </div>
                <div class="col-md-7 p-4 d-flex flex-column justify-content-center" style="background: linear-gradient(90deg, #000 0%, rgba(20,20,20,0.8) 100%);">
                    <h2 class="fw-bold text-uppercase mb-1 text-white"><?= $lap['nama_lapangan']; ?></h2>
                    <p class="text-info mb-3"><i class="bi bi-tag-fill me-2"></i><?= $lap['jenis']; ?> Court</p>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="px-3 py-2 rounded-pill bg-danger fw-bold shadow-sm" style="font-size: 1.1rem;">
                            Rp <?= number_format($lap['harga_per_jam'], 0, ',', '.'); ?> <span class="small fw-normal">/ Jam</span>
                        </div>
                    </div>
                    
                    <p class="text-white-50 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Pilih salah satu kotak jam di bawah untuk memesan lapangan ini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3 fw-bold"><i class="bi bi-clock-history me-2 text-danger"></i>Pilih Jam Tersedia</h5>

<div class="row g-3"> 
<?php
foreach ($list_jam as $jam) {
    // Logika Cek Status Booking di Database
    $cek = mysqli_query($conn, "SELECT id_booking FROM booking 
           WHERE id_lapangan = '$id_lapangan' AND tanggal = '$tanggal_pilihan' 
           AND jam_mulai = '$jam' AND status != 'batal'");

    $is_booked = mysqli_num_rows($cek) > 0;

    // Logika Jam Kadaluarsa (Passed)
    $is_expired = false;
    $jam_angka = (int)substr($jam, 0, 2);
    if ($tanggal_pilihan < $tanggal_sekarang) {
        $is_expired = true;
    } elseif ($tanggal_pilihan == $tanggal_sekarang) {
        if ($jam_angka <= $jam_sekarang) { $is_expired = true; }
    }

    echo '<div class="col-4 col-md-3 col-lg-2">';
    
    if ($is_booked) {
        // Kotak FULL
        echo '
        <div class="time-slot slot-booked text-center opacity-50">
            <i class="bi bi-x-circle d-block mb-1"></i>
            <span>'.$jam.'</span>
            <small class="d-block text-uppercase" style="font-size:8px;">Terisi</small>
        </div>';
    } elseif ($is_expired) {
        // Kotak PASSED
        echo '
        <div class="time-slot text-center" style="border:1px dashed #444; color:#444; cursor:not-allowed;">
            <i class="bi bi-lock d-block mb-1"></i>
            <span>'.$jam.'</span>
            <small class="d-block text-uppercase" style="font-size:8px;">Lewat</small>
        </div>';
    } else {
        // Kotak READY (BISA DIKLIK)
        // Memanggil fungsi JS siapkanBooking() yang ada di user/booking.php
        echo '
        <div class="time-slot slot-available text-center" style="cursor:pointer;" 
             onclick="siapkanBooking(\''.$id_lapangan.'\', \''.$lap['nama_lapangan'].'\', \''.$tanggal_pilihan.'\', \''.$jam.'\')">
            <i class="bi bi-plus-circle d-block mb-1 text-info"></i>
            <span>'.$jam.'</span>
            <small class="d-block fw-bold text-info" style="font-size:8px;">PILIH</small>
        </div>';
    }
    echo '</div>';
}
?>
</div>