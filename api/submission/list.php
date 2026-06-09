<?php
require_once "../config/database.php";

$username = $_GET['username'] ?? '';
$userQuery = mysqli_query($conn, "SELECT id FROM User WHERE username='$username'");
if(mysqli_num_rows($userQuery) == 0) {
    echo json_encode(["success" => true, "submissions" => []]); exit;
}
$user_id = mysqli_fetch_assoc($userQuery)['id'];

$query = "SELECT jenis, tanggal, alasan, status, created_at FROM Submission WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$submissions = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Reverse Mapping untuk UI
    $typeMap = [
        "cuti" => "Cuti Tahunan",
        "izin" => "Izin Keperluan",
        "sakit" => "Sakit",
        "lembur" => "Lembur"
    ];

    $submissions[] = [
        "type" => $typeMap[$row['jenis']] ?? ucfirst($row['jenis']),
        "date" => $row['tanggal'],
        "reason" => $row['alasan'],
        "status" => ucfirst($row['status']), // 'pending' jadi 'Pending'
        "submitted_at" => date("d M Y H:i", strtotime($row['created_at']))
    ];
}

echo json_encode(["success" => true, "submissions" => $submissions]);
?>