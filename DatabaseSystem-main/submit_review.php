<?php
session_start();

// Check if user is authenticated
$authenticated = isset($_SESSION['email']);

if (!$authenticated) {
    // Redirect or handle unauthorized access
    header("Location: login.php");
    exit;
}

// Retrieve form data
$reviewerFirstName = isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : '';
$reviewerLastName = isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : '';
$reviewerEmail = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '';
$reviewText = isset($_POST['review_text']) ? $_POST['review_text'] : '';
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$restaurantId = isset($_POST['restaurant_id']) ? intval($_POST['restaurant_id']) : 0;

// Combine first and last name into reviewer_name
$reviewerName = trim($reviewerFirstName . ' ' . $reviewerLastName);

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

// Insert new review into the database
$insertSql = "INSERT INTO reviews (reviewer_name, review_text, rating, restaurant_id, email) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("ssiss", $reviewerName, $reviewText, $rating, $restaurantId, $reviewerEmail);

if ($stmt->execute()) {
    // Review added successfully, update restaurant rating
    updateRestaurantRating($conn, $restaurantId);
    header("Location: restaurant_page.php?id=" . $restaurantId);
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Function to update restaurant rating
function updateRestaurantRating($conn, $restaurantId) {
    // Calculate new average rating
    $avgRatingSql = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE restaurant_id = ?";
    $avgStmt = $conn->prepare($avgRatingSql);
    $avgStmt->bind_param("i", $restaurantId);
    $avgStmt->execute();
    $result = $avgStmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $avgRating = $row['avg_rating'];

        // Update restaurant table with new average rating
        $updateSql = "UPDATE restaurants SET rating = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("di", $avgRating, $restaurantId);
        $updateStmt->execute();

        // Close statements
        $updateStmt->close();
    }

    // Close statement and connection
    $avgStmt->close();
}
?>
