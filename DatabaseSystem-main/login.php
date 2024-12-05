<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "myDB");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
//    $sql = "CREATE DATABASE myDB";
//    if ($conn->query($sql) === TRUE) {
//        echo "Database created successfully";
//    } else {
//        echo "Error creating database: " . $conn->error;
//    }

    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        // Authentication successful
        $_SESSION['authenticated'] = true;
        header("Location:https://lazymofo.pcmad.ro/?"); //need to change to homepage (logged in)
    } else {
        echo "<p><strong>Incorrect Login</strong></p>";
?>
        <html>
        <a href="./register.php">Signup</a> <br>
        <a href="./loginpage.php">Login</a>
        </html>

        <?php //header("Location: index.php");
    }

    mysqli_close($conn);
}
?>
