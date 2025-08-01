<?php
session_start();
header('Content-Type: application/json'); // Important: Respond with JSON

$conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$qty = intval($_POST['no_of_items']);

// Validate quantity
if ($qty <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid quantity"]);
    exit;
}

// Get price and availability
$product = $conn->query("SELECT price, availability FROM products WHERE id = $product_id");
if (!$product || $product->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Product not found"]);
    exit;
}

$data = $product->fetch_assoc();
if ($qty > $data['availability']) {
    echo json_encode(["success" => false, "message" => "Only {$data['availability']} available"]);
    exit;
}

$price = $data['price'];
$cost = $price * $qty;

// Insert order
$stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, no_of_items, cost, status) 
                        VALUES (?, ?, ?, ?, 'In Progress')");
$stmt->bind_param("iiid", $user_id, $product_id, $qty, $cost);
$stmt->execute();

// Update stock
$conn->query("UPDATE products SET availability = availability - $qty WHERE id = $product_id");

echo json_encode(["success" => true]);
$stmt->close();
$conn->close();
?>