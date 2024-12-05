<?php
session_start();

$authenticated = false;
if (isset($_SESSION['email'])) {
    $authenticated = true;
}

if ($authenticated) {
    header("Location: index.php");
    exit;
}

$business_name = "";
$email = "";
$phone = "";
$address = "";
$first_name = "";
$last_name = "";

// Initialize errors
$business_name_error = "";
$email_error = "";
$phone_error = "";
$address_error = "";
$first_name_error = "";
$last_name_error = "";
$password_error = "";
$confirm_password_error = "";

$error = false;

// Fetch restaurant name if restaurant_id is set
if (isset($_GET['restaurant_id'])) {
    $restaurant_id = intval($_GET['restaurant_id']);

    // Connect to restaurant_info database
    include 'tools/db_restaurant_info.php';
    $dbConnection = getDatabaseConnectionRestaurantInfo();

    // Fetch restaurant name
    $statement = $dbConnection->prepare("SELECT name FROM restaurants WHERE id = ?");
    if ($statement === false) {
        die('Failed to prepare SQL statement: ' . $dbConnection->error);
    }
    $statement->bind_param("i", $restaurant_id);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $business_name = htmlspecialchars($row['name']);
    } else {
        // Handle case where restaurant is not found
        $business_name_error = "Restaurant not found.";
    }
    $statement->close();
} else {
    // Handle missing restaurant_id
    $business_name_error = "No restaurant_id provided.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = $_POST['business_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($business_name)) {
        $business_name_error = "Business name is required";
        $error = true;
    }

    if (empty($first_name)) {
        $first_name_error = "First name is required";
        $error = true;
    }

    if (empty($last_name)) {
        $last_name_error = "Last name is required";
        $error = true;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Email is required";
        $error = true;
    }

    // Connect to users database
    include 'tools/db_users.php';
    $dbConnection = getDatabaseConnectionUsers();

    $statement = $dbConnection->prepare("SELECT id FROM users WHERE email = ?");
    if ($statement === false) {
        die('Failed to prepare SQL statement: ' . $dbConnection->error);
    }
    $statement->bind_param("s", $email);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows > 0) {
        $email_error = "Email is already used";
        $error = true;
    }
    $statement->close();

    if (!preg_match("/^(\+|00\d{1,3})?[- ]?\d{7,12}$/", $phone)) {
        $phone_error = "Phone format is not valid";
        $error = true;
    }
    if (strlen($password) < 6) {
        $password_error = "Password must be at least 6 characters";
        $error = true;
    }
    if ($password != $confirm_password) {
        $confirm_password_error = "Password does not match";
        $error = true;
    }
    if (!$error) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');
        $role = "restaurant"; // Assuming role is 'restaurant'
        $is_admin = 0; // Assuming this is not an admin
        $statement = $dbConnection->prepare("INSERT INTO users (first_name, last_name, email, phone, address, password, role, created_at, is_admin, restaurant_id) VALUES (?,?,?,?,?,?,?,?,?,?)");
        if ($statement === false) {
            die('Failed to prepare SQL statement: ' . $dbConnection->error);
        }
        $statement->bind_param("sssssssiis", $first_name, $last_name, $email, $phone, $address, $password, $role, $created_at, $is_admin, $restaurant_id);
        $statement->execute();
        $insert_id = $dbConnection->insert_id;
        $statement->close();

        // Set session variables
        $_SESSION['user_id'] = $insert_id;
        $_SESSION['business_name'] = $business_name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        $_SESSION['address'] = $address;
        $_SESSION['first_name'] = $first_name; // Set first name
        $_SESSION['last_name'] = $last_name;   // Set last name
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
    <title>Create a Business Account</title>
    <link rel="stylesheet" href="loginstyle/style1.css">
</head>
<body>
    <header>
        <h1>Create a Business Account</h1>
        <a href="index.php" class="logo">
            <img src="./picture/logo.svg" width="100" height="40" alt="Tastebuds logo">
        </a>
    </header>
    
    <div class="container">
        <div class="card">
            <form method="post">
                <div class="form-group">
                    <label for="business_name">Business name:</label>
                    <input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars($business_name) ?>" required>
                    <span class="text-danger"><?= $business_name_error ?></span>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
                    <span class="text-danger"><?= $first_name_error ?></span>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
                    <span class="text-danger"><?= $last_name_error ?></span>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    <span class="text-danger"><?= $email_error ?></span>
                </div>

                <div class="form-group">
                    <label for="phone">Phone number:</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
                    <span class="text-danger"><?= $phone_error ?></span>
                </div>

                <div class="form-group">
                    <label for="address">Business Address:</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($address) ?>" required>
                    <span class="text-danger"><?= $address_error ?></span>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="text-danger"><?= $password_error ?></span>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="text-danger"><?= $confirm_password_error ?></span>
                </div>

                <div class="col-sm-4 d-grid">
                    <button type="submit" class="btn">Sign Up</button>
                    <a href="./index.php" class="btn btn-outline-primary">Cancel</a>
                </div>
            </form>
            <p>Already have an account? <a href="restaurantLogin.php">Log in</a></p>
            <p><a href="register.php">Create a Personal Account</a></p>
        </div>
    </div>
</body>
</html>
