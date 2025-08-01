<?php
session_start();
$conn = new mysqli("localhost", "root", "", "task-8");

$user_id = $_POST['user_id'];
$companyName = $_POST['companyName'];
$position = $_POST['position'];
$experience = $_POST['experience'];
$salary = $_POST['salary'];

// Check if entry exists
$check = $conn->query("SELECT user_id FROM user_details WHERE user_id = '$user_id'");

if ($check->num_rows > 0) {
    $sql = $conn->prepare("UPDATE user_details SET companyName=?, position=?, experience=?, salary=? WHERE user_id=?");
    $sql->bind_param("ssidi", $companyName, $position, $experience, $salary, $user_id);
} else {
    $sql = $conn->prepare("INSERT INTO user_details (user_id, companyName, position, experience, salary) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("issid", $user_id, $companyName, $position, $experience, $salary);
}

if ($sql->execute()) {
    echo "<script>alert('Details saved!'); window.location.href = 'homepage.php';</script>";
} else {
    echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
}

$conn->close();
?>
