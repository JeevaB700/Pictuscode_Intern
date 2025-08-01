<html>
  <head>
    <title>task-8</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
    <?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn = new mysqli("localhost", "root", "", "task-8");

        $mail = $_POST['mail'];
        $pass = $_POST['pass'];

        $result = $conn->query("SELECT * FROM users WHERE mail = '$mail'");
        $row = $result->fetch_assoc();

        if ($row && password_verify($pass, $row['pass'])) {
            $_SESSION['user_id'] = $row['user_id']; // ✅ use user_id, not id
            header("Location: homepage.php");
        } else {
            echo "<script>alert('Invalid email or password'); window.history.back();</script>";
        }
    }
    ?>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 d-flex align-items-center justify-content-center">
                <div class="form-box" style="height: 450px; width: 90%;">
                    <h1 class="text-center p-1">Login Here</h1>
                    <div class="ms-5">
                        <form method="post">
                            <div class="input pb-4">
                                Email Address: <br>
                                <input type="email" name="mail" id="mail" placeholder="Enter Email Address" required><br>
                            </div>
                            <div class="input pb-4">
                              Password:<br>
                              <input type="password" name="pass" id="pass" placeholder="Enter your Password" required><br>
                            </div>
                            <div>
                                <div class="text-center log mt-2 mb-3 me-5">
                                    <button type="submit">Login</button>
                                </div>
                                <div class="text-center log me-5">
                                    <p style="font-size: 19px;">New user? <a class="reg" href="register.html">Register here!</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-3"></div>
        </div>
  </body>
</html>