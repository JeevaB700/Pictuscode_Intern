<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "task-8");

$user_id = $_GET['id'];
$response = ["success" => false];

$query = $conn->query("SELECT * FROM user_details WHERE user_id = $user_id");

if ($query->num_rows > 0) {
    $response["success"] = true;
    $response["details"] = $query->fetch_assoc();
}

echo json_encode($response);
?>
