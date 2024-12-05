<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: loginpage.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
    $restaurant_id = isset($_POST['restaurant_id']) ? intval($_POST['restaurant_id']) : 0;
    $email = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '';

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

    if ($is_admin) {
        // Admin can delete any review, no need to check ownership
        $delete_sql = "DELETE FROM reviews WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param('i', $review_id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            echo "Review deleted successfully.";
        } else {
            echo "Review not found.";
        }
    } 
    else {
        // Check if the review belongs to the user
        $check_sql = "SELECT email FROM reviews WHERE id = ? AND email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param('is', $review_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Delete the review
            $delete_sql = "DELETE FROM reviews WHERE id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param('i', $review_id);
            $stmt->execute();
        }
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the restaurant page
    header("Location: restaurant_page.php?id=" . $restaurant_id);
    exit();
}
?>
