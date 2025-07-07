<?php
session_start();
include "db_connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WeekndWIK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Styles -->
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- Navigation -->
<nav class="topnav">
    <div class="logo">WeekndWIK</div>

    <div class="nav-center" id="navCenter">
        <a href="index.php">HOME</a>
        <a href="album.php">ALBUM</a>
        <a href="song.php" class="active">SONG</a>
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

<!-- Search Bar -->
<div class="search-container" style="position: relative;">
    <span class="material-symbols-outlined search-icon" style="color: gray">search</span>
    <form method="GET" action="album.php" style="display: flex; width: 100%;" required>
        <input id="search" class="search-input" type="text" name="search" placeholder="Search album..." style="margin: 0; border: 0;">
        <div id="results"></div>
        <button type="button" class="filter-button" onclick="openFilterPopup()">
            <span class="material-symbols-outlined">instant_mix</span>
        </button>
        <button type="submit" class="search-button">
            <span class="material-symbols-outlined">search</span>
        </button>
    </form>
</div>

<!-- Filter Modal -->
<div id="filterPopup" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFilterPopup()">&times;</span>
        <h2>Filter by</h2>
        <form method="GET" action="album.php">
            <div>
                <h3>Year</h3>
                <?php foreach ([2012, 2013, 2015, 2016, 2018, 2020, 2022] as $year): ?>
                    <label><input type="checkbox" name="year[]" value="<?= $year ?>"> <?= $year ?></label><br>
                <?php endforeach; ?>
            </div>
            <button type="submit">Apply Filters</button>
        </form>
    </div>
</div>

<!-- Song Display Logic -->
<?php
if (!empty($_GET['search'])) {
    include 'search_logic.php';
} elseif (isset($_GET['genre']) || isset($_GET['year'])) {
    include 'filter_logic.php';
} else {
    echo '
        <table class="songs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Album</th>
                    <th></th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>';
    include 'songtable_display.php';
    echo '</tbody></table>';
}
?>

<!-- JavaScript -->
<script>
    function toggleNav() {
        document.getElementById('navCenter').classList.toggle('show');
        document.getElementById('navRight').classList.toggle('show');
    }

    function openFilterPopup() {
        document.getElementById("filterPopup").style.display = "block";
    }

    function closeFilterPopup() {
        document.getElementById("filterPopup").style.display = "none";
    }

    window.onclick = function (event) {
        const modal = document.getElementById("filterPopup");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    $(document).ready(function () {
        $("#search").on("keyup", function () {
            const query = $(this).val().trim();
            if (query.length > 0) {
                $.ajax({
                    url: "search_logic.php",
                    method: "POST",
                    data: { search: query },
                    success: function (data) {
                        $("#results").html(data).show();
                    },
                    error: function () {
                        $("#results").html("<div class='no-result'>Error fetching suggestions</div>").show();
                    }
                });
            } else {
                $("#results").hide();
            }
        });

        $(document).on("click", ".suggestion", function () {
            const href = $(this).data("href");
            window.location.href = href;
        });

        $(document).on("click", function (e) {
            if (!$(e.target).closest("#search, #results").length) {
                $("#results").hide();
            }
        });
    });
</script>
</body>
</html>
