<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
include "service/database.php";

if (empty($_SESSION['nik']) || empty($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    die("Harus login dulu untuk akses PDF.");
}

$nik = $_GET['nik'] ?? $_SESSION['nik'];


$stmt_pasien = $db->prepare("SELECT * FROM view_data_pasien WHERE nik = ?");
$stmt_pasien->bind_param("s", $nik);
$stmt_pasien->execute();
$result_pasien = $stmt_pasien->get_result();
$pasien = $result_pasien->fetch_assoc();
if (!$pasien) {
    die("Data pasien tidak ditemukan.");
}

$umur = "";
if (!empty($pasien['tanggal_lahir'])) {
    $tgl_lahir = new DateTime($pasien['tanggal_lahir']);
    $today = new DateTime();
    $umur = $today->diff($tgl_lahir)->y;
}

$stmt_inap = $db->prepare("
    SELECT * FROM rawat_inap 
    WHERE nik = ? 
    AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
");
$stmt_inap->bind_param("s", $nik);
$stmt_inap->execute();
$rawat_inap = $stmt_inap->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_jalan = $db->prepare("
    SELECT * FROM rawat_jalan 
    WHERE nik = ? 
    AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
");
$stmt_jalan->bind_param("s", $nik);
$stmt_jalan->execute();
$rawat_jalan = $stmt_jalan->get_result()->fetch_all(MYSQLI_ASSOC);

$skor = 0;

foreach ($rawat_inap as $ri) {
    $skor_penyakit = 1;
    if ($ri['tingkat_penyakit'] == "Sedang") $skor_penyakit = 2;
    elseif ($ri['tingkat_penyakit'] == "Berat") $skor_penyakit = 3;

    $skor += ($ri['jumlah_hari'] / 2) + ($skor_penyakit * 3);
    if ($ri['status'] == "Sembuh") $skor -= 2;
}

foreach ($rawat_jalan as $rj) {
    $skor_penyakit = 1;
    if ($rj['tingkat_penyakit'] == "Sedang") $skor_penyakit = 2;
    elseif ($rj['tingkat_penyakit'] == "Berat") $skor_penyakit = 3;

    $skor += ($rj['jumlah_datang'] * 2) + ($skor_penyakit * 2);
    if ($rj['status'] == "Sembuh") $skor -= 2;
}

if ($skor <= 7) {
    $status = "Sehat";
    $warna_status = "#0d6efd"; 
    $width = "100%";
} elseif ($skor <= 15) {
    $status = "Cukup Sehat";
    $warna_status = "#ffc107"; 
    $width = "50%";
} else {
    $status = "Kurang Sehat";
    $warna_status = "#dc3545"; 
    $width = "20%";
}

ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <title>Laporan Kesehatan</title>
  <style>
  body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }
    h2 {
      text-align: center;
      margin: 0 0 5px 0;
      color: #5DCECE;
      font-weight: 800;
    }
    h5 {
      text-align: center;
      margin: 0 0 20px 0;
      color: #666;
      font-weight: 500;
    }
    h3 {
      margin: 0 0 12px 0;
      font-size: 14px;
      color: #5DCECE;
      font-weight: 700;
    }
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 18px;
      border: 1px solid #ddd;
    }
    .label {
      font-weight: 600;
      width: 130px;
      display: inline-block;
      margin-right: 6px;
      color: #444;
    }
    img.foto {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #5DCECE;
      padding: 2px;
    }
    .progress {
      width: 100%;
      height: 15px;
      background: #eafafa;
      border-radius: 8px;
      overflow: hidden;
      margin-top: 8px;
    }
    .progress-bar {
      height: 100%;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 6px 7px;
      text-align: left;
    }
    th {
      background: #5DCECE;
      color: white;
      font-weight: 600;
    }
    tr:nth-child(even) { background: #f9fdfd; }
  </style>
</head>
<body>

<h2>Riwayat Kesehatan</h2>
<h5>Data Pasien</h5>

<div class="card">
  <?php
  $foto_db = $pasien['foto'] ?? "";
  $foto_full = empty($foto_db) 
      ? "http://localhost/KESANG/asset/Group 49.png" 
      : "http://localhost/KESANG/" . ltrim($foto_db, '/');
  ?>
  <table style="width: 100%; border: none;">
    <tr>
      <td style="width: 120px; border: none;">
        <img src="<?= $foto_full ?>" class="foto">
      </td>
      <td style="border: none; padding-left: 12px;">
        <div><span class="label">NIK:</span> <?= htmlspecialchars($pasien['nik']) ?></div>
        <div><span class="label">Nama:</span> <?= htmlspecialchars($pasien['nama_lengkap']) ?></div>
        <div><span class="label">Jenis Kelamin:</span> <?= htmlspecialchars($pasien['jenis_kelamin']) ?></div>
        <div><span class="label">Umur:</span> <?= $umur ?> Tahun</div>
        <div><span class="label">No. BPJS:</span> <?= htmlspecialchars($pasien['no_bpjs']) ?></div>
        <div><span class="label">No. Asuransi:</span> <?= htmlspecialchars($pasien['no_asuransi']) ?></div>
        <div><span class="label">Berat/Tinggi:</span> <?= htmlspecialchars($pasien['berat_badan']) ?> kg / <?= htmlspecialchars($pasien['tinggi_badan']) ?> cm</div>
        <div><span class="label">Golongan Darah:</span> <?= htmlspecialchars($pasien['gol_darah']) ?></div>
      </td>
    </tr>
  </table>
</div>

<div class="card">
  <h3>Status Kesehatan: <?= $status ?></h3>
  <div class="progress">
    <div class="progress-bar" style="background:<?= $warna_status ?>; width:<?= $width ?>;"></div>
  </div>
</div>

<div class="card">
  <h3>Riwayat Rawat Inap</h3>
  <?php if (!empty($rawat_inap)): ?>
  <table>
    <tr>
      <th>Tanggal</th><th>Rumah Sakit</th><th>Diagnosa</th><th>Status</th><th>Penanggung Biaya</th><th>Jumlah hari</th>
    </tr>
    <?php foreach ($rawat_inap as $ri): ?>
    <tr>
      <td><?= htmlspecialchars($ri['tanggal']) ?></td>
      <td><?= htmlspecialchars($ri['rumah_sakit']) ?></td>
      <td><?= htmlspecialchars($ri['diagnosa']) ?></td>
      <td><?= htmlspecialchars($ri['status']) ?></td>
      <td><?= htmlspecialchars($ri['penanggung_jawab_biaya']) ?></td>
      <td><?= htmlspecialchars($ri['jumlah_hari']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><i>Tidak ada data rawat inap.</i><?php endif; ?>
</div>

<div class="card">
  <h3>Riwayat Rawat Jalan</h3>
  <?php if (!empty($rawat_jalan)): ?>
  <table>
    <tr>
      <th>Tanggal</th><th>Rumah Sakit</th><th>Diagnosa</th><th>Status</th><th>Penanggung Biaya</th><th>Jumlah Kunjungan</th>
    </tr>
    <?php foreach ($rawat_jalan as $rj): ?>
    <tr>
      <td><?= htmlspecialchars($rj['tanggal']) ?></td>
      <td><?= htmlspecialchars($rj['rumah_sakit']) ?></td>
      <td><?= htmlspecialchars($rj['diagnosa']) ?></td>
      <td><?= htmlspecialchars($rj['status']) ?></td>
      <td><?= htmlspecialchars($rj['penanggung_jawab_biaya']) ?></td>
      <td><?= htmlspecialchars($rj['jumlah_datang']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><i>Tidak ada data rawat jalan.</i><?php endif; ?>
</div>

</body>
</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Kesehatan_{$nik}.pdf", ["Attachment" => true]);
exit;
