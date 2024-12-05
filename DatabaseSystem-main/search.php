
<?php
session_start();
$authenticated = false;
if (isset($_SESSION['email'])) {
    $authenticated = true;
}

// Include the database connection settings
include 'db.php';

// Implement search functionality
$search_results = [];
$photos = [];

// Fetch photos for each restaurant and store them in an associative array
$photo_query = "SELECT restaurant_id, url FROM photos";
$photo_result = $conn->query($photo_query);
if ($photo_result->num_rows > 0) {
    while ($photo_row = $photo_result->fetch_assoc()) {
        $photos[$photo_row['restaurant_id']][] = $photo_row['url'];
    }
}

if (isset($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM restaurants WHERE name LIKE '%$search_term%' OR category LIKE '%$search_term%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Restaurants - Tastebuds</title>
    <link rel="stylesheet" href="./style.css">
    <style>
/* General container styling */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Section title styling */
.section-title {
    font-size: 24px;
    margin-bottom: 20px;
}

/* Results list container styling */
.results-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Individual result card styling */
.result-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    width: calc(33.333% - 20px); 
    display: flex;
    flex-direction: column;
}

.result-card:hover {
    transform: translateY(-5px);
}

/* Card image styling */
.card-image {
    background-size: cover;
    background-position: center;
    height: 150px;
    width: 100%;
}

/* Card content styling */
.card-content {
    padding: 15px;
}

.card-content h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 20px;
}

.card-content p {
    margin: 5px 0;
    color: #555;
}

/* Details link styling */
.details-link {
    display: inline-block;
    margin-top: 10px;
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s;
}

.details-link:hover {
    color: #0056b3;
}

/* No results message styling */
.no-results {
    font-size: 18px;
    color: #999;
}
</style>
</head>


<body>

    <!-- Header -->
    <header class="header section" data-header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="./picture/logo.svg" width="100" height="40" alt="Tastebuds logo">
            </a>
            <nav class="navbar" data-navbar>
                <ul class="navbar-list">
                    <li class="navbar-item"><a href="index.php" class="navbar-link hover:underline" data-nav-link>Home</a></li>
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
                <?php if ($authenticated): ?>
                    <li class="navbar-item dropdown">
                        <a href="#" class="navbar-link hover:underline" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= htmlspecialchars($_SESSION['first_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="/logout.php" class="dropdown-item">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <a href="./loginpage.php" class="btn">Log in</a>
                    <a href="./register.php" class="btn">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Search Section -->
    <section class="section search" aria-label="search restaurants">
        <div class="container">
            <h2 class="h2 section-title">Search for Restaurants</h2>
            <form id="searchForm" class="search-form" method="GET" action="search.php">
                <input type="text" id="searchInput" name="search" placeholder="e.g. Gunshow" class="search-field">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>
    </section>

    <!-- Search Results Section -->
    <section class="section search-results" aria-label="search results">
        <div class="container">
            <h2 class="h2 section-title">Search Results</h2>
            <div class="results-list" id="resultsList">
                <?php if (!empty($search_results)): ?>
                    <?php foreach ($search_results as $restaurant): ?>
                        <?php
                        $restaurant_id = $restaurant['id']; 
                        $image_url = !empty($photos[$restaurant_id]) ? htmlspecialchars($photos[$restaurant_id][0]) : 'default_image.jpg';
                        ?>
                        <div class="result-card">
                            <div class="card-image" style="background-image: url('<?= $image_url ?>');"></div>
                            <div class="card-content">
                                <h3><?= htmlspecialchars($restaurant['name']) ?></h3>
                                <p>Category: <?= htmlspecialchars($restaurant['category']) ?></p>
                                <p>Rating: <?= number_format($restaurant['rating'], 1) ?></p>
                                <a href="restaurant_page.php?id=<?= $restaurant['id'] ?>" class="details-link">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">No results found.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->

    <script src="script.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
