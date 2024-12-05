<?php
session_start();
if (isset($_POST['reply_text']) && isset($_POST['review_id']) && isset($_POST['restaurant_id']) && isset($_SESSION['email'])) {
    // Database connection settings
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "restaurant_info";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the form data
    $reply_text = $_POST['reply_text'];
    $review_id = intval($_POST['review_id']);
    $restaurant_id = intval($_POST['restaurant_id']); // Get restaurant_id from POST data
    $user_email = $_SESSION['email'];

    // Fetch the user_id and associated restaurant_id from the users database
    $conn_users = new mysqli("localhost", "root", "", "users");
    if ($conn_users->connect_error) {
        die("Connection failed: " . $conn_users->connect_error);
    }
    
    $user_sql = "SELECT id, restaurant_id FROM users WHERE email = ?";
    if ($stmt = $conn_users->prepare($user_sql)) {
        $stmt->bind_param('s', $user_email);
        $stmt->execute();
        $user_result = $stmt->get_result();
        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $user_id = $user['id'];
            $user_restaurant_id = $user['restaurant_id'];
            
            // Check if the user is associated with the same restaurant
            if ($user_restaurant_id != $restaurant_id) {
                echo "You are not authorized to reply to this restaurant.";
                $stmt->close();
                $conn_users->close();
                $conn->close();
                exit();
            }
        } else {
            echo "User not found.";
            $stmt->close();
            $conn_users->close();
            $conn->close();
            exit();
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn_users->error;
        $conn_users->close();
        $conn->close();
        exit();
    }
    $conn_users->close();

    // Insert the reply into the replies table
    $sql = "INSERT INTO replies (review_id, user_id, reply_text, restaurant_id) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('iiss', $review_id, $user_id, $reply_text, $restaurant_id);
        if ($stmt->execute()) {
            // Redirect back to the restaurant page
            header("Location: restaurant_page.php?id=" . $restaurant_id);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
