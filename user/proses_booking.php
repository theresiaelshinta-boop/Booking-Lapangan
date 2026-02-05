<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['id_user']) || !isset($_POST['submit_booking'])) {
    header("Location: index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_lapangan = mysqli_real_escape_string($conn, $_POST['id_lapangan']);
$tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
$jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
$total_harga = mysqli_real_escape_string($conn, $_POST['total_harga']);
$kode_booking = "BK" . date('Ymd') . rand(100, 999);

// Cek Double Booking
$cek = mysqli_query($conn, "SELECT * FROM booking WHERE id_lapangan = '$id_lapangan' AND tanggal = '$tanggal' AND jam_mulai = '$jam_mulai' AND status != 'batal'");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Jam sudah dipesan!'); window.location='booking.php?id=$id_lapangan';</script>";
    exit();
}

// Simpan data (bukti_bayar dibiarkan NULL agar tombol upload muncul di riwayat)
$query = "INSERT INTO booking (id_user, id_lapangan, kode_booking, tanggal, jam_mulai, total_harga, status, bukti_bayar) 
          VALUES ('$id_user', '$id_lapangan', '$kode_booking', '$tanggal', '$jam_mulai', '$total_harga', 'menunggu', NULL)";

if (mysqli_query($conn, $query)) {
    $id_baru = mysqli_insert_id($conn);
    echo "<script>
            alert('Booking Berhasil! Silahkan upload bukti pembayaran.');
            window.location.href = 'bayar.php?id=$id_baru';
          </script>";
}
?>