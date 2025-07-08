<?php
require_once('../db_config.php');
// Delete review
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id = $id");
}

// Update review
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);
    $conn->query("UPDATE reviews SET rating = $rating, comment = '$comment' WHERE id = $id");
}

// Get products
$products = $conn->query("SELECT id, name FROM products");

// Handle product filter
$selectedProductId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$reviews = $conn->query("SELECT * FROM reviews WHERE product_id = $selectedProductId");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Product Reviews</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f9f0; padding: 30px;">

    <h2 style="color: green;">Delete or Edit Reviews</h2>

    <form method="get" style="margin-bottom: 20px;">
        <label style="font-weight: bold;">Select Product:</label>
        <select name="product_id" onchange="this.form.submit()" style="padding: 8px; font-size: 14px;">
            <option value="">-- Choose --</option>
            <?php while ($p = $products->fetch_assoc()) {
                $selected = $selectedProductId == $p['id'] ? 'selected' : '';
                echo "<option value='{$p['id']}' $selected>{$p['name']}</option>";
            } ?>
        </select>
    </form>

    <?php if ($selectedProductId && $reviews->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white;">
            <tr style="background-color: green; color: white;">
                <th style="text-align: left;">Rating</th>
                <th style="text-align: left;">Comment</th>
                <th style="text-align: left;">User</th>
                <th style="text-align: left;">Created</th>
                <th style="text-align: left;">Action</th>
            </tr>
            <?php while ($row = $reviews->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <td><input type="number" name="rating" value="<?= $row['rating'] ?>" style="width: 50px;"></td>
                        <td><input type="text" name="comment" value="<?= htmlspecialchars($row['comment']) ?>" style="width: 250px;"></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <button type="submit" name="update" style="padding: 5px 10px; background: green; color: white; border: none; cursor: pointer;">Update</button>
                            <a href="?product_id=<?= $selectedProductId ?>&delete=<?= $row['id'] ?>" onclick="return confirm('Delete this review?')" style="color: red; text-decoration: none; margin-left: 10px;">Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($selectedProductId): ?>
        <p style="color: gray;">No reviews found for this product.</p>
    <?php endif; ?>

</body>
</html>
