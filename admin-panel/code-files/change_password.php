<?php
require_once('../db_config.php');
require_once('auth_check.php');
$msg = '';

// Get logged-in username from session
$username = $_SESSION['username'] ?? null;

if (!$username) {
    die("<p style='color: red;'>User not logged in. Please log in to change password.</p>");
}

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Fetch user from database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        $msg = "<p style='color: red;'>User not found in database.</p>";
    } else {
        $user = $result->fetch_assoc();
        
        if (!isset($user['password'])) {
            $msg = "<p style='color: red;'>Password field missing in database result.</p>";
        } elseif (password_verify($old_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $updateStmt->bind_param("ss", $new_password_hash, $username);

                if ($updateStmt->execute()) {
                    $msg = "<p style='color: green;'>Password updated successfully!</p>";
                } else {
                    $msg = "<p style='color: red;'>Error updating password: " . $updateStmt->error . "</p>";
                }
            } else {
                $msg = "<p style='color: red;'>New password and confirmation do not match.</p>";
            }
        } else {
            $msg = "<p style='color: red;'>Old password is incorrect.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 30px;">
    <div style="background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto;">
        <h2 style="text-align: center;">Change Password</h2>
        
        <?php echo $msg; ?>
        
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label>Old Password:</label><br>
                <input type="password" name="old_password" style="width: 100%; padding: 8px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label>New Password:</label><br>
                <input type="password" name="new_password" style="width: 100%; padding: 8px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Confirm New Password:</label><br>
                <input type="password" name="confirm_password" style="width: 100%; padding: 8px;" required>
            </div>
            <button type="submit" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px;">Update Password</button>
        </form>
    </div>
</body>
</html>
