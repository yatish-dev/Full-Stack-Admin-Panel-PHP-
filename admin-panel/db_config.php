<?php
$host = "localhost";       // usually localhost
$username = "root";        // your MySQL username (default is 'root' in XAMPP)
$password = "";            // your MySQL password (empty by default in XAMPP)
$database = "majedar";     // your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
