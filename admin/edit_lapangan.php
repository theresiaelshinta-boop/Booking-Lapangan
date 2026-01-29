<?php 
session_start();
require '../config/database.php';

// 1. PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// 2. AMBIL DATA LAPANGAN
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM lapangan WHERE id_lapangan = '$id'");
$data = mysqli_fetch_assoc($query);

// 3. PROSES UPDATE
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $harga = $_POST['harga_per_jam'];
    
    if ($_FILES['foto']['name'] != "") {
        $foto_nama = time() . "_" . $_FILES['foto']['name'];
        $tmp_name = $_FILES['foto']['tmp_name'];
        $folder_tujuan = "../assets/images/" . $foto_nama;
        
        if(move_uploaded_file($tmp_name, $folder_tujuan)) {
            $update_foto = ", foto = '$foto_nama'";
        } else {
            $update_foto = "";
            echo "<script>alert('Gagal mengupload gambar!');</script>";
        }
    } else {
        $update_foto = "";
    }

    $sql = "UPDATE lapangan SET nama_lapangan = '$nama', harga_per_jam = '$harga' $update_foto WHERE id_lapangan = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='lapangan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lapangan - Trinity Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');
        
        body { background-color: #050505; color: white; font-family: 'Montserrat', sans-serif; overflow-x: hidden; }
        
        /* SIDEBAR STYLE SESUAI MAU LO */
        .sidebar-admin { background: #000; min-height: 100vh; border-right: 1px solid #222; position: fixed; width: 16%; z-index: 100; }
        
        .nav-link { 
            color: rgba(255, 255, 255, 0.4) !important; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            padding: 12px 15px; border-radius: 12px; margin-bottom: 5px; 
            text-decoration: none; 
            display: flex !important; /* KUNCI BIAR GAK TURUN */
            align-items: center !important; 
        }
        
        /* Animasi Geser & Neon */
        .nav-link:hover { 
            color: #00f2ff !important; 
            background: rgba(0, 242, 255, 0.05); 
            transform: translateX(8px); 
        }
        
        .nav-link.active { 
            color: #00f2ff !important; 
            background: rgba(0, 242, 255, 0.1); 
            font-weight: 600; 
        }

        /* Hover Logout Merah Glow */
        .nav-link.text-danger:hover { 
            color: #ff4d4d !important; 
            background: rgba(255, 77, 77, 0.1); 
            box-shadow: 0 0 15px rgba(255, 77, 77, 0.1);
            transform: translateX(8px);
        }

        /* MAIN CONTENT ANIMATION */
        .main-content { margin-left: 16%; padding: 40px; animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .edit-card { background: #111; border: 1px solid #222; border-radius: 24px; padding: 35px; transition: 0.3s; }
        .edit-card:hover { border-color: #333; }
        
        .form-control { background: #000 !important; border: 1px solid #333 !important; color: white !important; border-radius: 12px; padding: 12px; }
        .form-control:focus { border-color: #00f2ff !important; box-shadow: none; }
        
        .img-preview { width: 100%; max-height: 300px; object-fit: cover; border-radius: 20px; border: 2px solid #333; background: #000; transition: 0.3s; }
        
        .btn-update { background: #00f2ff; color: #000; font-weight: 700; border-radius: 12px; padding: 14px; border: none; width: 100%; transition: 0.3s; }
        .btn-update:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0, 242, 255, 0.3); background: #00d8e4; }
    </style>
</head>
<body>

<div class="sidebar-admin p-4">
    <h5 class="fw-bold text-info mb-5">TRINITY ADMIN</h5>
    <div class="nav flex-column">
        <a href="dashboard.php" class="nav-link mb-2">
            <i class="bi bi-grid-1x2-fill me-3"></i> <span>Dashboard</span>
        </a>
        
        <a href="lapangan.php" class="nav-link active mb-2">
            <i class="bi bi-trophy-fill me-3"></i> <span>Lapangan</span>
        </a>
        
        <a href="konfirmasi.php" class="nav-link mb-2">
            <i class="bi bi-credit-card-2-front-fill me-3"></i> <span>Konfirmasi</span>
        </a>
        
        <a href="laporan.php" class="nav-link mb-2">
            <i class="bi bi-file-earmark-bar-graph-fill me-3"></i> <span>Laporan</span>
        </a>
        
        <hr class="border-secondary my-4">
        
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left me-3"></i> <span>Logout</span>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4 d-flex align-items-center">
        <a href="lapangan.php" class="btn btn-outline-secondary btn-sm rounded-circle me-3 text-white"><i class="bi bi-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">Edit <span class="text-info">Lapangan</span></h3>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="edit-card shadow-lg">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="small fw-bold text-info text-uppercase mb-2" style="letter-spacing: 1px;">Nama Lapangan</label>
                        <input type="text" name="nama_lapangan" class="form-control" value="<?= htmlspecialchars($data['nama_lapangan']) ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="small fw-bold text-info text-uppercase mb-2" style="letter-spacing: 1px;">Harga per Jam</label>
                        <input type="number" name="harga_per_jam" class="form-control" value="<?= $data['harga_per_jam'] ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-info text-uppercase d-block mb-2" style="letter-spacing: 1px;">Pratinjau Foto</label>
                        <?php 
                            $foto_sekarang = "../assets/images/" . $data['foto'];
                            if(empty($data['foto']) || !file_exists($foto_sekarang)) {
                                $foto_sekarang = "https://placehold.co/600x400/111/00f2ff?text=No+Image";
                            }
                        ?>
                        <img src="<?= $foto_sekarang ?>" id="output" class="img-preview mb-3 shadow">
                        <input type="file" name="foto" class="form-control" accept="image/*" onchange="loadFile(event)">
                        <p class="mt-2 small text-white-50"><i class="bi bi-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengubah foto.</p>
                    </div>

                    <button type="submit" name="update" class="btn btn-update shadow-lg">SIMPAN PERUBAHAN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>