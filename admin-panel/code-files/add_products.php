<?php
require_once('../db_config.php');
require_once('auth_check.php');

// Fetch categories
$category_result = $conn->query("SELECT * FROM categories");
$categories = $category_result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = $_FILES['image']['name'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($image_ext), $allowed_ext)) {
            $image_new_name = 'product_' . time() . '.' . $image_ext;
            $image_path = 'uploads/' . $image_new_name;

            if (!move_uploaded_file($image_tmp, '../' . $image_path)) {
                echo "Error uploading the image!";
            }
        } else {
            echo "Invalid image file type!";
        }
    }

    $query = "INSERT INTO products (name, price, description, image, category_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $price, $description, $image_path, $category_id);
    $stmt->execute();
    $stmt->close();

    echo "Product added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f8f7;
    }
    .container {
      margin-top: 30px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .category-thumb {
      display: inline-block;
      text-align: center;
      margin: 10px;
    }
    .category-thumb img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Add Product</h2>

    <form method="POST" action="add_products.php" enctype="multipart/form-data">
      <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" class="form-control" required />
      </div>

      <div class="form-group">
        <label for="price">Price</label>
        <input type="text" id="price" name="price" class="form-control" required />
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
      </div>

      <div class="form-group">
  <label for="category_id">Select Category</label>
  <select id="category_id" name="category_id" class="form-control" required>
    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>

      <div class="form-group">
        <label for="image">Product Image</label>
        <input type="file" id="image" name="image" class="form-control" />
      </div>

      <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
    </form>

    <hr />

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
