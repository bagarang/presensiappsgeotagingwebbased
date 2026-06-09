<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$type = $data['type'] ?? '';
$date = $data['date'] ?? '';
$reason = $data['reason'] ?? '';

$userQuery = mysqli_query($conn, "SELECT id FROM User WHERE username='$username'");
if(mysqli_num_rows($userQuery) == 0) {
    echo json_encode(["success" => false, "message" => "User tidak ditemukan"]); exit;
}
$user_id = mysqli_fetch_assoc($userQuery)['id'];

// Map Frontend 'type' ke MySQL enum 'jenis' ('izin', 'cuti', 'sakit', 'lembur')
$jenisMap = [
    "Cuti" => "cuti",
    "Izin" => "izin",
    "Sakit" => "sakit",
    "Lembur" => "lembur"
];
$jenis = $jenisMap[$type] ?? 'izin';

$query = "INSERT INTO Submission (user_id, jenis, tanggal, alasan, status) VALUES ('$user_id', '$jenis', '$date', '$reason', 'pending')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true, "message" => "Pengajuan berhasil dikirim"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan pengajuan"]);
}
?>