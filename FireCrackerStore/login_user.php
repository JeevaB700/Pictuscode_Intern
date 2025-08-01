<?php
session_start();
$loginError = "";

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $mail = $_POST['mail_id'];
    $pass = $_POST['pass'];

    $stmt = $conn->prepare("SELECT id, name, pass FROM users WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $username, $hashed_pass);
        $stmt->fetch();

        if (password_verify($pass, $hashed_pass)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['mail'] = $mail;
            header("Location: user_home.php");
            exit;
        } else {
            $loginError = "Invalid password!";
        }
    } else {
        $loginError = "Mail not found!";
    }

    $conn->close();
}
?>

<html>
<head>
  <title>User Login - Firecracker Store</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="css/styles.css">
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
          <?php if ($loginError): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <?php echo $loginError; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <p class="mt-5 text-light">New User? <a class="a-nav" href="register_user.php">Register here</a></p>
          <div class="card p-4 mb-5">
            <h3 class="text-center mb-4">User Login</h3>
            <form method="POST" action="">
              <div class="mb-3">
                <label for="mail_id" class="form-label">Mail ID</label>
                <input type="text" class="form-control" name="mail_id" id="mail_id" placeholder="Enter your Mail ID" required>
              </div>
              <div class="mb-3">
                <label for="pass" class="form-label">Password</label>
                <input type="password" class="form-control" name="pass" id="pass" placeholder="Enter your Password" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-danger">Login</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>