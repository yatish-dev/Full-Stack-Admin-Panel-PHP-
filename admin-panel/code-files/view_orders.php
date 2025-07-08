<?php
require_once('auth_check.php');
require_once('../db_config.php');

// Fetch orders
$sql = "SELECT * FROM orders ORDER BY order_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>All Orders - Majedar Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.85rem;
    }
    .badge-pending {
      background-color: #ffc107;
      color: #212529;
    }
    .badge-delivered {
      background-color: #28a745;
    }
    .badge-cancelled {
      background-color: #dc3545;
    }
    .search-input {
      max-width: 300px;
      margin-left: auto;
    }
  </style>
</head>
<body>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-success">All Orders</h3>
    <input type="text" id="searchInput" class="form-control search-input" placeholder="Search orders..." />
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle" id="ordersTable">
      <thead class="table-success">
        <tr>
          <th>#Order ID</th>
          <th>Customer Name</th>
          <th>Total Amount</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
            <td class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></td>
            <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
            <td>
              <?php
              $status = strtolower($row['status']);
              $badgeClass = match($status) {
                  'delivered' => 'badge-delivered',
                  'cancelled' => 'badge-cancelled',
                  default => 'badge-pending'
              };
              ?>
              <span class="status-badge badge <?php echo $badgeClass; ?>">
                <?php echo ucfirst($status); ?>
              </span>
            </td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  Actions
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="order_details.php?order_id=<?php echo $row['order_id']; ?>">View Details</a></li>
                  <li><a class="dropdown-item text-danger" href="mark_orders.php?order_id=<?php echo $row['order_id']; ?>&action=cancel">Cancel</a></li>
                  <li><a class="dropdown-item text-success" href="mark_orders.php?order_id=<?php echo $row['order_id']; ?>&action=deliver">Mark as Delivered</a></li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  document.querySelector('#searchInput').addEventListener('keyup', function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#ordersTable tbody tr');

    rows.forEach(row => {
      let orderId = row.children[0].textContent.toLowerCase();
      let customer = row.querySelector('.customer-name').textContent.toLowerCase();
      row.style.display = (orderId.includes(filter) || customer.includes(filter)) ? '' : 'none';
    });
  });
</script>

</body>
</html>
