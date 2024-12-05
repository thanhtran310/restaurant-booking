<?php
session_start();
$authenticated = false;

if (isset($_SESSION['email'])) {
    $authenticated = true;
}

if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$email = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] =='POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required";
    } 
    else {
        include 'tools/db.php';
        $dbConnection = getDatabaseConnection();

        $statement = $dbConnection->prepare("SELECT id, first_name, last_name, phone, address, password, created_at FROM users WHERE email = ?");
        $statement->bind_param("s", $email);
        $statement->execute();

        $statement->bind_result($id, $first_name, $last_name, $phone, $address, $stored_password, $created_at);

        if($statement->fetch()) {
            if (password_verify($password, $stored_password)){
                $_SESSION['id'] = $id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['email'] = $email;
                $_SESSION['phone'] = $phone;
                $_SESSION['address'] = $address;
                $_SESSION['created_at'] = $created_at;
                header("Location: index.php");
                exit;
            }
        }
        $statement->close();
        $error = "Invalid email or password";
        
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Login</title>
    <link rel="stylesheet" href="loginstyle/style2.css">
</head>
<header>
    <h1>Business Log In</h1>
        <a href="index.php" class="logo">
            <img src=".\picture\logo.svg" width="100" height="40" alt="Tastebuds logo">
        </a>
</header>
<body>

<div class="container">
    <div class="card">
        <!--action="login.php"-->
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role ="alert">
                <strong><?= $error ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        
        <?php } ?>
        <form  method="post">
            <div class="form-group">
                <label for="username">Email:</label>
                <input type="text" id="email" name="email" value="<?= $email ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <a href="./index.php" class="btn btn-outline-primary">Cancel</a>
        </form>
        <p>Don't have an account? <a href="restaurantRegister.php">Claim your business</a></p>
        <p><a href="loginpage.php">Login as User</a></p>
    </div>
</div>
</body>
</html>
<?php
?>
