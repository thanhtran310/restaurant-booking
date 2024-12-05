<?php
session_start();
$authenticated = false;
if (isset($_SESSION['email'])) {
    $authenticated = true;
}

if (isset($_SESSION['email'])) {
    header("Location: /index.php");
    exit;
}


$first_name ="";
$last_name ="";
$email ="";
$phone ="";
$address ="";

$first_name_error ="";
$last_name_error ="";
$email_error ="";
$phone_error="";
$address_error = "";
$password_error ="";
$confirm_password_error ="";

$error = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    if(empty($first_name)){
        $first_name_error = "First name is required";
        $error = true;
    }
    if(empty($last_name)){
        $last_name_error = "Last name is required";
        $error = true;
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_error = "Email is required";
        $error = true;
    }
    include 'tools/db.php';
    $dbConnection = getDatabaseConnection();

    $statement = $dbConnection->prepare("SELECT id FROM users WHERE email = ?");
    $statement->bind_param("s", $email);
    $statement->execute();

    $result = $statement->get_result();
    if($result->num_rows > 0){
        $email_error = "Email is already used";
        $error = true;
    }
    $statement->close();



    if(!preg_match("/^(\+|00\d{1,3})?[- ]?\d{7,12}$/",$phone)){
        $phone_error = "Phone format is not valid";
        $error = true;
    }
    if(strlen($password) < 6){
        $password_error = "Password must be at least 6 characters";
        $error = true;
    }
    if($password != $confirm_password){
        $confirm_password_error = "Password does not match";
        $error = true;
    }
    if(!$error){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');
        $statement = $dbConnection->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password, created_at) VALUES (?,?,?,?,?,?,?)");
        $statement->bind_param("sssssss", $first_name, $last_name, $email, $phone, $address, $password, $created_at);
        $statement->execute();
        $insert_id = $dbConnection->insert_id;
        $statement->close();


        $_SESSION['user_id'] = $insert_id;
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="loginstyle/style1.css">
</head>
<header>
    <h1>Sign Up</h1>
        <a href="index.php" class="logo">
            <img src=".\picture\logo.svg" width="100" height="40" alt="Tastebuds logo">
        </a>
</header>
<body>

<div class="container">
    <div class="card">
    <!--action="signup_process.php"-->
        <form  method="post">
            <div class="form-group">
                <label for="first_name">First name:</label>
                <input type="text" id="first_name" name="first_name" value="<?= $first_name ?>" required>
                <span class="text-danger"><?= $first_name_error ?></span>
            </div>

            <div class="form-group">
                <label for="username">Last name:</label>
                <input type="text" id="last_name" name="last_name" value="<?= $last_name ?>" required>
                <span class="text-danger"><?= $last_name_error ?></span>
            </div>
            <div class="form-group">
                <label for="username">Email:</label>
                <input type="text" id="email" name="email" value="<?= $email ?>" required>
                <span class="text-danger"><?= $email_error ?></span>
            </div>

            <div class="form-group">
                <label for="username">Phone number:</label>
                <input type="text" id="phone" name="phone" value="<?= $phone ?>" required>
                <span class="text-danger"><?= $phone_error ?></span>
            </div>

            <div class="form-group">
                <label for="username">Address:</label>
                <input type="text" id="address" name="address" value="<?= $address ?>" required>
                <span class="text-danger"><?= $address_error ?></span>
            </div>
            <div class="form-group">
                <label for="username">Password:</label>
                <input type="password" id="password" name="password" required>
                <span class="text-danger"><?= $password_error ?></span>
            </div>
            <div class="form-group">
                <label for="password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="text-danger"><?= $confirm_password_error ?></span>
            </div>
            
                    <div class="col-sm-4 d-grid">
                        <button type="submit" class="btn">Sign Up</button>
                        <a href="./index.php" class="btn btn-outline-primary">Cancel</a>
                    </div>
        </form>
        <p>Already have an account? <a href="loginpage.php">Log in</a></p>
    </div>
</div>
</body>
</html>
<?php

?>