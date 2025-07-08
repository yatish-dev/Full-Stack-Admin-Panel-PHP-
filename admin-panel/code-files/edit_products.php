<?php
// Include your database connection here
require_once('../db_config.php');
require_once('auth_check.php');

// Handle form submission to update the product
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload
    $image_path = $product['image']; // Default to current image if no new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Validate and move the uploaded image to a directory
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = $_FILES['image']['name'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Check file extension
        if (in_array(strtolower($image_ext), $allowed_ext)) {
            $image_new_name = 'product_' . time() . '.' . $image_ext;
            $image_path = 'uploads/' . $image_new_name;
            
            if (move_uploaded_file($image_tmp, $image_path)) {
                // File uploaded successfully
            } else {
                echo "Error uploading the image!";
            }
        } else {
            echo "Invalid image file type!";
        }
    }

    // Update the product in the database
    $query = "UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $price, $description, $image_path, $product_id);
    $stmt->execute();
    $stmt->close();

    echo "Product updated successfully!";
}

// Fetch all products for the dropdown
$query = "SELECT id, name FROM products";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Product</title>
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
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Product</h2>

    <!-- Select product dropdown -->
    <form method="GET" action="edit_products.php">
      <div class="form-group">
        <label for="productSelect">Select Product</label>
        <select name="id" id="productSelect" class="form-control" onchange="this.form.submit()">
          <option value="">Select a product...</option>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['id']}'" . (isset($_GET['id']) && $_GET['id'] == $row['id'] ? ' selected' : '') . ">{$row['name']}</option>";
            }
          }
          ?>
        </select>
      </div>
    </form>

    <?php
    // If product ID is selected, fetch its details
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $product_id = $_GET['id'];

        // Fetch the selected product details
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
    ?>
    
    <!-- Product Edit Form -->
    <h3>Edit Product: <?php echo $product['name']; ?></h3>
    <form method="POST" action="edit_products.php" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
      
      <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?php echo $product['name']; ?>" required />
      </div>
      
      <div class="form-group">
        <label for="price">Price</label>
        <input type="text" id="price" name="price" class="form-control" value="<?php echo $product['price']; ?>" required />
      </div>
      
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $product['description']; ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="image">Product Image</label>
        <input type="file" id="image" name="image" class="form-control" />
        <?php if (!empty($product['image'])): ?>
          <p>Current Image: <img src="<?php echo $product['image']; ?>" alt="Product Image" width="100" /></p>
        <?php endif; ?>
      </div>
      
      <button type="submit" name="update" class="btn btn-success">Update Product</button>
    </form>

    <?php
        } else {
            echo "<p>Product not found.</p>";
        }
    }
    ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
