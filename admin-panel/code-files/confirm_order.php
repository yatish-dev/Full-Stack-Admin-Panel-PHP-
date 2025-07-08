<?php
// Include the database connection file
require_once('../db_config.php');

// Get the order ID from the URL
$order_id = $_GET['id'];

// Update the order status to "Confirmed"
$sql = "UPDATE orders SET status = 'Confirmed' WHERE id = $order_id";
if ($conn->query($sql) === TRUE) {
    header('Location: admin_orders.php'); // Redirect back to the orders list
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
