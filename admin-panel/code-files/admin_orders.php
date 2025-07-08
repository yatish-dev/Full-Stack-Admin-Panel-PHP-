<?php
// Admin login check (you should implement actual login mechanism)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch all orders from the orders.json file (or a database)
$orders = json_decode(file_get_contents('orders.json'), true);

// Admin UI to view all orders
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center mb-4">Manage Orders</h2>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars($order['order_id']) ?></td>
          <td><?= htmlspecialchars($order['fullname']) ?></td>
          <td><?= htmlspecialchars($order['status']) ?></td>
          <td>
            <?php if ($order['status'] == 'pending'): ?>
              <a href="admin_confirm_order.php?order_id=<?= urlencode($order['order_id']) ?>" class="btn btn-success">Confirm</a>
            <?php else: ?>
              <button class="btn btn-secondary" disabled>Completed</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
