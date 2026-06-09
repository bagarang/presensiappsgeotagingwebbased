<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$photo = $data['photo'] ?? '';
$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;
$timestamp = $data['timestamp'] ?? time();

$userQuery = mysqli_query($conn, "SELECT id FROM User WHERE username='$username'");
if(mysqli_num_rows($userQuery) == 0) {
    echo json_encode(["success" => false, "message" => "User tidak ditemukan"]); exit;
}
$user = mysqli_fetch_assoc($userQuery);
$user_id = $user['id'];

$tanggal = date("Y-m-d", $timestamp / 1000);
$jam_keluar = date("H:i:s", $timestamp / 1000);

// Cek absensi masuk
$check = mysqli_query($conn, "SELECT id FROM Attendance WHERE user_id='$user_id' AND tanggal='$tanggal'");
if (mysqli_num_rows($check) == 0) {
    echo json_encode(["success" => false, "message" => "Belum absen masuk hari ini"]); exit;
}

$query = "UPDATE Attendance SET jam_keluar='$jam_keluar', lat_keluar='$lat', lng_keluar='$lng', foto_keluar='$photo' WHERE user_id='$user_id' AND tanggal='$tanggal'";

if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true, "message" => "Absen Keluar Berhasil"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal update data"]);
}
?>