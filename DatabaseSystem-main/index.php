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
  <title>Search Restaurants - Tastebuds</title>
  <meta name="title" content="Tastebuds - Hey, we’re Tastebuds. See our thoughts, stories and restaurants.">
  <meta name="description" content="This is a restaurant review blog.">

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

  <!-- 
    - #SEARCH BAR
  -->

  <div class="search-bar" data-search-bar>

    <div class="input-wrapper">
      <input type="search" name="search" placeholder="Search" class="input-field">

      <button class="search-close-btn" aria-label="close search bar" data-search-toggler>
        <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
      </button>

    </div>

    <p class="search-bar-text">Please enter at least 3 characters</p>

  </div>

  <div class="overlay" data-overlay data-search-toggler></div>





  <main>
    <article>

      <!-- 
        - #HERO
      -->

      <section class="section hero" aria-label="home">
        <div class="container">

          <h1 class="h1 hero-title">
            <strong class="strong">Hey, we’re Tastebuds.</strong> See our thoughts and restaurants!
          </h1>

          <div class="wrapper">

            <form action="" class="newsletter-form">
              <input type="email" name="email_address" placeholder="Your email address" class="email-field">

              <button type="submit" class="btn">Register</button>
            </form>

            <p class="newsletter-text">
              Join the reviews communtity.
            </p>

          </div>

        </div>
      </section>





      <!-- 
        - #FEATURED POST
      -->

      <section class="section featured" aria-label="featured post">
        <div class="container">

          <p class="section-subtitle">
            Get started with our <strong class="strong">best restaurants</strong>
          </p>

          <ul class="has-scrollbar">

            <li class="scrollbar-item">
              <div class="blog-card">

                <figure class="card-banner img-holder" style="--width: 500; --height: 600;">
                  <img src="./picture/Gunshow.jpg" width="500" height="600" loading="lazy"
                    alt="It’s a new era in design, there are no rules" class="img-cover">

                  
                </figure>

                <div class="card-content">
                  <ul class="card-meta-list">
                      <li>
                          <a href="#" class="card-tag">Creative</a>
                      </li>
                      <li>
                          <a href="#" class="card-tag">Industrial</a>
                      </li>
                  </ul>
                  <h3 class="h4">
                      <a href="restaurant_page.php?id=2" class="card-title hover:underline">
                          Gunshow
                      </a>
                  </h3>
                  <p class="card-text">
                      The fluorescent lights are bright, the hard-rock music is loud, and the kitchen is so in-your-face that you can see right into the walk-in cooler.
                  </p>
              </div>
              
            </li>

            <li class="scrollbar-item">
              <div class="blog-card">

                <figure class="card-banner img-holder" style="--width: 500; --height: 600;">
                  <img src="./picture/boccalupo.jpg" width="500" height="600" loading="lazy"
                    alt="Perfection has to do with the end product" class="img-cover">

                  
                </figure>

                <div class="card-content">

                  <ul class="card-meta-list">

                    <li>
                      <a href="#" class="card-tag">Origin</a>
                    </li>

                    <li>
                      <a href="#" class="card-tag">Italian</a>
                    </li>

                    <li>
                      <a href="#" class="card-tag">Pasta</a>
                    </li>

                  </ul>

                  <h3 class="h4">
                    <a href="restaurant_page.php?id=4" class="card-title hover:underline">
                        BoccaLupo
                    </a>
                </h3>

                  <p class="card-text">
                    At BoccaLupo, the chef-owner shows why The New York Times hailed him the fief of his own “neighborhood pasta kingdom.”
                  </p>

                </div>

              </div>
            </li>

            <li class="scrollbar-item">
              <div class="blog-card">

                <figure class="card-banner img-holder" style="--width: 500; --height: 600;">
                  <img src="./picture/chaiyo.jpg" width="500" height="600" loading="lazy"
                    alt="Everyone has a different life story" class="img-cover">

                  
                </figure>

                <div class="card-content">

                  <ul class="card-meta-list">

                    <li>
                      <a href="#" class="card-tag">Thai</a>
                    </li>

                    <li>
                      <a href="#" class="card-tag">Fine Dining</a>
                    </li>

                  </ul>

                  <h3 class="h4">
                    <a href="restaurant_page.php?id=1" class="card-title hover:underline">
                        Chai Yo Modern Thai
                    </a>
                  </h3>

                  <p class="card-text">
                    Contemporary takes on Thai classics, created & served up in an elegant, mod setting with full bar.
                  </p>

                </div>

              </div>
            </li>

            <li class="scrollbar-item">
              <div class="blog-card">
              <a href="restaurant_page.php">


                <figure class="card-banner img-holder" style="--width: 500; --height: 600;">
                  <img src="./picture/umi.jpg" width="500" height="600" loading="lazy"
                    alt="The difference is quality" class="img-cover">

                  
                </figure>

                <div class="card-content">

                  <ul class="card-meta-list">



                    <li>
                      <a href="#" class="card-tag">Japanese</a>
                    </li>

                    <li>
                      <a href="#" class="card-tag">Fine Dining</a>
                    </li>

                  </ul>

                  <h3 class="h4">
                    <a href="restaurant_page.php?id=5" class="card-title hover:underline">
                        Umi
                    </a>
                  </h3>

                  <p class="card-text">
                    At Umi, a see-and-be-scene hot spot in Buckhead, the staff flies its seafood in from all over the world to create clean, crisp dishes. 
                  </p>

                </div>
                </a>

              </div>
            </li>

            <li class="scrollbar-item">
              <div class="blog-card">

                <figure class="card-banner img-holder" style="--width: 500; --height: 600;">
                  <img src="./picture/Taqueria.jpg" width="500" height="600" loading="lazy"
                    alt="Problems are not stop signs, they are guidelines" class="img-cover">

                  
                </figure>

                <div class="card-content">

                  <ul class="card-meta-list">

                    <li>
                      <a href="#" class="card-tag">Mexican</a>
                    </li>

                    <li>
                      <a href="#" class="card-tag">Casual</a>
                    </li>

                  </ul>

                  <h3 class="h4">
                    <a href="restaurant_page.php?id=3" class="card-title hover:underline">
                        Taqueria del Sol
                    </a>
                  </h3>

                  <p class="card-text">
                    People spill out of Taqueria del Sol’s Westside restaurant all day long.
                  </p>

                </div>

              </div>
            </li>

          </ul>

        </div>
      </section>






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

      <div class="section footer-bottom">


      </div>

    </div>
  </footer>




  <!-- 
    - custom js link
  -->
  <script src="./assets/js/script.js" defer></script>

  <!-- 
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const navToggler = document.querySelector("[data-nav-toggler]");
    const navbar = document.querySelector("[data-navbar]");

    navToggler.addEventListener("click", function () {
      navbar.classList.toggle("active");
      navToggler.classList.toggle("active");
    });

    document.addEventListener("click", function(event) {
      if (!navbar.contains(event.target) && !navToggler.contains(event.target)) {
        navbar.classList.remove("active");
        navToggler.classList.remove("active");
      }
    });
  });
</script>
</body>

</html>
