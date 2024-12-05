<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$authenticated = false;
$isAdmin = false;

$reviewerEmail = '';
$user_restaurant_id = null; // Initialize the variable

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$database_users = "users";
$database_restaurant = "restaurant_info";

// Check authentication and admin status
if (isset($_SESSION['email'])) {
    $authenticated = true;
    $reviewerEmail = $_SESSION['email'];
    
    
    // Connect to the users database
    $conn_users = new mysqli($servername, $username, $password, $database_users);
    if ($conn_users->connect_error) {
        die("Connection failed: " . $conn_users->connect_error);
    }

    // Check if the user is an admin
    $admin_sql = "SELECT is_admin FROM users WHERE email = ?";
    if ($stmt = $conn_users->prepare($admin_sql)) {
        $stmt->bind_param('s', $reviewerEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $isAdmin = $user['is_admin'] == 1;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn_users->error;
    }
    $reviewerName = (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) ? htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']) : '';

    // Fetch the restaurant ID for the authenticated user
    $user_restaurant_sql = "SELECT restaurant_id FROM users WHERE email = ?";
    if ($stmt = $conn_users->prepare($user_restaurant_sql)) {
        $stmt->bind_param('s', $reviewerEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_restaurant_id = $user['restaurant_id'];
        } else {
            $user_restaurant_id = null;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn_users->error;
    }
    
    $conn_users->close();
}

// Connect to the restaurant_info database
$conn_restaurant = new mysqli($servername, $username, $password, $database_restaurant);
if ($conn_restaurant->connect_error) {
    die("Connection failed: " . $conn_restaurant->connect_error);
}

// Get restaurant ID from query string
$restaurant_id = isset($_GET['id']) ? intval($_GET['id']) : 4;

// Fetch restaurant details
$restaurant_sql = "SELECT name, website, rating FROM restaurants WHERE id = ?";
if ($stmt = $conn_restaurant->prepare($restaurant_sql)) {
    $stmt->bind_param('i', $restaurant_id);
    $stmt->execute();
    $restaurant_result = $stmt->get_result();
    $restaurant = $restaurant_result->fetch_assoc();
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn_restaurant->error;
}

// Fetch header photo
$header_photo_sql = "SELECT url FROM photos WHERE restaurant_id = ? ORDER BY id ASC LIMIT 1";
if ($stmt = $conn_restaurant->prepare($header_photo_sql)) {
    $stmt->bind_param('i', $restaurant_id);
    $stmt->execute();
    $header_photo_result = $stmt->get_result();
    $header_photo = $header_photo_result->fetch_assoc();
    $photo_url = isset($header_photo['url']) ? $header_photo['url'] : '';
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn_restaurant->error;
}

// Fetch photos
$photos_sql = "SELECT url FROM photos WHERE restaurant_id = ? AND id != 1";
if ($stmt = $conn_restaurant->prepare($photos_sql)) {
    $stmt->bind_param('i', $restaurant_id);
    $stmt->execute();
    $photos_result = $stmt->get_result();
    $photos = [];
    while ($photo = $photos_result->fetch_assoc()) {
        $photos[] = $photo['url'];
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn_restaurant->error;
}

// Fetch reviews and replies
$reviews_sql = "
    SELECT reviews.id AS review_id, reviews.reviewer_name, reviews.review_text, reviews.rating, reviews.email AS reviewer_email,
           replies.id AS reply_id, replies.reply_text, replies.created_at AS reply_created_at
    FROM reviews 
    LEFT JOIN replies ON reviews.id = replies.review_id
    WHERE reviews.restaurant_id = ? 
    ORDER BY reviews.id, replies.created_at ASC";
    
if ($stmt = $conn_restaurant->prepare($reviews_sql)) {
    $stmt->bind_param('i', $restaurant_id);
    if ($stmt->execute()) {
        $reviews_result = $stmt->get_result();
        $stmt->close();

        // Process reviews and replies
        $reviews_with_replies = [];
        while ($row = $reviews_result->fetch_assoc()) {
            $review_id = $row['review_id'];
            if (!isset($reviews_with_replies[$review_id])) {
                $reviews_with_replies[$review_id] = [
                    'review_id' => $row['review_id'],
                    'reviewer_name' => $row['reviewer_name'],
                    'review_text' => $row['review_text'],
                    'rating' => $row['rating'],
                    'reviewer_email' => $row['reviewer_email'],
                    'replies' => []
                ];
            }
            if ($row['reply_id']) {
                $reviews_with_replies[$review_id]['replies'][] = [
                    'reply_id' => $row['reply_id'],
                    'reply_text' => $row['reply_text'],
                    'reply_created_at' => $row['reply_created_at']
                ];
            }
        }
    } else {
        echo "Error executing statement: " . $stmt->error;
    }
} else {
    echo "Error preparing statement: " . $conn_restaurant->error;
}

$canReply = $authenticated && isset($user_restaurant_id) && $user_restaurant_id == $restaurant_id;




$conn_restaurant->close();
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo htmlspecialchars($restaurant['name']); ?></title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">
<!-- Google Fonts Link -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
    .button {
        display: inline-flex;
        align-items: center; /* Align icon and text vertically */
        padding: 8px 16px;
        border-radius: 20px; /* Adjust the value as needed */
        border: 1px solid #ddd; /* Light gray stroke */
        text-decoration: none;
        color: #333; /* Text color */
        font-weight: bold;
        transition: background-color 0.3s ease;
        margin-top: 5px;
    }
    .button:hover {
        background-color: #f0f0f0; /* Light gray background on hover */
    }
    .icon {
        width: 30px; /* Adjust width */
        height: 16px; /* Adjust height */
        margin-right: 8px; /* Space between icon and text */
        vertical-align: middle; /* Align the icon vertically */
    }
    body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
    }
    .header {
    padding: 10px 0; /* Adjust padding as needed */
    text-align: center;
    }
    .header-img {
    width: 100%;
    max-height: 200px; /* Adjust max-height as needed */
    object-fit: cover;
    }
    .header-text {
    text-align: center;
    margin-top: 20px; /* Adjust margin as needed */
    }
    .container {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    justify-content: space-between; /* Ensure items are spaced evenly */
    }
    .about {
    flex: 0 0 50%; /* About section takes up more space */
    background-color: #f0f0f0;
    padding: 20px;
    border-radius: 8px;
    margin-right: 20px;
    }
    .reviews {
    flex: 0 0 50%; /* Reviews section takes up less space */
    background-color: #f0f0f0;
    padding: 20px;
    border-radius: 8px;
    }
    @media (max-width: 768px) {
    .container {
        flex-direction: column;
    }
    .about, .reviews {
        flex: 1 1 100%; /* Full width on smaller screens */
        margin-right: 0; /* Reset margin */
        margin-bottom: 20px; /* Space between sections */
    }
    }
    .review-card {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        display: flex;
        justify-content: space-between; /* Align items to the sides */
        align-items: center; /* Center items vertically */
    }
    .delete-button-container {
        display: flex;
        align-items: center;
    }

    @media (max-width: 768px) {
    .container {
        flex-direction: column;
    }
    .about, .reviews {
        flex: 1 1 100%;
        margin-right: 0;
        margin-bottom: 20px;
    }
    }
    /* Adjustments for star rating */
    .rating {
        display: flex; /* Ensure stars are displayed in a row */
        align-items: center; /* Align items vertically */
        font-size: 18px; /* Adjust font size of rating text */
        margin-top: 5px; /* Adjust spacing as needed */
    }
    .rating-number {
        margin-right: 5px;
    }
    .star {
        width: 20px; /* Adjust star size */
        height: auto; /* Maintain aspect ratio */
        margin-right: 5px; /* Adjust spacing between stars */
    }
    #add-review-form {
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
    }
    #add-review-form label {
        display: block;
        margin-top: 10px;
    }
    #add-review-form input[type="text"],
    #add-review-form textarea,
    #add-review-form select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    #add-review-form button {
        margin-top: 10px;
        padding: 10px 20px;
        border: none;
        border-radius: 20px;
        background-color: #E64042;
        color: #fff;
        cursor: pointer;
    }
    #add-review-form button:hover {
        background-color: #555;
    }
    .rating-button-container {
        display: flex;
        align-items: center; /* Align items vertically */
        justify-content: center; /* Center items horizontally */
    }
    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    /* Modal styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; 
        border-radius: 8px;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Responsive grid columns */
        gap: 10px; /* Gap between grid items */
        margin-top: 20px;
    }
    .photo-item {
        width: 240px; /* Set the width of the container */
        height: 200px; /* Set the height of the container */
        display: inline-block; /* Make sure the containers align horizontally */
    }
    .photo-item img {
        width: 100%; /* Ensure photos take full width of their containers */
        height: 100%; /* Ensure photos take full height of their containers */
        object-fit: cover; /* Cover the container and crop the excess */
        object-position: center; /* Center the image */
        display: block; /* Prevent extra space below images */
        border-radius: 8px;
    }
    .footer-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px; /* Adjust spacing if needed */
    }

    .footer-brand {
    margin-right: 20px; /* Space between logo and sections */
    }

    .footer-sections-wrapper {
    display: flex;
    flex: 1;
    align-items: flex-start; /* Aligns the sections and claim link at the top */
    justify-content: space-between; /* Distributes space between sections and the claim link */
    }

    .footer-sections {
    display: flex;
    gap: 20px; /* Space between the sections */
    }

    .claim-link-container {
    display: flex;
    align-items: flex-start; /* Aligns the link to the top */
    margin-left: 20px; /* Space from the left edge */
    }

    .claim-business-link {
    color: #007BFF; /* Blue color for the link */
    text-decoration: none;
    font-weight: bold;
    padding: 10px;
    }

    .claim-business-link:hover {
    text-decoration: underline;
    color: #0056b3; /* Darker blue on hover */
    }

    .footer-list {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    }

    .footer-list-item {
    margin: 0;
    padding: 0;
    }

    .footer-bottom {
    text-align: center;
    }

</style>
</head>
<body>
<!-- Navbar (sit on top) -->
<header class="header">
    <div class="container">
        <a href="index.php" class="logo">
            <img src="./picture/logo.svg" width="100" height="40" alt="Tastebuds logo">
        </a>
        <nav class="navbar">
            <ul class="navbar-list">
                <li class="navbar-item"><a href="index.php" class="navbar-link">Home</a></li>
            </ul>
        </nav>
        <div class="wrapper">
            <a href="search.php" class="search-btn" aria-label="search">
                <ion-icon name="search-outline" aria-hidden="true"></ion-icon>
                <span class="span">Search</span>
            </a>
            <button class="nav-toggle-btn" aria-label="toggle menu" data-nav-toggler>
                <span class="span one"></span>
                <span class="span two"></span>
                <span class="span three"></span>
            </button>


        <?php
        if ($authenticated) {
        ?>

        <li class="navbar-item dropdown">
            <a href="#" class="navbar-link hover:underline" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?= htmlspecialchars($_SESSION['first_name']) ?>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="logout.php" class="dropdown-item" >Logout</a></li>
        </ul>
        </li>

        <?php
        } else {
        ?>
        <a href="./loginpage.php" class="btn">Log in</a>
        <a href="./register.php" class="btn">Register</a>
        <?php
        }   
        ?>
      </div>
    </div>
</header>
<!-- Ionicons script -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<!-- Header Image -->
<div class="header">
    <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="header-img">
</div>
<!-- Restaurant Name and Rating -->
<div class="header-text">
    <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
    <div class="rating-button-container">
        <div class="rating">
            <span><?php echo number_format($restaurant['rating'], 1);?>&nbsp;</span>
            <?php
            $rating = floatval($restaurant['rating']); // Convert rating to float
            $full_stars = floor($rating); // Number of full stars
            $half_star = $rating - $full_stars; // Check if there's a half star
            // Loop to display stars
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $full_stars) {
                    echo '<img src="assets/vectors/star-filled.png" alt="Star filled" class="star">';
                } else if ($i == ceil($rating) && $half_star >= 0.5) {
                    echo '<img src="assets/vectors/star-half.png" alt="Half filled star" class="star">';
                } else {
                    echo '<img src="assets/vectors/star-empty.png" alt="Empty star" class="star">';
                }
            }
            ?>
        </div>
        <div>
            <a href="<?php echo htmlspecialchars($restaurant['website']); ?>" class="button">
                <img src="assets/vectors/link.png" alt="Link icon" class="icon">
                Website
            </a>
        </div>
    </div>
</div>
<div class="container">
    <!-- Photos Section -->
    <div class="about">
        <h2>Photos</h2>
        <div class="photo-grid">
            <?php if (count($photos) > 0): ?>
                <?php foreach ($photos as $photo_url): ?>
                    <div class="photo-item">
                        <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Restaurant Photo">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No photos available.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Reviews Section -->
    <!-- Reviews Section -->
    <div class="reviews">
        <div class="reviews-header">
            <h2> Reviews </h2>
            <?php if ($authenticated && !$isAdmin && (isset($user_restaurant_id)  != $restaurant_id)): ?>
                <button class="button" id="add-review-button" style="background-color: #EE4D47; color: white; border-radius: 5px; padding: 10px 20px; border: none; cursor: pointer;">Add Your Own</button>
            <?php endif; ?>
        </div>

        <?php if (!empty($reviews_with_replies)): ?>
            <?php foreach ($reviews_with_replies as $review): ?>
                <div class="review-card" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
                    <div class="review-details" style="grid-column: 1 / 2;">
                        <h3 style="margin: 0;"><?php echo htmlspecialchars($review['reviewer_name']); ?></h3>
                        <div class="rating" style="margin-top: 5px;">
                            <span class="rating-number"><?php echo htmlspecialchars($review['rating']); ?></span>
                            <?php
                                $rating = floatval($review['rating']); // Convert rating to float
                                $full_stars = floor($rating); // Number of full stars
                                $half_star = $rating - $full_stars; // Check if there's a half star
                                
                                // Loop to display stars
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $full_stars) {
                                        echo '<img src="assets/vectors/star-filled.png" alt="Star filled" class="star" style="width: 20px; height: 20px;">';
                                    } else if ($i == ceil($rating) && $half_star >= 0.5) {
                                        echo '<img src="assets/vectors/star-half.png" alt="Half filled star" class="star" style="width: 20px; height: 20px;">';
                                    } else {
                                        echo '<img src="assets/vectors/star-empty.png" alt="Empty star" class="star" style="width: 20px; height: 20px;">';
                                    }
                                }
                            ?>
                        </div>
                        <p style="margin-top: 10px;"><?php echo htmlspecialchars($review['review_text']); ?></p>
                    </div>

                    <div class="reply-section" style="grid-column: 2 / 3;">
                        <!-- Replies for the current review -->
                        <div class="replies" style="margin-bottom: 15px;">
                            <?php if (isset($review['replies']) && !empty($review['replies'])): ?>
                                <?php foreach ($review['replies'] as $reply): ?>
                                    <div class="reply" style="background-color: #f1f1f1; border-left: 3px solid #EE4D47; padding: 10px; margin-top: 10px; border-radius: 4px;">
                                        <strong><?php echo htmlspecialchars($restaurant['name']); ?>:</strong>
                                        <p><?php echo htmlspecialchars($reply['reply_text']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Reply form for authenticated users -->
                        <?php if ($authenticated && isset($user_restaurant_id) && $user_restaurant_id == $restaurant_id): ?>
                            <form method="post" action="submit_reply.php" class="reply-form" style="display: flex; flex-direction: column;">
                                <label for="reply_text" style="margin-bottom: 5px;">Reply:</label>
                                <textarea id="reply_text" name="reply_text" required style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 10px; resize: vertical;"></textarea>
                                <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['review_id']); ?>">
                                <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant_id); ?>">
                                <button type="submit" class="button" style="background-color: #EE4D47; color: white; border-radius: 5px; padding: 10px 20px; border: none; cursor: pointer;">Submit</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Conditional delete button -->
                    <?php if ($authenticated && ($review['reviewer_email'] === $reviewerEmail || $isAdmin)): ?>
                        <div class="delete-button-container" style="margin-top: 10px; grid-column: 1 / 3;">
                            <form action="delete_review.php" method="post" style="display: inline;">
                                <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['review_id']); ?>">
                                <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant_id); ?>">
                                <button type="submit" class="button" style="background-color: #E64042; color: white; border-radius: 5px; padding: 8px 16px; border: none; cursor: pointer;">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>
</div>



<!-- The Modal -->
<div id="reviewModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fff; margin: 15% auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; width: 80%; max-width: 500px;">
        <span class="close" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <form id="add-review-form" method="post" action="submit_review.php">
            <h3>Add Your Review</h3>
            <label for="reviewer_name">Name:</label>
            <input type="text" id="reviewer_name" name="reviewer_name" value="<?php echo htmlspecialchars($reviewerName); ?>" disabled>
            <label for="review_text">Comment:</label>
            <textarea id="review_text" name="review_text" required></textarea>
            <label for="rating">Rating:</label>
            <select id="rating" name="rating" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant_id); ?>">
            <button type="submit" class="button" style="background-color: #EE4D47; color: white; border-radius: 5px; padding: 10px 20px; border: none; cursor: pointer;">Submit</button>
        </form>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("reviewModal");

    // Get the button that opens the modal
    var btn = document.getElementById("add-review-button");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<!-- 
  - #FOOTER
-->

<footer class="footer">
  <div class="container">
    <div class="footer-top section">
      <div class="footer-brand">
        <a href="#" class="logo">
          <img src="./picture/logo.svg" width="129" height="40" alt="Blogy logo">
        </a>
        <p class="footer-text">
          The best review website for the greater Atlanta area.
        </p>
      </div>

      <!-- Wrapper for the three sections -->
      <div class="footer-sections-wrapper">
        <div class="footer-sections">
          <ul class="footer-list">
            <li>
              <p class="h5">Social</p>
            </li>
            <li class="footer-list-item">
              <ion-icon name="logo-facebook"></ion-icon>
              <a href="#" class="footer-link hover:underline">Facebook</a>
            </li>
            <li class="footer-list-item">
              <ion-icon name="logo-twitter"></ion-icon>
              <a href="#" class="footer-link hover:underline">Twitter</a>
            </li>
            <li class="footer-list-item">
              <ion-icon name="logo-pinterest"></ion-icon>
              <a href="#" class="footer-link hover:underline">Pinterest</a>
            </li>
            <li class="footer-list-item">
              <ion-icon name="logo-vimeo"></ion-icon>
              <a href="#" class="footer-link hover:underline">Vimeo</a>
            </li>
          </ul>

          <ul class="footer-list">
            <li>
              <p class="h5">About</p>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Style Guide</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Features</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Contact</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">404</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Privacy Policy</a>
            </li>
          </ul>

          <ul class="footer-list">
            <li>
              <p class="h5">Features</p>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Upcoming Events</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Blog & News</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Features</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">FAQ Question</a>
            </li>
            <li>
              <a href="#" class="footer-link hover:underline">Testimonial</a>
            </li>
          </ul>
        </div>

        <!-- "Claim this business" link -->
        <div class="claim-link-container">
        <a href="restaurantRegister.php?restaurant_id=<?= $restaurant_id ?>" style="color: blue;font-weight: 600">Claim this business</a>

        </div>

      </div>
    </div>

    
</footer>


</body>

</html>