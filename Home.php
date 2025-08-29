<?php
session_start();
include "service/database.php";

$login_message = "";
$register_message = "";
$otp_message = "";

if (isset($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
    header("Location: home.php");
    exit;
}

// Login
if (isset($_POST['login'])) {
    $nik = trim($_POST['nik']);

    $stmt = $db->prepare("SELECT * FROM users WHERE nik = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $otp = rand(1000, 9999);

        $_SESSION['pending_user'] = [
            "mode" => "login",
            "nik"  => $user["nik"],
            "nama" => $user["nama_lengkap"],
            "otp"  => $otp
        ];

        $otp_message = "Kode OTP Anda: <b>$otp</b>";

        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                    otpModal.show();
                });
              </script>";
    } else {
        $login_message = "NIK tidak ditemukan. Silakan coba lagi.";
    }
}


// Register
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
        $cek_stmt = $db->prepare("SELECT nik FROM users WHERE nik = ?");
        $cek_stmt->bind_param("s", $nik);
        $cek_stmt->execute();
        $cek_result = $cek_stmt->get_result();

        if ($cek_result->num_rows > 0) {
            $register_message = "NIK sudah terdaftar.";
        } else {
            $otp = rand(1000, 9999);

            $_SESSION["pending_user"] = [
                "mode"        => "register",
                "nama_lengkap"=> $nama_lengkap,
                "email"       => $email,
                "no_telepon"  => $no_telepon,
                "nik"         => $nik,
                "otp"         => $otp
            ];

            $otp_message = "Kode OTP kamu: $otp";

            echo "<script>
                    window.addEventListener('DOMContentLoaded', function(){
                        var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                        otpModal.show();
                    });
                  </script>";
        }
    }
}

// Verifikasi OTP
if (isset($_POST['verify_otp']) && isset($_SESSION['pending_user'])) {
    $entered_otp = trim($_POST['otp']);
    $pending = $_SESSION['pending_user'];

    if ($entered_otp == $pending['otp']) {
        if ($pending['mode'] === "register") {
            // INSERT ke tabel users
            $insert_stmt = $db->prepare("
                INSERT INTO users (nama_lengkap, email, no_telepon, nik) 
                VALUES (?, ?, ?, ?)
            ");
            $insert_stmt->bind_param(
                "ssss",
                $pending['nama_lengkap'],
                $pending['email'],
                $pending['no_telepon'],
                $pending['nik']
            );

            if ($insert_stmt->execute()) {
           unset($_SESSION['pending_user']);

           header("Location: Home.php?success=registered");
            exit;
} else {
    $otp_message = "Registrasi gagal ❌ coba lagi.";
}
        } else {
            // login case
            $_SESSION["is_login"] = true;
            $_SESSION["nik"] = $pending["nik"];
            $_SESSION["nama"] = $pending["nama"];
            unset($_SESSION['pending_user']);
            header("Location: hal.php");
            exit;
        }
    } else {
        echo "<script>
                window.addEventListener('DOMContentLoaded', function(){
                    var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                    otpModal.show();
                });
              </script>";
    }
}

$showOtpModal   = isset($_SESSION['pending_user']); 
$showLoginModal = (isset($_GET['success']) && $_GET['success'] === 'registered');
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
<body data-login-message="<?= $login_message ?>" data-register-message="<?= $register_message ?>">
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

<!-- Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-body text-center p-5">
        <h3 class="fw-bold mb-4">Login</h3>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
          <div class="alert alert-success">
            Registrasi berhasil, silahkan login.
          </div>
        <?php endif; ?>

        <?php if (!empty($login_message)): ?>
          <div class="alert alert-danger"><?= $login_message ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-4 text-start">
            <label class="form-label">NIK:</label>
            <input type="text" name="nik" class="form-control rounded-pill" 
                   placeholder="16 Digit NIK" required pattern="^\d{16}$" 
                   title="NIK harus terdiri dari 16 digit angka" maxlength="16">
          </div>
          <button type="submit" name="login" 
                  class="btn btn-custom px-5 rounded-pill d-block mx-auto mb-3">
            <i class="bi bi-box-arrow-in-right"></i> LOGIN
          </button>
        </form>

        <p>Belum punya akun? 
          <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Daftar di sini
          </a>
        </p>
      </div>
    </div>
  </div>
</div>


<!-- Register -->
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

<!-- OTP -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-body text-center p-5">
        <h3 class="fw-bold mb-4">Kode OTP</h3>

        <?php if ($otp_message): ?>
          <div class="alert alert-info"><?= $otp_message ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="d-flex justify-content-center gap-3 mb-4">
            <input type="text" placeholder="Masukan OTP " name="otp" maxlength="4" class="form-control w-50 text-center" required>
          </div>
          <button type="submit" name="verify_otp" class="btn btn-custom px-5 rounded-pill d-block mx-auto">
            <i class="bi bi-check-circle"></i> KONFIRMASI
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  <?php if ($showOtpModal): ?>
    var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
    otpModal.show();
  <?php elseif ($showLoginModal): ?>
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
  <?php endif; ?>
});
</script>

<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/alert-handler.js"></script>
</body>
</html>
