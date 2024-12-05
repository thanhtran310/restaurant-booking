<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // MySQL default username
$password = ""; // MySQL default password (empty string if no password)
$database = "restaurant_info"; // The database name to use

// Create MySQL connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
