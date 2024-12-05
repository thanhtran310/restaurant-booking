    <?php
// Database connection
$conn = mysqli_connect("localhost", "root", "","myDB");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//$sql = "CREATE DATABASE myDB";
//if ($conn->query($sql) === TRUE) {
//    echo "Database created successfully";
//} else {
//    echo "Error creating database: " . $conn->error;
//}
$sql_create_table = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
)";

if (mysqli_query($conn, $sql_create_table)) {
    echo "Table employees created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}
$username = $_POST['username'];
$password = md5($_POST['password']); // Hash the password

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

if (mysqli_query($conn, $sql)) {
    // Redirect to login page after successful sign up
    header("Location: loginpage.php");
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
