<?php
$conn = new mysqli("localhost", "root", "", "task-8");

$name = $_POST['name'];
$mail = $_POST['mail'];
$pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$original_email = isset($_POST['original_email']) ? $_POST['original_email'] : "";

// Check if this user already exists
$checkUser = $conn->query("SELECT * FROM users WHERE user_id = $user_id");

if ($checkUser->num_rows > 0) {
    // Update flow
    if ($mail !== $original_email) {
        $checkMail = $conn->query("SELECT id FROM users WHERE mail = '$mail'");
        if ($checkMail->num_rows > 0) {
            echo "<script>alert('Email already in use!'); window.location.href = document.referrer;</script>";
            exit();
        }
    }

    $sql = "UPDATE users SET name='$name', mail='$mail', dob='$dob', gender='$gender' WHERE user_id='$user_id'";
    $message = $conn->query($sql) ? "User updated!" : "Error: " . $conn->error;

} else {
    // Insert flow
    $checkMail = $conn->query("SELECT * FROM users WHERE mail = '$mail' OR user_id = $user_id");
    if ($checkMail->num_rows > 0) {
        echo "<script>alert('Email or User ID already exists!'); window.location.href = document.referrer;</script>";
        exit();
    }

    $sql = $conn->prepare("INSERT INTO users (user_id, name, mail, pass, dob, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param('isssss', $user_id, $name, $mail, $pass, $dob, $gender);
    $message = $sql->execute() ? "User created!" : "Error: " . $conn->error;
}

echo "<script>alert('$message'); window.location.href = document.referrer;</script>";
$conn->close();
?>
