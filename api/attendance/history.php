<?php
require_once "../config/database.php";

$username = $_GET['username'] ?? '';
$userQuery = mysqli_query($conn, "SELECT id FROM User WHERE username='$username'");
if(mysqli_num_rows($userQuery) == 0) {
    echo json_encode(["success" => true, "history" => []]); exit;
}
$user_id = mysqli_fetch_assoc($userQuery)['id'];

$query = "SELECT tanggal, jam_masuk, jam_keluar, foto_masuk, foto_keluar, status FROM Attendance WHERE user_id='$user_id' ORDER BY tanggal DESC LIMIT 50";
$result = mysqli_query($conn, $query);

$history = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Transform rule: MySQL field -> Frontend UI expectation
    $history[] = [
        "date" => $row['tanggal'],
        "time_in" => $row['jam_masuk'],
        "time_out" => $row['jam_keluar'],
        "photo_in" => $row['foto_masuk'],
        "photo_out" => $row['foto_keluar'],
        "status" => $row['status']
    ];
}

echo json_encode(["success" => true, "history" => $history]);
?>