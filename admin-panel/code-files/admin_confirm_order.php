<?php
// Admin login check
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch the order data from the orders file
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$orders = json_decode(file_get_contents('orders.json'), true);
$order = null;

// Find the specific order by order_id
foreach ($orders as &$order_data) {
    if ($order_data['order_id'] === $order_id) {
        $order = &$order_data;
        break;
    }
}

if ($order && $order['status'] === 'pending') {
    // Update order status to 'completed'
    $order['status'] = 'completed';

    // Save updated orders back to file
    file_put_contents('orders.json', json_encode($orders, JSON_PRETTY_PRINT));

    // Redirect to the admin panel
    header("Location: admin_orders.php");
    exit;
} else {
    echo "Order not found or already confirmed.";
}
