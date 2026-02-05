<?php
session_start();

// Hapus semua data session khusus admin
unset($_SESSION['id_admin']);
unset($_SESSION['nama_admin']);
unset($_SESSION['role']);

// Hancurkan session secara total
session_destroy();

// Lempar balik ke halaman login admin
echo "<script>
    alert('Anda telah keluar dari Panel Admin.');
    window.location='login_admin.php';
</script>";
exit;
?>