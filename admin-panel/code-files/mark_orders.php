<?php
require_once('../db_config.php');
require_once('auth_check.php');

// Handle status update via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status_input = $_POST['status'];
    $new_status = in_array($status_input, ['Delivered', 'Pending', 'Cancelled']) ? $status_input : 'Pending';

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
    exit;
}

// Fetch orders
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Orders</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 1100px;
            margin: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        #searchInput {
            width: 100%;
            padding: 10px;
            margin: 15px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .status-label {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 6px;
            color: white;
            display: inline-block;
        }
        .delivered {
            background: #28a745;
        }
        .pending {
            background: #ffc107;
            color: black;
        }
        .cancelled {
            background: #dc3545;
        }
        .action-btn {
            margin-left: 8px;
            padding: 4px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .cancel-order-btn {
            background: #dc3545;
            color: white;
        }
        .undo-cancel-btn {
            background: #007bff;
            color: white;
        }
        .action-btn:disabled {
            background: #aaa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Orders</h2>
        <input type="text" id="searchInput" placeholder="Search by customer name or order ID...">

        <table id="ordersTable">
            <thead>
                <tr>
                    <th>Order number</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                    <td class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td id="status-<?php echo $row['id']; ?>">
                        <span class="status-label <?php
                            echo $row['status'] === 'Delivered' ? 'delivered' :
                                 ($row['status'] === 'Cancelled' ? 'cancelled' : 'pending'); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <input type="checkbox"
                               class="delivery-toggle"
                               data-id="<?php echo $row['id']; ?>"
                               <?php echo $row['status'] === 'Delivered' ? 'checked' : ''; ?>
                               <?php echo $row['status'] === 'Cancelled' ? 'disabled' : ''; ?>>

                        <?php if ($row['status'] === 'Cancelled'): ?>
                            <button class="action-btn undo-cancel-btn" data-id="<?php echo $row['id']; ?>">Undo</button>
                        <?php else: ?>
                            <button class="action-btn cancel-order-btn" data-id="<?php echo $row['id']; ?>">Cancel</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            // Toggle Delivered/Pending
            $('.delivery-toggle').change(function () {
                const orderId = $(this).data('id');
                const newStatus = $(this).is(':checked') ? 'Delivered' : 'Pending';

                $.post('mark_orders.php', {
                    order_id: orderId,
                    status: newStatus
                }, function (res) {
                    const result = JSON.parse(res);
                    if (result.success) {
                        updateStatusUI(orderId, newStatus);
                    } else {
                        alert('Failed to update order status.');
                    }
                });
            });

            // Cancel Order
            $('.cancel-order-btn').click(function () {
                if (!confirm("Are you sure you want to cancel this order?")) return;

                const orderId = $(this).data('id');

                $.post('mark_orders.php', {
                    order_id: orderId,
                    status: 'Cancelled'
                }, function (res) {
                    const result = JSON.parse(res);
                    if (result.success) {
                        updateStatusUI(orderId, 'Cancelled');
                    } else {
                        alert('Failed to cancel order.');
                    }
                });
            });

            // Undo Cancel
            $('.undo-cancel-btn').click(function () {
                const orderId = $(this).data('id');

                $.post('mark_orders.php', {
                    order_id: orderId,
                    status: 'Pending'
                }, function (res) {
                    const result = JSON.parse(res);
                    if (result.success) {
                        updateStatusUI(orderId, 'Pending');
                    } else {
                        alert('Failed to undo cancel.');
                    }
                });
            });

           function updateStatusUI(orderId, newStatus) {
    const statusCell = $('#status-' + orderId);
    const label = $('<span></span>')
        .addClass('status-label')
        .text(newStatus)
        .removeClass('delivered pending cancelled')
        .addClass(newStatus.toLowerCase());
    statusCell.html(label);

    const row = $('input[data-id="' + orderId + '"]').closest('tr');
    const toggle = row.find('.delivery-toggle');
    const actionCell = row.find('td:last');

    // ✅ Clear all existing buttons inside the Actions cell
    actionCell.find('button').remove();

    if (newStatus === 'Cancelled') {
        toggle.prop('disabled', true).prop('checked', false);
        actionCell.append('<button class="action-btn undo-cancel-btn" data-id="' + orderId + '">Undo</button>');
    } else {
        toggle.prop('disabled', false);
        toggle.prop('checked', newStatus === 'Delivered');
        actionCell.append('<button class="action-btn cancel-order-btn" data-id="' + orderId + '">Cancel</button>');
    }
}


            // Live search filter
            $('#searchInput').on('keyup', function () {
                const searchText = $(this).val().toLowerCase();
                $('#ordersTable tbody tr').each(function () {
                    const orderId = $(this).find('td:first').text().toLowerCase();
                    const customer = $(this).find('.customer-name').text().toLowerCase();
                    $(this).toggle(orderId.includes(searchText) || customer.includes(searchText));
                });
            });

            // Handle dynamically added buttons (delegated)
            $('#ordersTable').on('click', '.undo-cancel-btn', function () {
                const orderId = $(this).data('id');
                $.post('mark_orders.php', {
                    order_id: orderId,
                    status: 'Pending'
                }, function (res) {
                    const result = JSON.parse(res);
                    if (result.success) {
                        updateStatusUI(orderId, 'Pending');
                    }
                });
            });

            $('#ordersTable').on('click', '.cancel-order-btn', function () {
                if (!confirm("Are you sure you want to cancel this order?")) return;
                const orderId = $(this).data('id');
                $.post('mark_orders.php', {
                    order_id: orderId,
                    status: 'Cancelled'
                }, function (res) {
                    const result = JSON.parse(res);
                    if (result.success) {
                        updateStatusUI(orderId, 'Cancelled');
                    }
                });
            });
        });
    </script>
</body>
</html>
