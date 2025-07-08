<?php
require_once('auth_check.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

require_once('../db_config.php');

$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) as revenue FROM orders")->fetch_assoc()['revenue'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin: 0; font-family: 'Segoe UI', sans-serif; background: #f0f2f5;">

    <div style="padding: 20px; max-width: 1200px; margin: auto;">

        <!-- Header -->
        <div style="background: linear-gradient(90deg,rgb(145, 189, 98),rgb(25, 101, 0)); padding: 30px 20px; color: white; border-radius: 10px; text-align: center;">
            <h2 style="margin: 0; font-size: 28px;">Welcome, <?php echo $_SESSION['username']; ?> (Admin)</h2>
            <p style="margin-top: 10px;">Here’s an overview of your admin dashboard.</p>
        </div>

        <!-- Navigation -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; margin: 30px 0;">
            <a href="view_orders.php" target="main" style="background: #007bff; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">Manage Orders</a>
            <a href="manage_users.php" target="main" style="background: #28a745; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">Manage Users</a>
            <a href="sells.php" target="main" style="background: #ffc107; color: black; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">Get Sales Report</a>
        </div>

        <!-- Stats Cards -->
        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">

            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 250px; text-align: center;">
                <h3 style="color: #007bff;">Total Orders</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $totalOrders; ?></p>
            </div>

            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 250px; text-align: center;">
                <h3 style="color: #28a745;">Total Users</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $totalUsers; ?></p>
            </div>

            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 250px; text-align: center;">
                <h3 style="color: #ffc107;">Total Revenue</h3>
                <p style="font-size: 24px; font-weight: bold;">₹<?php echo number_format($totalRevenue, 2); ?></p>
            </div>


        </div>
    </div>
 <!-- Footer -->
        <div style="text-align: center; margin-top: 50px; color: #888;">
        <p style="margin: 0; font-size: 14px; color: #555;">
    © 2025 Yourname. All rights reserved. | Developed by Yatish -
    <a href="mailto:yatishjobanputra@outlook.com">Email</a> OR
    <a href="https://wa.me/919662851606" target="_blank" rel="noopener noreferrer">WhatsApp</a>
  </p>
        </div>
</body>
</html>
