<?php
require_once('../db_config.php'); // Database connection
require_once('auth_check.php');   // Authentication check

// Handle product deletion
if (isset($_GET['delete'])) {
    $productId = intval($_GET['delete']); // Sanitize input

    // Step 1: Delete related reviews
    $deleteReviews = "DELETE FROM reviews WHERE product_id = $productId";
    mysqli_query($conn, $deleteReviews);

    // Step 2: Delete the product
    $deleteProduct = "DELETE FROM products WHERE id = $productId";
    if (mysqli_query($conn, $deleteProduct)) {
        echo "<script>alert('Product and related reviews deleted successfully!'); window.location='delete_products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_error($conn) . "'); window.location='delete_products.php';</script>";
    }
    exit;
}

// Fetch products from database
$products = [];
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Delete Products</title>
</head>
<body style="font-family: 'Segoe UI', sans-serif; background-color: #f4f8f7; padding: 2rem;">
    <h2 style="color: #009e3c;">Delete Products</h2>

    <?php if (empty($products)): ?>
        <p style="color: #555;">No products found.</p>
    <?php else: ?>
        <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #009e3c; color: white;">
                    <th style="padding: 0.75rem;">Product Name</th>
                    <th style="padding: 0.75rem;">Price</th>
                    <th style="padding: 0.75rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr style="background-color: #fff;">
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($product['name']); ?></td>
                    <td style="padding: 0.75rem;">₹<?php echo number_format($product['price'], 2); ?></td>
                    <td style="padding: 0.75rem;">
                        <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" style="color: red; text-decoration: none;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
