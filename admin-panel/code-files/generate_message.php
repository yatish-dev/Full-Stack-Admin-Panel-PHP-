<?php
require_once('auth_check.php');
require_once('../db_config.php');

$orderDetails = null;
$paymentRequestMessage = '';
$postPaymentMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = trim($_POST['order_id']);

    // Fetch order
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        $customerName = $order['customer_name'];
        $shippingAddress = $order['shipping_address'];
        $whatsappNumber = $order['whatsapp_number'];
        $totalAmount = number_format($order['total_amount'], 2);
        $cartJson = $order['cart_json'];
        $items = json_decode($cartJson, true);

        // Build item list
        $itemList = '';
        foreach ($items as $item) {
            $itemList .= "- {$item['name']} (x{$item['quantity']})\n";
        }

        // Message 1: Payment Request
        $paymentRequestMessage = "Hello *$customerName*,\n\nThank you for placing your order (Order ID: *$orderId*). Please find the summary below:\n\n$itemList\nTotal Amount: ₹$totalAmount\n\n📍Delivery Address:\n$shippingAddress\n\nKindly make the payment to confirm your order. 🙏\n\n- *Majedar*";

        // Message 2: Payment Confirmation
        $postPaymentMessage = "Hi *$customerName*,\n\nWe’ve received your payment for Order ID: *$orderId* ✅\n\nHere's your confirmed order summary:\n\n$itemList\nTotal Paid: ₹$totalAmount\n\n📍Delivery Address:\n$shippingAddress\n\nWe’ll notify you when your order is out for delivery. ✨\n\n- *Majedar Team*";

    } else {
        $paymentRequestMessage = "❌ Order not found. Please check the Order ID.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate WhatsApp Order Message</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }
        .container {
            background: white;
            padding: 25px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        textarea {
            height: 200px;
            font-family: monospace;
            margin-bottom: 20px;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            font-weight: bold;
            border-radius: 5px;
        }
        button:hover {
            background: #218838;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Generate WhatsApp Order Messages</h2>
        <form method="POST">
            <label>Enter Order ID</label>
            <input type="text" name="order_id" placeholder="e.g. MJ1234" required />
            <button type="submit">Generate</button>
        </form>

        <?php if (!empty($paymentRequestMessage)): ?>
            <h4>💰 Payment Request Message</h4>
            <textarea readonly><?php echo $paymentRequestMessage; ?></textarea>

            <h4>✅ Payment Confirmation Message</h4>
            <textarea readonly><?php echo $postPaymentMessage; ?></textarea>
        <?php endif; ?>
    </div>
</body>
</html>
