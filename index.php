<?php
include "db_connect.php";
include "filter_logic.php";
session_start();

if (isset($_SESSION['login_message'])) {
    $alertType = $_SESSION['alert_type'] ?? 'info';
    echo "
    <div class='container'>
        <div class='alert alert-$alertType alert-dismissible fade in' style='background-color:#7838de; padding: 5px;'>
            <a href='#' class='close' data-dismiss='alert' aria-label='close' style='font-size: 20px;'>&times;</a>
            <strong>" . htmlspecialchars($_SESSION['login_message']) . "</strong>
        </div>
    </div>";
    unset($_SESSION['login_message'], $_SESSION['alert_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WeekndWIK</title>

    <!-- Fonts and Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles.css" />

    <!-- jQuery and Bootstrap (for alerts) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="topnav">
        <div class="logo">WeekndWIK</div>

        <div class="nav-center" id="navCenter">
            <a href="index.php" class="active">HOME</a>
            <a href="album.php">ALBUM</a>
            <a href="song.php">SONG</a>
            <a href="favourite.php">MY FAV</a>
        </div>

        <div class="nav-right" id="navRight">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user.php" class="nav-icon">
                    <span class="material-symbols-outlined">account_circle</span>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-button">LOGIN</a>
                <a href="signup.php" class="nav-button">SIGN UP</a>
            <?php endif; ?>
        </div>

        <div class="hamburger" onclick="toggleNav()">☰</div>
    </nav>

    <div class="image-container">
      <div class="left-column">
        <h1 class="cover">THE<br>WEEKND</h1>
        <p class="description">
          Discover the dark, genre-blending universe of The Weeknd. From haunting R&B to chart-topping pop, dive into his discography and share your thoughts.
        </p>
        <div class="buttons">
            <a href="https://www.youtube.com/channel/UC0WP5P-ufpRfjbNrmOWwLBQ" class="btn"><i class="fa fa-youtube-play" aria-hidden="true" style="font-size:24px"></i></a>
            <a href="https://www.instagram.com/theweeknd/?hl=en" class="btn"><i class="fa fa-instagram" aria-hidden="true" style="font-size:24px"></i></a>
            <a href="https://open.spotify.com/artist/1Xyo4u8uXC1ZmMpatF05PJ" class="btn"><i class="fa fa-spotify" aria-hidden="true" style="font-size:24px"></i></a>
            <a href="https://x.com/theweeknd" class="btn"><i class="fa fa-twitter" aria-hidden="true" style="font-size:24px"></i></a>
        </div>
      </div>
      <div class="right-column">
        <img src="theWeekndCover.jpg" alt="Cover" />
      </div>
    </div>


    <!-- Random Songs Section -->
    <div class="trending-container">
        <div class="grid-container">
            <?php include "random_display.php"; ?>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        function toggleNav() {
            document.getElementById('navCenter').classList.toggle('show');
            document.getElementById('navRight').classList.toggle('show');
        }
    </script>
</body>
</html>
