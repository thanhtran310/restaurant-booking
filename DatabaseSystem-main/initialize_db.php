<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // MySQL default username
$password = ""; // MySQL default password (empty string if no password)

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS restaurant_info";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db("restaurant_info");

// Create tables and insert data
$sql = file_get_contents('restaurant_info.sql');
if ($conn->multi_query($sql) === TRUE) {
    echo "Tables created and data inserted successfully\n";
} else {
    echo "Error creating tables or inserting data: " . $conn->error;
}

$conn->close();
?>
