<?php
session_start();
require '../config/database.php'; // Menggunakan koneksi $conn

// Pastikan yang akses sudah login dan melalui tombol submit
if (!isset($_SESSION['id_user']) || !isset($_POST['submit_booking'])) {
    header("Location: index.php");
    exit();
}

// 1. Ambil data dari form
$id_user = $_SESSION['id_user'];
$id_lapangan = mysqli_real_escape_string($conn, $_POST['id_lapangan']);
$tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
$jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
$total_harga = mysqli_real_escape_string($conn, $_POST['total_harga']);

// 2. Buat Kode Booking Otomatis (Unik)
$kode_booking = "BK" . date('Ymd') . rand(100, 999);

// 3. Ambil Nama Lapangan (untuk keperluan pesan WA)
$query_lap = mysqli_query($conn, "SELECT nama_lapangan FROM lapangan WHERE id_lapangan = '$id_lapangan'");
$data_lap = mysqli_fetch_assoc($query_lap);
$nama_lapangan = $data_lap['nama_lapangan'];

// 4. Cek Double Booking (mencegah dua orang booking jam yang sama)
$cek_jadwal = mysqli_query($conn, "SELECT * FROM booking WHERE id_lapangan = '$id_lapangan' AND tanggal = '$tanggal' AND jam_mulai = '$jam_mulai' AND status != 'batal'");

if (mysqli_num_rows($cek_jadwal) > 0) {
    echo "<script>alert('Waduh Seng, jam ini barusan dipesan orang lain!'); window.location='booking.php?id=$id_lapangan';</script>";
    exit();
}

// 5. Simpan ke Database (Status awal: 'menunggu')
$query_insert = "INSERT INTO booking (id_user, id_lapangan, kode_booking, tanggal, jam_mulai, total_harga, status) 
                 VALUES ('$id_user', '$id_lapangan', '$kode_booking', '$tanggal', '$jam_mulai', '$total_harga', 'menunggu')";

if (mysqli_query($conn, $query_insert)) {
    $id_booking_baru = mysqli_insert_id($conn);

    // --- LOGIKA NOTIFIKASI WHATSAPP OTOMATIS KE NOMOR KAMU ---
    $nomor_admin = "6285694261056"; // Nomor kamu sudah disesuaikan
    $pesan_wa = "Halo Admin, ada pesanan baru masuk! ⚽%0A%0A" .
                "*Kode:* " . $kode_booking . "%0A" .
                "*Lapangan:* " . $nama_lapangan . "%0A" .
                "*Jadwal:* " . date('d M Y', strtotime($tanggal)) . " Jam " . $jam_mulai . ":00 WIB%0A" .
                "*Total:* Rp " . number_format($total_harga, 0, ',', '.') . "%0A%0A" .
                "Mohon segera dicek di Dashboard Admin ya!";

    $wa_link = "https://wa.me/$nomor_admin?text=$pesan_wa";

    // Script JS: Buka WA di tab baru & arahkan ke halaman bayar.php
    echo "
    <script>
        window.open('$wa_link', '_blank');
        window.location.href = 'bayar.php?id=$id_booking_baru';
    </script>";
    exit();

} else {
    echo "Gagal memproses pesanan: " . mysqli_error($conn);
}
?>