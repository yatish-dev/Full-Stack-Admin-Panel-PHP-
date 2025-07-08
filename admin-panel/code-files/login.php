<?php
// Include DB connection
require_once('../db_config.php');
// Initialize variables for error messages
$msg = '';

// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if already logged in
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query to fetch the user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the username exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start the session and set user details
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the admin panel or dashboard based on the role
            if ($user['role'] === 'Admin') {
                header("Location: admin_layout.php"); // Admin dashboard
                exit();
            } else {
                header("Location: index.php"); // Regular user home page
                exit();
            }
        } else {
            $msg = "<p style='color: red;'>Incorrect password.</p>";
        }
    } else {
        $msg = "<p style='color: red;'>Username not found.</p>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 50px;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #218838;
        }
        .message {
            text-align: center;
            font-size: 14px;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 120px;
            object-fit: contain;
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            .container {
                padding: 40px;
                width: 100%;
                max-width: 480px; /* Increased container width for mobile */
            }
            .logo {
                max-width: 150px; /* Increased logo size for mobile */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/logo.png" alt="Logo" class="logo">
        <h2>Login<br>username:yatish<br>password:yatish</h2>

        <?php echo $msg; ?>

        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
