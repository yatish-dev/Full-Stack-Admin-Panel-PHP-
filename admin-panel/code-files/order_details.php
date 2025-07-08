<?php
require_once('auth_check.php');
require_once('../db_config.php');

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("❌ Order ID not provided.");
}

// Fetch order from DB
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param('s', $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    die("❌ Order not found for ID: " . htmlspecialchars($order_id));
}

// Decode cart JSON
$order_items = json_decode($order['cart_json'], true);
if (!is_array($order_items)) {
    $order_items = [];
}

// Prepare product details
$product_details = [];
foreach ($order_items as $product_id => $item) {
    $product_sql = "SELECT * FROM products WHERE id = ?";
    $product_stmt = $conn->prepare($product_sql);
    $product_stmt->bind_param('i', $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product_details[$product_id] = $product_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Order Details - Majedar Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container mt-4">
    <h3 class="text-success mb-3">Order #<?php echo htmlspecialchars($order['order_id']); ?> Details</h3>

    <h5>Customer Information</h5>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['whatsapp_number']); ?></p>
    <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>

    <h5>Ordered Items</h5>
    <?php if (!empty($order_items)): ?>
      <table class="table table-bordered">
        <thead class="table-success">
          <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (₹)</th>
            <th>Total (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order_items as $product_id => $item): 
            $product = $product_details[$product_id] ?? ['name' => 'Unknown Product'];
          ?>
          <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo intval($item['quantity']); ?></td>
            <td><?php echo number_format($item['price'], 2); ?></td>
            <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No items found in this order.</p>
    <?php endif; ?>

    <h5>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></h5>

    <a href="view_orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
  </div>
</body>
</html>
