<?php
$success = $error = "";

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $name = $_POST['user_name'];
    $mail = $_POST['mail_id'];
    $phone = $_POST['phone_no'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE mail = ?");
    $check->bind_param("s", $mail);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered. Please login.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, mail, phone, pass) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $mail,$phone, $pass);
        if ($stmt->execute()) {
            $success = "User registered successfully!";
        } else {
            $error = "Registration failed.";
        }
    }

    $conn->close();
}
?>
<html>
<head>
  <title>User Registration - Firecracker Store</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div style="height: 100%;">
    <div class="container pt-4" style="height: 15%;">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid d-flex justify-content-center">
                <a class="navbar-brand d-flex align-items-center" href="index.html">
                    <img src="img/logo.png" height="80px" width="80px" class="me-3">
                    <h1>Firecrackers Store</h1>
                </a>
            </div>
        </nav>
    </div>

  <div class="container pt-3" style="height: 85%;">
    <div class="row justify-content-center">
      <div class="col-md-7">
        <?php if ($success): ?>
          <div class="alert alert-success alert-dismissible mt-3" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php elseif ($error): ?>
          <div class="alert alert-danger alert-dismissible mt-3" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <p class="text-light mt-5">Already a User? <a class="a-nav" href="login_user.php">Login here</a></p>
        <div class="card p-4 mb-5">
          <h3 class="text-center mb-4">User Registration</h3>
          <form method="POST" action="">
            <div class="mb-3">
              <label for="user_name" class="form-label">User Name</label>
              <input type="text" class="form-control" name="user_name" id="user_name" required>
            </div>
            <div class="mb-3">
              <label for="phone_no" class="form-label">Phone Number</label>
              <input type="tel" pattern="\d{10}" maxlength="10" class="form-control" name="phone_no" id="phone_no" required>
            </div>
            <div class="mb-3">
              <label for="mail_id" class="form-label">Mail ID</label>
              <input type="email" class="form-control" name="mail_id" id="mail_id" required>
            </div>
            <div class="mb-3">
              <label for="pass" class="form-label">Password</label>
              <input type="password" minlength="6" class="form-control" name="pass" id="pass" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-danger">Register User</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>