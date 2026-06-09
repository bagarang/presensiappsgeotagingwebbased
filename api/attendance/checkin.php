<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$photo = $data['photo'] ?? '';
$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;
$timestamp = $data['timestamp'] ?? time();

// 1. Convert username -> user_id
$userQuery = mysqli_query($conn, "SELECT id FROM User WHERE username='$username'");
if(mysqli_num_rows($userQuery) == 0) {
    echo json_encode(["success" => false, "message" => "User tidak ditemukan"]); exit;
}
$user = mysqli_fetch_assoc($userQuery);
$user_id = $user['id'];

// 2. Set Format Waktu
$tanggal = date("Y-m-d", $timestamp / 1000); // Konversi JS timestamp (ms) ke s
$jam_masuk = date("H:i:s", $timestamp / 1000);

// 3. Cek apakah sudah absen hari ini
$check = mysqli_query($conn, "SELECT id FROM Attendance WHERE user_id='$user_id' AND tanggal='$tanggal'");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(["success" => false, "message" => "Sudah absen masuk hari ini"]); exit;
}

// 4. Insert Data
$query = "INSERT INTO Attendance (user_id, tanggal, jam_masuk, lat_masuk, lng_masuk, foto_masuk, status) 
          VALUES ('$user_id', '$tanggal', '$jam_masuk', '$lat', '$lng', '$photo', 'Hadir')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true, "message" => "Absen Masuk Berhasil"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan data"]);
}
?>