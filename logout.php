<?php
session_start();

// 1. Hapus semua data di dalam variabel session
session_unset();

// 2. Hancurkan session-nya
session_destroy();

// 3. Arahkan ke halaman utama (index.php)
header("Location: index.php");
exit;
?>