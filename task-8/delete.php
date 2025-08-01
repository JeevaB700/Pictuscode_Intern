<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "task-8");

$id = $_GET['id'];
$response = ["success" => false];

$delete = $conn->query("DELETE FROM users WHERE user_id = $id");

if ($delete) {
    $response["success"] = true;
} else {
    $response["message"] = $conn->error;
}

echo json_encode($response);
?>
