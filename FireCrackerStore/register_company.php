<?php
$success = "";
$error = "";
// Backend processing at the top
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $company_name = $_POST['company_name'];
    $owner_name = $_POST['owner_name'];
    $phone_no = $_POST['phone_no'];
    $mail = $_POST['mail_id'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM company WHERE mail = ?");
    $check->bind_param("s", $mail);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO company (company_name, owner_name, phone, mail, pass)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $company_name, $owner_name, $phone_no, $mail, $pass);
        if ($stmt->execute()) {
            $success = "Company registered successfully!";
        } else {
            $error = "Registration failed. Try again.";
        }
    }
    $conn->close();
}
?>

<html>
<head>
  <title>Register Company - Firecracker Store</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
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

  <div class="row justify-content-center mt-5 pt-3 mb-5">
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

      <p class="text-light">Already a User? <a class="a-nav" href="login_company.php">Login here</a></p>
      <div class="card p-4">
        <h3 class="text-center mb-4">Register Firecracker Company</h3>
        <form method="POST" action="">
          <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="owner_name" class="form-label">Owner Name</label>
            <input type="text" name="owner_name" class="form-control" required>
          </div>
          <div class="mb-3">
              <label for="phone_no" class="form-label">Phone Number</label>
              <input type="tel" pattern="\d{10}" maxlength="10" class="form-control" name="phone_no" id="phone_no" required>
          </div>
          <div class="mb-3">
            <label for="mail_id" class="form-label">Mail ID</label>
            <input type="email" name="mail_id" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="pass" class="form-label">Password</label>
            <input type="password" minlength="6" name="pass" class="form-control" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-danger">Register Company</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>