<?php
require_once('auth_check.php');
require_once('../db_config.php');

// Default date range: last 30 days
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Prepare SQL query to fetch sales data within the date range
$sql = "
    SELECT 
        order_id,
        customer_name,
        total_amount,
        status,
        order_date
    FROM orders
    WHERE order_date BETWEEN ? AND ?
    ORDER BY order_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data
$order_summary = [];
$total_orders = 0;
$total_revenue = 0;

while ($row = $result->fetch_assoc()) {
    // Collecting sales data
    $order_summary[] = [
        'order_id' => $row['order_id'],
        'customer_name' => $row['customer_name'],
        'total_amount' => $row['total_amount'],
        'status' => $row['status'],
        'order_date' => $row['order_date']
    ];
    
    // Calculate total orders and total revenue
    $total_orders++;
    $total_revenue += $row['total_amount'];
}

$stmt->close();

// Export to CSV functionality
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=sales_report.csv');
    
    $output = fopen('php://output', 'w');
    
    // Write total orders and revenue as first row
    fputcsv($output, ['Total Orders', $total_orders]);
    fputcsv($output, ['Total Revenue (₹)', number_format($total_revenue, 2)]);
    fputcsv($output, []);  // Empty row for spacing
    
    // Write column headers for order data
    fputcsv($output, ['Order ID', 'Customer Name', 'Total Amount (₹)', 'Status', 'Order Date']);
    
    // Write the order data
    foreach ($order_summary as $order) {
        fputcsv($output, [
            $order['order_id'], 
            $order['customer_name'], 
            number_format($order['total_amount'], 2), 
            $order['status'], 
            $order['order_date']
        ]);
    }
    
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sales Report - Majedar Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h3 class="text-success mb-3">Sales Report</h3>

        <!-- Date Range Filter -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" />
                </div>
                <div class="col">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" />
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
                    <a href="?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&export=csv" class="btn btn-success mt-4">Export to CSV</a>
                </div>
            </div>
        </form>

        <!-- Sales Summary Table -->
        <h5>Report from <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?></h5>
        
        <!-- Total Orders and Revenue Summary -->
        <div class="mb-4">
            <p><strong>Total Orders:</strong> <?php echo $total_orders; ?></p>
            <p><strong>Total Revenue:</strong> ₹<?php echo number_format($total_revenue, 2); ?></p>
        </div>

        <table class="table table-striped">
            <thead class="table-success">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount (₹)</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_summary as $order): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Sales Chart (Using Chart.js) -->
        <h5>Sales Chart</h5>
        <canvas id="salesChart"></canvas>
    </div>

    <script>
        // Sales data for the chart
        const orderSummary = <?php echo json_encode($order_summary); ?>;
        const labels = orderSummary.map(order => order.order_id);
        const totalSales = orderSummary.map(order => order.total_amount);

        // Create the chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Sales (₹)',
                    data: totalSales,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Order ID'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales (₹)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
