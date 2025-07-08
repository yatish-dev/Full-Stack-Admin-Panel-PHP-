<?php
require_once('auth_check.php');


// Include DB connection
require_once('../db_config.php');


// Initialize variables
$user_id = $_GET['id'] ?? null;
$username = '';
$role = '';
$msg = '';

// Handle invalid user ID
if (!$user_id) {
    header("Location: manage_users.php");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $msg = "<div class='alert alert-danger'>User not found.</div>";
} else {
    $username = $user['username'];
    $role = $user['role'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_role = $_POST['role'];

    $update = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE user_id = ?");
    $update->bind_param("ssi", $new_username, $new_role, $user_id);

    if ($update->execute()) {
        $msg = "<div class='alert alert-success'>User updated successfully.</div>";
        $username = $new_username;
        $role = $new_role;
    } else {
        $msg = "<div class='alert alert-danger'>Failed to update user.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f4f4f4; padding: 30px;">
<div class="container bg-white p-4 rounded shadow" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Edit User</h2>

    <?php echo $msg; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Back</a>
        <a href="change_password.php?id=<?php echo $user_id; ?>" class="btn btn-warning">Change Password</a>
    
    </form>
</div>
</body>
</html>
