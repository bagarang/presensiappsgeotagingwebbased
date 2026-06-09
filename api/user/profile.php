<?php
require_once "../config/database.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
    $username = $_GET['username'] ?? '';
    $query = mysqli_query($conn, "SELECT nama, jabatan, nip, foto_profile FROM User WHERE username='$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        echo json_encode([
            "success" => true,
            "profile" => [
                "name" => $user['nama'],
                "job" => $user['jabatan'],
                "nip" => $user['nip'],
                "imgBase64" => $user['foto_profile']
            ]
        ]);
    } else {
        echo json_encode(["success" => false]);
    }
} 
else if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $name = mysqli_real_escape_string($conn, $data['name'] ?? '');
    $job = mysqli_real_escape_string($conn, $data['job'] ?? '');
    $nip = mysqli_real_escape_string($conn, $data['nip'] ?? '');
    $imgBase64 = mysqli_real_escape_string($conn, $data['imgBase64'] ?? '');

    $query = "UPDATE User SET nama='$name', jabatan='$job', nip='$nip', foto_profile='$imgBase64' WHERE username='$username'";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Profil diupdate"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal update profil"]);
    }
}
?>