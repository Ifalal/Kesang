<?php  
session_start();

// Pastikan koneksi database sudah di-include dan variabel $db tersedia
include "service/database.php";

// Cek apakah user sudah login dan nik ada di session
if (empty($_SESSION['nik']) || empty($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: home.php");
    exit;
}

$nik = $_SESSION['nik'];

// --- Ambil data pasien
$stmt_pasien = $db->prepare("SELECT * FROM view_data_pasien WHERE nik = ?");
if (!$stmt_pasien) {
    die("Prepare statement gagal: " . $db->error);
}
$stmt_pasien->bind_param("s", $nik);
$stmt_pasien->execute();
$result_pasien = $stmt_pasien->get_result();

if ($result_pasien->num_rows === 0) {
    die("Data pasien dengan NIK $nik tidak ditemukan.");
}
$pasien = $result_pasien->fetch_assoc();

// --- Hitung umur dari tanggal lahir
$umur = "";
if (!empty($pasien['tanggal_lahir'])) {
    $tgl_lahir = new DateTime($pasien['tanggal_lahir']);
    $today = new DateTime();
    $umur = $today->diff($tgl_lahir)->y; // umur dalam tahun
}

// --- Ambil data rawat inap (1 tahun terakhir)
$stmt_inap = $db->prepare("
    SELECT * FROM rawat_inap 
    WHERE nik = ? 
    AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
");
$stmt_inap->bind_param("s", $nik);
$stmt_inap->execute();
$result_inap = $stmt_inap->get_result();
$rawat_inap = [];
while ($row = $result_inap->fetch_assoc()) {
    $rawat_inap[] = $row;
}

// --- Ambil data rawat jalan (1 tahun terakhir)
$stmt_jalan = $db->prepare("
    SELECT * FROM rawat_jalan 
    WHERE nik = ? 
    AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
");
$stmt_jalan->bind_param("s", $nik);
$stmt_jalan->execute();
$result_jalan = $stmt_jalan->get_result();
$rawat_jalan = [];
while ($row = $result_jalan->fetch_assoc()) {
    $rawat_jalan[] = $row;
}

// Hitung skor kesehatan
$skor = 0;

// Rawat inap
foreach ($rawat_inap as $ri) {
    $skor_penyakit = 1; // default ringan
    if ($ri['tingkat_penyakit'] == "Sedang") {
        $skor_penyakit = 2;
    } elseif ($ri['tingkat_penyakit'] == "Berat") {
        $skor_penyakit = 3;
    }

    $skor += ($ri['jumlah_hari'] / 2) + ($skor_penyakit * 3);

    if ($ri['status'] == "Sembuh") {
        $skor -= 2; // dikurang 2 karna sudah sembuh
    }
}

// Rawat jalan
foreach ($rawat_jalan as $rj) {
    $skor_penyakit = 1; 
    if ($rj['tingkat_penyakit'] == "Sedang") {
        $skor_penyakit = 2;
    } elseif ($rj['tingkat_penyakit'] == "Berat") {
        $skor_penyakit = 3;
    }

    $skor += ($rj['jumlah_datang'] * 2) + ($skor_penyakit * 2);

    if ($rj['status'] == "Sembuh") {
        $skor -= 2;
    }
}

// Menentukan Status dari score 
if ($skor <= 7) {
    $status = "Sehat";
    $warna_status = "bg-primary";
    $width = "100%";
} elseif ($skor <= 15) {
    $status = "Cukup Sehat";
    $warna_status = "bg-warning";
    $width = "50%";
} else {
    $status = "Kurang Sehat";
    $warna_status = "bg-danger";
    $width = "20%";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Test Kesehatan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid py-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="logout.php" class="text-decoration-none fw-bold text-secondary">
      <i class="bi bi-arrow-left-circle-fill" ></i> Kembali
    </a>
    <h5 class="fw-bold">Riwayat Kesehatan</h5>
    <span class="fw-bold text-info">KéSANG</span>
  </div>

  <div class="row g-4">
    <!-- Ilustrasi -->
    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center">
      <img src="asset/amico.png" class="img-fluid w-75" alt="Ilustrasi Kesehatan">
    </div>

    <!-- Konten -->
    <div class="col-md-7">
      <div class="card shadow-sm rounded-4 p-4">
        <h5 class="fw-bold text-center mb-3">Data Pasien</h5>

        <!-- Profil -->
      <div class="row g-3 mb-3 pasien-data">
  <div class="col-md-3 text-center">
    <img src="<?= htmlspecialchars($pasien['foto']) ?: 'asset/Group 49.png' ?>"
         width="130" height="130" 
         class="img-fluid rounded-3" 
         alt="Foto Pasien">
  </div>

  <div class="col-md-9">
    <div class="row g-2">

      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= htmlspecialchars($pasien['nik']) ?>" 
               readonly>
      </div>

      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= htmlspecialchars($pasien['no_bpjs']) ?>" 
               readonly>
      </div>

      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= htmlspecialchars($pasien['nama_lengkap']) ?>" 
               readonly>
      </div>

      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= htmlspecialchars($pasien['no_asuransi']) ?>" 
               readonly>
      </div>

      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= htmlspecialchars($pasien['jenis_kelamin']) ?>" 
               readonly>
      </div>

      <!-- Berat/Tinggi digabung -->
      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= htmlspecialchars($pasien['berat_badan']) ?> kg / <?= htmlspecialchars($pasien['tinggi_badan']) ?> cm" 
               readonly>
      </div>

      <!-- Umur otomatis -->
      <div class="col-md-6">
        <input type="text" class="form-control" 
               value=" <?= $umur ?> Tahun" 
               readonly>
      </div>

      <div class="col-md-6">
        <input type="text" class="form-control" 
               value="Golongan Darah: <?= htmlspecialchars($pasien['gol_darah']) ?>" 
               readonly>
      </div>

    </div>
  </div>
</div>


        <!-- Status Kesehatan -->
        <h6 class="fw-bold">Status Kesehatan: 
        <div class="progress mb-2">
          <div class="progress-bar <?= $warna_status ?>" role="progressbar" style="width: <?= $width ?>"></div>
        </div>
        <div class="d-flex gap-3 small mb-3">
          <span><span class="badge bg-primary">&nbsp;</span> Sehat</span>
          <span><span class="badge bg-warning">&nbsp;</span> Cukup Sehat</span>
          <span><span class="badge bg-danger">&nbsp;</span> Tidak Sehat</span>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#inap">Rawat Inap (<?= count($rawat_inap) ?>)</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#jalan">Rawat Jalan (<?= count($rawat_jalan) ?>)</button></li>
        </ul>

        <div class="tab-content">
        
<!-- Tab Rawat Inap -->
<div class="tab-pane fade show active" id="inap">
  <div class="accordion" id="accordionInap">
    <?php if (count($rawat_inap) > 0): ?>
      <?php foreach($rawat_inap as $r): ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?= $r['no_rekam_medis'] ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInap<?= $r['no_rekam_medis'] ?>">
            <?= htmlspecialchars($r['rumah_sakit']) ?> - <?= htmlspecialchars($r['tanggal']) ?>
          </button>
        </h2>
        <div id="collapseInap<?= $r['no_rekam_medis'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionInap">
          <div class="accordion-body">
            <div class="row g-2">
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= $r['jumlah_hari'] ?> Hari" disabled></div>
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($r['nama_dokter']) ?>" disabled></div>
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($r['diagnosa']) ?>" disabled></div>
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($r['penyebab_sakit']) ?>" disabled></div>
            </div>
      <div class="mt-2">
  <label class="fw-bold small">Status:</label>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['status'] === 'Sembuh') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Sembuh</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['status'] === 'Belum Sembuh') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Proses Sembuh</label>
  </div>
</div>

<div class="mt-2">
  <label class="fw-bold small">Penanggung Jawab Biaya:</label>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['penanggung_jawab_biaya'] === 'BPJS') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">BPJS</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['penanggung_jawab_biaya'] === 'Asuransi') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Asuransi</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['penanggung_jawab_biaya'] === 'Mandiri') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Mandiri</label>
  </div>
</div>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">Belum ada data rawat inap.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Tab Rawat Jalan -->
<div class="tab-pane fade" id="jalan">
  <div class="accordion" id="accordionJalan">
    <?php if (count($rawat_jalan) > 0): ?>
      <?php foreach($rawat_jalan as $r): ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingJalan<?= $r['no_rekam_medis'] ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJalan<?= $r['no_rekam_medis'] ?>">
            <?= htmlspecialchars($r['rumah_sakit']) ?> - <?= htmlspecialchars($r['tanggal']) ?>
          </button>
        </h2>
        <div id="collapseJalan<?= $r['no_rekam_medis'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionJalan">
          <div class="accordion-body">
            <div class="row g-2">
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= $r['jumlah_datang'] ?> Kali" disabled></div>
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($r['nama_dokter']) ?>" disabled></div>
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($r['diagnosa']) ?>" disabled></div>
              <div class="col-md-6"><input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($r['penyebab_sakit']) ?>" disabled></div>
            </div>
        <div class="mt-2">
  <label class="fw-bold small">Status:</label>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['status'] === 'Sembuh') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Sembuh</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['status'] === 'Belum Sembuh') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Proses Sembuh</label>
  </div>
</div>

<div class="mt-2">
  <label class="fw-bold small">Penanggung Jawab Biaya:</label>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['penanggung_jawab_biaya'] === 'BPJS') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">BPJS</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['penanggung_jawab_biaya'] === 'Asuransi') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Asuransi</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" 
           <?= ($r['penanggung_jawab_biaya'] === 'Mandiri') ? 'checked' : '' ?> disabled>
    <label class="form-check-label small">Mandiri</label>
  </div>
</div>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">Belum ada data rawat jalan.</p>
    <?php endif; ?>
  </div>
</div>
<a href="export_pdf.php" class="btn-cek" target="_blank">📄 Export PDF</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
