<?php
session_start();
$authenticated = false;
if (isset($_SESSION['email'])) {
  $authenticated = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 
    - primary meta tags
  -->
  <title>Tastebuds - Hey, we’re Tastebuds. See our thoughts, stories and restaurants.</title>
  <meta name="title" content="Tastebuds - Hey, we’re Tastebuds. See our thoughts, stories and restaurants.">
  <meta name="description" content="This is a restaurant review blog.">
  <!-- 
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <!-- 
    - favicon
  -->
  <link rel="shortcut icon" href="./picture/logo.svg" type="image/svg+xml">

  <!-- 
    - custom css link
  -->
  <link rel="stylesheet" href="./style.css">
  

  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">

</head>
<style>
.profile-container {
    margin-left: 20px; 
    margin-right: 20px; 
}


.profile-container {
    padding-left: 20px; 
    padding-right: 20px;
}
.table td, .table th {
    text-align: left;
}

.profile-container {
    margin-left: 20px;
    margin-right: 20px; 
}

.profile-container {
    padding-left: 20px; 
    padding-right: 20px; 
}

</style>

<body>

  <!-- 
    - #HEADER
  -->

  <header class="header section" data-header>
    <div class="container">

      <a href="index.php" class="logo">
        <img src="./picture/logo.svg" width="100" height="40" alt="Tastebuds logo">
      </a>

      <nav class="navbar" data-navbar>
        <ul class="navbar-list">

        <li class="navbar-item">
            <a href="index.php" class="navbar-link hover:underline" data-nav-link>Home</a>
        </li>

        

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
                <li><a class="dropdown-item" href="/profile.php">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="/logout.php" class="dropdown-item" >Logout</a></li>
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

</body>

</html>
