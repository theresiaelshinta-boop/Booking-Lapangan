<?php
session_start();
require '../config/database.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Akses ditolak");
}

// Ambil filter dari URL agar hasil Excel sama dengan filter di halaman laporan
$filter_tgl = isset($_GET['tgl']) ? $_GET['tgl'] : '';
$filter_bln = isset($_GET['bln']) ? $_GET['bln'] : '';
$filter_thn = isset($_GET['thn']) ? $_GET['thn'] : '';

$where_clause = "WHERE b.status = 'dikonfirmasi'";

if (!empty($filter_tgl)) {
    $where_clause .= " AND b.tanggal = '$filter_tgl'";
    $filename = "Laporan_Harian_$filter_tgl.xls";
} elseif (!empty($filter_bln) && !empty($filter_thn)) {
    $where_clause .= " AND MONTH(b.tanggal) = '$filter_bln' AND YEAR(b.tanggal) = '$filter_thn'";
    $filename = "Laporan_Bulanan_$filter_bln" . "_" . "$filter_thn.xls";
} elseif (!empty($filter_thn)) {
    $where_clause .= " AND YEAR(b.tanggal) = '$filter_thn'";
    $filename = "Laporan_Tahunan_$filter_thn.xls";
} else {
    $filename = "Semua_Laporan_Pendapatan.xls";
}

// Header untuk memicu download Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$filename");

$query = mysqli_query($conn, "SELECT b.*, u.nama, l.nama_lapangan 
                              FROM booking b 
                              LEFT JOIN users u ON b.id_user = u.id_user 
                              LEFT JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                              $where_clause
                              ORDER BY b.tanggal DESC");
?>

<h2>TRINITY SPORT CENTER - LAPORAN PENDAPATAN</h2>
<table border="1">
    <thead>
        <tr style="background-color: #00f2ff; font-weight: bold;">
            <th>No</th>
            <th>Kode Booking</th>
            <th>Nama Pelanggan</th>
            <th>Lapangan</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Total Bayar</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $total_semua = 0;
        while($row = mysqli_fetch_assoc($query)): 
            $total_semua += $row['total_harga'];
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['kode_booking']; ?></td>
            <td><?= $row['nama'] ?? 'User'; ?></td>
            <td><?= $row['nama_lapangan']; ?></td>
            <td><?= $row['tanggal']; ?></td>
            <td><?= $row['jam_mulai']; ?>:00</td>
            <td><?= $row['total_harga']; ?></td>
        </tr>
        <?php endwhile; ?>
        <tr style="font-weight: bold; background-color: #eeeeee;">
            <td colspan="6" style="text-align: right;">GRAND TOTAL:</td>
            <td><?= $total_semua; ?></td>
        </tr>
    </tbody>
</table>