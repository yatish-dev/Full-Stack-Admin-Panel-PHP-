<?php
require_once('../db_config.php');
require_once('auth_check.php');

$message = '';
$sliderData = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_slider'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $link = $_POST['link'];
    $status = isset($_POST['status']) ? 1 : 0;

    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $imagePath = 'images/' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $update = "UPDATE sliders SET title='$title', link='$link', image='$imagePath', status=$status WHERE id=$id";
    } else {
        $update = "UPDATE sliders SET title='$title', link='$link', status=$status WHERE id=$id";
    }

    if ($conn->query($update)) {
        $message = "<p style='color:green; text-align:center;'>Slider updated successfully!</p>";
    } else {
        $message = "<p style='color:red; text-align:center;'>Failed to update slider.</p>";
    }
}

// Fetch selected slider
if (isset($_GET['id'])) {
    $slider_id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM sliders WHERE id = $slider_id");
    $sliderData = $result->fetch_assoc();
}

// Fetch all sliders
$sliderList = $conn->query("SELECT id, title FROM sliders");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Slider</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8f8f8; padding: 30px; text-align:center;">
    <h2 style="margin-bottom: 20px;">Edit Slider</h2>

    <form method="get" action="" style="margin-bottom: 20px;">
        <label for="id"><strong>Select Slider:</strong></label>
        <select name="id" onchange="this.form.submit()" style="padding: 5px 10px; margin-left: 10px;">
            <option value="">-- Select --</option>
            <?php while ($row = $sliderList->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= isset($slider_id) && $slider_id == $row['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($sliderData): ?>
    <form method="post" enctype="multipart/form-data" style="display: inline-block; padding: 20px; background: white; border: 1px solid #ccc; border-radius: 8px;">
        <input type="hidden" name="id" value="<?= $sliderData['id'] ?>">
        <div style="margin-bottom: 10px;">
            <label><strong>Title:</strong></label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($sliderData['title']) ?>" style="padding: 8px; width: 300px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label><strong>Link:</strong></label><br>
            <input type="text" name="link" value="<?= htmlspecialchars($sliderData['link']) ?>" style="padding: 8px; width: 300px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label><strong>Image:</strong></label><br>
            <?php if (!empty($sliderData['image'])): ?>
                <img src="<?= $sliderData['image'] ?>" style="max-width: 100px; display: block; margin: 10px auto;">
            <?php endif; ?>
            <input type="file" name="image" style="margin-top: 5px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>
                <input type="checkbox" name="status" value="1" <?= $sliderData['status'] ? 'checked' : '' ?>> Active
            </label>
        </div>
        <button type="submit" name="update_slider" style="padding: 10px 20px; background-color: green; color: white; border: none; border-radius: 5px;">Update Slider</button>
    </form>
    <?php endif; ?>

    <?= $message ?>
</body>
</html>
