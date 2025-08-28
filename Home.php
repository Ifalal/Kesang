<?php
session_start();
include "service/database.php";

$login_message = "";
$register_message = "";

// cek session login
if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
    header("Location: hal.php");
    exit;
}

// LOGIN 
if (isset($_POST['login'])) {
    $nik = trim($_POST['nik']);

    // Validasi menggunakan prepared statement
    $stmt = $db->prepare("SELECT * FROM users WHERE nik = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION["is_login"] = true;
        $_SESSION["nik"] = $user["nik"];
        $_SESSION["nama"] = $user["nama_lengkap"];
        header("Location: hal.php");
        exit;
    } else {
        $login_message = "NIK tidak ditemukan. Silakan coba lagi.";
    }
}

// REGISTER 
if (isset($_POST['register'])) {
    $nama_lengkap = trim($_POST["nama_lengkap"]);
    $email = trim($_POST["email"]);
    $no_telepon = trim($_POST["no_telepon"]);
    $nik = trim($_POST["nik"]);

    if (empty($nama_lengkap) || empty($email) || empty($no_telepon) || empty($nik)) {
        $register_message = "Semua field harus diisi.";
    } elseif (strlen($email) < 6) {
        $register_message = "Email minimal 6 karakter.";
    } else {
        // Cek NIK sudah ada
        $cek_stmt = $db->prepare("SELECT nik FROM users WHERE nik = ?");
        $cek_stmt->bind_param("s", $nik);
        $cek_stmt->execute();
        $cek_result = $cek_stmt->get_result();

        if ($cek_result->num_rows > 0) {
            $register_message = "NIK sudah terdaftar.";
        } else {
            $insert_stmt = $db->prepare("INSERT INTO users (nama_lengkap, email, no_telepon, nik) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $nama_lengkap, $email, $no_telepon, $nik);

            if ($insert_stmt->execute()) {
                $register_message = "Registrasi berhasil. Silakan login.";
            } else {
                $register_message = "Registrasi gagal. Coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <title>Kesang</title>
</head>
<body>
  <div class="container">
    <header class="header">
      <div class="tkami">
        <a href="tkami.html">Tentang Kami</a>
      </div>
    </header>

    <main class="hero">
      <div class="logo">
        <img src="asset/logo.png" alt="Logo">
      </div>
      <div class="content">
        <span class="badge badge-kesang">KéSANG</span>
        <h1>LEBIH PEDULI <br> KE BADANMU SENDIRI</h1>
        <p><span class="blue">KéSANG</span> hadir bantu <br> cek kesehatan <b>KAMU</b></p>
        <a href="#" class="btn-cek" data-bs-toggle="modal" data-bs-target="#loginModal">
          CEK SEKARANG
        </a>
      </div>
    </main>
  </div>

<footer class="footer">
  <div class="container text-center text-md-start">
    <div class="row align-items-center">
      <div class="col-12 col-md-3 mb-3">
        <img src="asset/Eputih.png" alt="Eputih" class="eputih img-fluid">
      </div>
      <div class="col-12 col-md-3 mb-3 fw-bold fs-5">
        <p>KONTAK</p>
        <span>Replikas@gmail.com</span><br>
        <span>085881705717</span>
      </div>
      <div class="col-12 col-md-3 mb-3">
        <p class="fw-bold fs-5">MEDIA SOSIAL</p>
        <a href="https://facebook.com" target="_blank" class="d-block">Facebook</a>
        <a href="https://instagram.com" target="_blank" class="d-block">Instagram</a>
        <a href="https://twitter.com" target="_blank" class="d-block">Twitter</a>
      </div>
      <div class="col-12 col-md-3 mb-3 text-md-end">
        <img src="asset/oscar.png" alt="Oscar" class="oscar img-fluid">
      </div>
    </div>
  </div>
</footer>

  <!-- Modal Login -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-body text-center p-5">
          <h3 class="fw-bold mb-4">Login</h3>
          <?php if ($login_message): ?>
            <div class="alert alert-danger"><?= $login_message ?></div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="mb-4 text-start">
              <label class="form-label">NIK:</label>
              <input type="text" name="nik" class="form-control rounded-pill" placeholder="16 Digit NIK" required pattern="^\d{16}$" title="NIK harus terdiri dari 16 digit angka" maxlength="16">
            </div>
            <button type="submit" name="login" class="btn btn-custom px-5 rounded-pill d-block mx-auto mb-3">
              <i class="bi bi-box-arrow-in-right"></i> LOGIN
            </button>
          </form>
          <p>Belum punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Daftar di sini</a></p>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal Register -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-body text-center p-5">
          <h3 class="fw-bold mb-4">Registrasi</h3>
          <?php if ($register_message): ?>
            <div class="alert alert-info"><?= $register_message ?></div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="mb-3 text-start">
              <label class="form-label">Nama Lengkap:</label>
              <input type="text" name="nama_lengkap" class="form-control rounded-pill" placeholder="Nama Lengkap" required>
            </div>
            <div class="mb-3 text-start">
              <label class="form-label">Email:</label>
              <input type="email" name="email" class="form-control rounded-pill" placeholder="Email" required minlength="6">
            </div>
            <div class="mb-3 text-start">
              <label class="form-label">No Telepon:</label>
              <input type="tel" name="no_telepon" class="form-control rounded-pill" placeholder="No Telepon" required>
            </div>
            <div class="mb-4 text-start">
              <label class="form-label">NIK:</label>
              <input type="text" name="nik" class="form-control rounded-pill" placeholder="16 Digit NIK" required pattern="^\d{16}$" title="NIK harus terdiri dari 16 digit angka" maxlength="16">
            </div>
            <button type="submit" name="register" class="btn btn-custom px-5 rounded-pill d-block mx-auto">
              <i class="bi bi-person-plus"></i> DAFTAR
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>