<?php
require_once('../db_config.php'); 
require_once('auth_check.php');  // Assuming this handles user authentication

$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
  $product_id = $_POST['product_id'];

  // Check if we are marking as newly launched or removing the status
  if (isset($_POST['mark_new'])) {
    $query = "UPDATE products SET is_new = 1 WHERE id = $product_id";
    mysqli_query($conn, $query);
    $success_message = "Product marked as Newly Launched.";
  } elseif (isset($_POST['remove_new'])) {
    $query = "UPDATE products SET is_new = 0 WHERE id = $product_id";
    mysqli_query($conn, $query);
    $success_message = "Product removed from Newly Launched.";
  }
}

// Fetch products
$result = mysqli_query($conn, "SELECT id, name, is_new FROM products");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mark Newly Launched Products</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
  <div style="max-width: 500px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2 style="color: #009e3c; text-align: center;">Mark Newly Launched</h2>

    <?php if ($success_message): ?>
      <p style="color: green; text-align: center; font-weight: bold;"> <?= htmlspecialchars($success_message) ?> </p>
    <?php endif; ?>

    <form method="post" style="margin-top: 20px;">
      <label for="product_id" style="display: block; margin-bottom: 8px; font-weight: bold;">Select Product:</label>
      <select name="product_id" id="product_id" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 15px;" onchange="this.form.submit()">
        <option value="">-- Select Product --</option>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <option value="<?= $row['id'] ?>" <?= (isset($_POST['product_id']) && $_POST['product_id'] == $row['id']) ? 'selected' : '' ?>><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
      </select>

      <?php
        if (isset($_POST['product_id'])) {
          $selected_id = $_POST['product_id'];
          $check = mysqli_query($conn, "SELECT is_new FROM products WHERE id = $selected_id");
          $row = mysqli_fetch_assoc($check);
          if ($row['is_new'] == 1) {
            echo '<button type="submit" name="remove_new" style="background-color: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Remove from Newly Launched</button>';
          } else {
            echo '<button type="submit" name="mark_new" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Mark as Newly Launched</button>';
          }
        }
      ?>
    </form>
  </div>
</body>
</html>
