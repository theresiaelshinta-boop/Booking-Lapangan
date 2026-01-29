<?php
require '../config/database.php';

// Menghitung jumlah pesanan yang statusnya 'menunggu'
$q = mysqli_query($conn, "SELECT COUNT(*) as jml FROM booking WHERE status = 'menunggu' OR status = 'menunggu_konfirmasi'");
$res = mysqli_fetch_assoc($q);

header('Content-Type: application/json');
echo json_encode($res);
?>