<?php
require_once('../db_config.php');
require_once('auth_check.php');

$msg = '';

// Check ad count before allowing insert
$countResult = $conn->query("SELECT COUNT(*) as count FROM ads");
$countRow = $countResult->fetch_assoc();
$adCount = $countRow['count'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $adCount < 2) {
    $title = $_POST['title'];

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "../images/";
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = "images/" . $imageName;
        } else {
            $msg = "<p style='color:red;'>Image upload failed.</p>";
        }
    }

    if ($imagePath !== '') {
        $stmt = $conn->prepare("INSERT INTO ads (title, image) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $imagePath);
        if ($stmt->execute()) {
            $msg = "<p style='color:green;'>Ad added successfully!</p>";
        } else {
            $msg = "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
} elseif ($adCount >= 2) {
    $msg = "<p style='color:red;'>You already have 2 ads. Please delete one before adding a new ad.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Ad</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 400px;">
        <h2 style="text-align:center;">Add New Ad</h2>
        <?php echo $msg; ?>
        <?php if ($adCount < 2): ?>
            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 15px;">
                    <label>Title:</label><br>
                    <input type="text" name="title" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label>Image:</label><br>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Add Ad</button>
            </form>
        <?php endif; ?>
        <div style="text-align:center; margin-top: 15px;">
            <a href="ads.php" style="color: #007bff;">Back to Ads</a>
        </div>
    </div>
</body>
</html>
