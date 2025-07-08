<?php
require_once('../db_config.php');
require_once('auth_check.php');
$message = "";
$productData = null;

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $result = $conn->query("SELECT id, name, is_best_seller FROM products WHERE id = $product_id");
    $productData = $result->fetch_assoc();
}

if (isset($_POST['toggle_status'])) {
    $product_id = $_POST['product_id'];
    $new_status = $_POST['current_status'] == 1 ? 0 : 1;
    $conn->query("UPDATE products SET is_best_seller = $new_status WHERE id = $product_id");
    $message = "Updated successfully.";
    $productData['is_best_seller'] = $new_status;
}

$products = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Make Best Seller</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
  <div style="max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <h2 style="color: #009e3c; margin-bottom: 20px;">Make Product a Best Seller</h2>

    <?php if ($message): ?>
      <p style="padding: 10px; background-color: #e0ffe0; color: #007700; border-radius: 5px;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 8px;">Select Product:</label>
      <select name="product_id" required onchange="this.form.submit()" style="padding: 8px; width: 100%; border: 1px solid #ccc; border-radius: 5px;">
        <option value="">-- Choose Product --</option>
        <?php while ($row = $products->fetch_assoc()) { ?>
          <option value="<?= $row['id'] ?>" <?= (isset($productData) && $productData['id'] == $row['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['name']) ?>
          </option>
        <?php } ?>
      </select>
    </form>

    <?php if ($productData): ?>
      <div style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px;"><?= htmlspecialchars($productData['name']) ?></h3>
        <p style="margin-bottom: 10px;">
          Status:
          <strong style="color: <?= $productData['is_best_seller'] ? '#009e3c' : '#cc0000' ?>;">
            <?= $productData['is_best_seller'] ? '✅ Best Seller' : '❌ Not a Best Seller' ?>
          </strong>
        </p>

        <form method="post">
          <input type="hidden" name="product_id" value="<?= $productData['id'] ?>">
          <input type="hidden" name="current_status" value="<?= $productData['is_best_seller'] ?>">
          <button type="submit" name="toggle_status"
            style="background-color: <?= $productData['is_best_seller'] ? '#cc0000' : '#009e3c' ?>; 
                   color: white; border: none; padding: 10px 20px; border-radius: 5px; 
                   cursor: pointer;">
            <?= $productData['is_best_seller'] ? 'Remove from Best Seller' : 'Mark as Best Seller' ?>
          </button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
