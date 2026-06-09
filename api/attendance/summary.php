<?php
require_once "../config/database.php";

$username = $_GET['username'] ?? '';
$userQuery = mysqli_query($conn, "SELECT id FROM User WHERE username='$username'");
if(mysqli_num_rows($userQuery) == 0) {
    echo json_encode(["success" => false]); exit;
}
$user_id = mysqli_fetch_assoc($userQuery)['id'];

$bulanSekarang = date('Y-m');
$hariIni = date('Y-m-d');

// Total Hadir (bulan ini)
$qTotal = mysqli_query($conn, "SELECT COUNT(id) as hadir FROM Attendance WHERE user_id='$user_id' AND tanggal LIKE '$bulanSekarang-%'");
$hadir = mysqli_fetch_assoc($qTotal)['hadir'];

// Absen hari ini
$qToday = mysqli_query($conn, "SELECT jam_masuk, jam_keluar FROM Attendance WHERE user_id='$user_id' AND tanggal='$hariIni'");
$today_in = null;
$today_out = null;

if(mysqli_num_rows($qToday) > 0) {
    $row = mysqli_fetch_assoc($qToday);
    // Hapus detik (misal 08:00:00 jadi 08:00)
    $today_in = $row['jam_masuk'] ? substr($row['jam_masuk'], 0, 5) : null;
    $today_out = $row['jam_keluar'] ? substr($row['jam_keluar'], 0, 5) : null;
}

echo json_encode([
    "success" => true,
    "hadir" => $hadir,
    "total" => $hadir, // Disesuaikan jika ada logika hari kerja
    "today_in" => $today_in,
    "today_out" => $today_out
]);
?>