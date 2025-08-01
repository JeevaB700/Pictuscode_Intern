<?php
session_start();
$conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
if (!isset($_SESSION['company_id'])) die("Unauthorized");
$cid = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $oid);
    $stmt->execute();
}
?>

<html>
<head>
  <title>Manage Orders - Firecracker Store</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container pt-4">
  <div class="container" style="height: 25%;">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
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
                    <a class="nav-link" href="company_home.php">Manage Products</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link active" href="manage_orders.php">Manage Orders</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                    </li>    
                </ul>
            </div>
        </div>
    </nav>
  </div>

  <h2 class="text-center text-light mb-4">Orders for Your Products</h2>
  <table class="table table-bordered text-center">
    <thead class="table-dark">
      <tr><th>User</th><th>Phone No</th><th>Product</th><th>Qty</th><th>Cost</th><th>Order Date</th><th>Status</th><th>Update</th></tr>
    </thead>
    <tbody>
    <?php
      $orders = $conn->query("SELECT o.id, u.name,u.phone, p.product_name, o.no_of_items, o.cost, o.created_at, o.status
                              FROM orders o
                              JOIN products p ON o.product_id = p.id
                              JOIN users u ON o.user_id = u.id
                              WHERE p.company_id = $cid 
                              ORDER BY o.id DESC");
      while ($row = $orders->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['no_of_items']}</td>
                <td>₹{$row['cost']}</td>
                <td>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                <td>
                  <form method='POST'>
                    <input type='hidden' name='order_id' value='{$row['id']}'>
                    <select name='status' class='form-select'>
                      <option " . ($row['status'] === 'In Progress' ? 'selected' : '') . ">In Progress</option>
                      <option " . ($row['status'] === 'Delivered' ? 'selected' : '') . ">Delivered</option>
                      <option " . ($row['status'] === 'Out of Stock' ? 'selected' : '') . ">Out of Stock</option>
                    </select>
                </td>
                <td><button class='btn btn-sm btn-primary'>Update</button></form></td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>