<?php
session_start();
$conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
if (!isset($_SESSION['user_id'])) {
    header("Location: login_user.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$mail = $_SESSION['mail'];
?>

<html>
<head>
  <title>My Orders - Firecracker Store</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container pt-3" style="height: 25%;">
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.html">
            <img src="img/logo.png" height="80px" width="80px" class="me-3">
            <h1>Firecrackers Store</h1>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" href="user_home.php">Buy Products</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="user_orders.php">My Orders</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
                </li>    
            </ul>
        </div>
    </div>
</nav>
</div>

<div class="container mt-5">
  <h2 class="text-center text-light mb-4">My Orders</h2>
  <?php
  $orders = $conn->query("SELECT o.id,c.company_name, p.product_name, o.no_of_items, o.cost, o.status, o.created_at
                         FROM orders o 
                         JOIN products p ON o.product_id = p.id
                         JOIN company c ON p.company_id = c.id
                         WHERE o.user_id = $user_id
                         ORDER BY o.created_at DESC");
  
  if ($orders->num_rows > 0): ?>
    <table class="table table-bordered text-center">
      <thead class="table-dark">
        <tr>
          <th>Company</th>
          <th>Product</th>
          <th>Quantity</th>
          <th>Total Cost</th>
          <th>Status</th>
          <th>Order Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = $orders->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($order['company_name']) ?></td>
            <td><?= htmlspecialchars($order['product_name']) ?></td>
            <td><?= $order['no_of_items'] ?></td>
            <td>₹<?= number_format($order['cost'], 2) ?></td>
            <td>
              <span class="badge 
                <?= $order['status'] === 'Delivered' ? 'bg-success' : 
                   ($order['status'] === 'Out of Stock' ? 'bg-danger' : 'bg-warning') ?>">
                <?= $order['status'] ?>
              </span>
            </td>
            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-info text-center">
      You haven't placed any orders yet. <a href="user_home.php">Browse products</a> to get started!
    </div>
  <?php endif; ?>
</div>
</body>
</html>