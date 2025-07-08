<?php
require_once('../db_config.php');
require_once('auth_check.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id']) || !is_numeric($_POST['order_id'])) {
    die("Invalid request.");
}

$order_id = (int)$_POST['order_id'];

// Delete order from database
$stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

// Redirect back to orders page
header("Location: manage_orders.php");
exit;
?>
