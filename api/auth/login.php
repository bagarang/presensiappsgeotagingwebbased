<?php
require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$pin = $data['pin'] ?? ''; // Frontend kirim 'pin', DB pakai 'password'

if (!$username || !$pin) {
    echo json_encode(["success" => false, "message" => "Username dan PIN wajib diisi"]);
    exit;
}

$username = mysqli_real_escape_string($conn, $username);
$pin = mysqli_real_escape_string($conn, $pin);

$query = "SELECT id, username, role, nama, jabatan, nip, foto_profile FROM users WHERE username='$username' AND password='$pin' AND status='aktif'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    echo json_encode([
        "success" => true,
        "session" => [
            "user_id" => $user['id'],
            "username" => $user['username'],
            "role" => $user['role']
        ],
        "profile" => [
            "name" => $user['nama'],
            "job" => $user['jabatan'],
            "nip" => $user['nip'],
            "imgBase64" => $user['foto_profile'] ?: 'https://via.placeholder.com/150'
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Username atau PIN salah!"]);
}
?>