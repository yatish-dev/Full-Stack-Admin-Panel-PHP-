<?php
require_once('auth_check.php');
require_once('../db_config.php');

// Fetch total orders
$sql = "SELECT COUNT(*) AS total_orders FROM orders";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_orders = $row['total_orders'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Total Orders - Majedar Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* Add custom styles */
  </style>
</head>
<body>
  <div class="container mt-4">
    <h3 class="text-success mb-3">Total Orders</h3>
    <p>Total Orders: <strong><?php echo $total_orders; ?></strong></p>
  </div>
</body>
</html>
