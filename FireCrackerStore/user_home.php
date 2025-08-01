<?php
session_start();
$conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
if (!isset($_SESSION['user_id'])) header("Location: login_user.php");
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$mail = $_SESSION['mail'];
?>

<html>
<head>
  <title>Firecracker Store - User Home</title>
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
                <a class="nav-link active" href="user_home.php">Buy Products</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="user_orders.php">My Orders</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
                </li>    
            </ul>
        </div>
    </div>
</nav>
</div>

<div class="container mt-4">
  <h2 class="text-center text-light mb-4">Order Firecrackers</h2>
  <?php
  echo "<div class='row'>";
  $products = $conn->query("
    SELECT products.*, company.company_name 
    FROM products 
    JOIN company ON products.company_id = company.id 
    WHERE products.status = 'active' AND products.is_deleted = 0
    ORDER BY products.id DESC");

  while ($row = $products->fetch_assoc()) {
    $isOut = $row['availability'] <= 0;
    echo "<div class='col-md-4 mb-4'>
      <div class='card p-3'>
        <h3 class='text-center fw-bold mb-3'>" . htmlspecialchars($row['company_name']) . "</h3>";

    $images = json_decode($row['images'], true);
    $carouselId = 'carousel' . $row['id'];

    echo "<div id='{$carouselId}' class='carousel slide' data-bs-ride='false'>
            <div class='carousel-inner'>";

    foreach ($images as $index => $img) {
      $activeClass = $index === 0 ? 'active' : '';
      echo "<div class='carousel-item {$activeClass}'>
              <img src='" . htmlspecialchars($img) . "' class='d-block w-100' height='250px' onerror=\"this.src='img/logo.png';\">
            </div>";
    }

    echo "  </div>
            <button class='carousel-control-prev' type='button' data-bs-target='#{$carouselId}' data-bs-slide='prev'>
              <span class='carousel-control-prev-icon'></span>
            </button>
            <button class='carousel-control-next' type='button' data-bs-target='#{$carouselId}' data-bs-slide='next'>
              <span class='carousel-control-next-icon'></span>
            </button>
          </div>
          <h5 class='mt-3'>" . htmlspecialchars($row['product_name']) . "</h5>
          <p class='mt-2'>Price: ₹" . htmlspecialchars($row['price']) . "</p>
          <p>Available: " . htmlspecialchars($row['availability']) . "</p>";
    if ($isOut) {
      echo "<button class='btn btn-secondary w-100' disabled>Out of Stock</button>";
    } else {
      echo "<button class='btn btn-success w-100' data-bs-toggle='modal' data-bs-target='#orderModal'
            data-id='{$row['id']}' data-name='{$row['product_name']}' data-price='{$row['price']}' data-qty='{$row['availability']}'>Order Now</button>";
    }
    echo "</div></div>";
  }
  echo "</div>";
  ?>
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="orderForm" method="POST" action="place_orders.php">
        <div class="modal-header">
          <h5 class="modal-title">Place Order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="product_id" id="modalPid">
          <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="no_of_items" id="modalQty" class="form-control" min="1" value="1" required>
          </div>
          <div class="mb-3">
            <label>Total Cost</label>
            <input type="text" class="form-control" id="showCost" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Place Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Order Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-5">
        <img src="img/tick.png" width="100px" alt="Success">
        <h4 class="mt-3">Order Placed Successfully!</h4>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let currentPrice = 0;
  const orderModal = document.getElementById('orderModal');
  const qtyInput = document.getElementById('modalQty');
  const costDisplay = document.getElementById('showCost');

  // Show modal with pre-filled values
  orderModal.addEventListener('show.bs.modal', function(e) {
    const button = e.relatedTarget;
    currentPrice = parseFloat(button.getAttribute('data-price'));

    document.getElementById('modalPid').value = button.getAttribute('data-id');
    qtyInput.max = button.getAttribute('data-qty');
    qtyInput.value = 1;
    costDisplay.value = "₹" + currentPrice.toFixed(2);
  });

  // Update cost
  qtyInput.addEventListener('input', function() {
    const quantity = parseInt(this.value) || 1;
    const totalCost = quantity * currentPrice;
    costDisplay.value = "₹" + totalCost.toFixed(2);
  });

  // AJAX form submission
  document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('place_orders.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(orderModal).hide();
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        // Optional: Reload page after 3 seconds to update product availability
        setTimeout(() => location.reload(), 3000);
      } else {
        alert(data.message || "Order failed");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      alert("Something went wrong");
    });
  });
});
</script>
</body>
</html>