<?php
function getDatabaseConnection()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "users";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);
    if($connection->connect_error)
    {
        die("Failed to connect: " . $connection->connect_error);
    }
    return $connection;
}
?>
    