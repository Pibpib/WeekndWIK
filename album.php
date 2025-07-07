<?php
include 'db_connect.php';
session_start();

// Handle search and filters
$album_results = [];
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filters = [];
$filter_clauses = [];

$query = "SELECT * FROM albums";

// Add search query
if (!empty($search)) {
    $query .= " WHERE title LIKE '%$search%'";
}

// Add filters
if (isset($_GET['year']) && is_array($_GET['year'])) {
    $years = array_map('intval', $_GET['year']);
    if (!empty($years)) {
        $filter_clauses[] = "release_year IN (" . implode(',', $years) . ")";
    }
}

// Combine filter clauses
if (!empty($filter_clauses)) {
    $query .= " WHERE " . implode(' AND ', $filter_clauses);
}

// Execute the query
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $album_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols Icon -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>WeekndWIK</title>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="topnav">
        <div class="logo">WeekndWIK</div>

        <div class="nav-center" id="navCenter">
            <a href="index.php">HOME</a>
            <a href="album.php" class="active">ALBUM</a>
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
    
    <!-- Search bar -->
    <div class="search-container">
        <span class="material-symbols-outlined search-icon" style="color: gray">search</span>
        <form method="GET" action="album.php" style="display: flex; width: 100%;" required>
        <input id="search" style="margin: 0; border: 0;" class="search-input" type="text" name="search" placeholder="Search album...">
        <div id="results"></div>
            <button type="button" class="filter-button" onclick="openFilterPopup()">
              <span class="material-symbols-outlined">instant_mix</span>
            </button>

            <button type="submit" class="search-button">
              <span class="material-symbols-outlined">search</span>
            </button>
        </form>
    </div>
    
    <!-- Filter Form and Modal -->
    <div id="filterPopup" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeFilterPopup()">&times;</span>
            <h2>Filter by</h2>
            <form method="GET" action="album.php">
                <div>
                    <h3>Year</h3>
                    <label><input type="checkbox" name="year[]" value="2012"> 2012</label><br>
                    <label><input type="checkbox" name="year[]" value="2013"> 2013</label><br>
                    <label><input type="checkbox" name="year[]" value="2015"> 2015</label><br>
                    <label><input type="checkbox" name="year[]" value="2016"> 2016</label><br>
                    <label><input type="checkbox" name="year[]" value="2018"> 2018</label><br>
                    <label><input type="checkbox" name="year[]" value="2020"> 2020</label><br>
                    <label><input type="checkbox" name="year[]" value="2022"> 2022</label><br>
                </div>
                <button type="submit">Apply Filters</button>
            </form>
        </div>
    </div>
    
    <!-- Album Display -->
    <div class="container">
        <div class="grid-container">
            <?php if (!empty($album_results)): ?>
                <?php foreach ($album_results as $row): ?>
                    <div class="grid-item album-item" onclick="window.location.href='albumdetail_display.php?album_id=<?= $row['album_id']; ?>'">
                        <img src="<?= htmlspecialchars($row['cover_image_url']); ?>" alt="Album Cover" style="width:100%; height:auto; border-radius:8px;">
                        <h3 class="title"><?= htmlspecialchars($row['title']); ?></h3>
                        <p>Year: <?= htmlspecialchars($row['release_year']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No albums found for this search or filter.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleNav() {
            document.getElementById('navCenter').classList.toggle('show');
            document.getElementById('navRight').classList.toggle('show');
        }
        // Function to open the filter popup
        function openFilterPopup() {
            document.getElementById("filterPopup").style.display = "block";
        }

        // Function to close the filter popup
        function closeFilterPopup() {
            document.getElementById("filterPopup").style.display = "none";
        }

          
        $(document).ready(function () {
    $("#search").on("keyup", function () {
        let query = $(this).val().trim();
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

    // Redirect to the song detail page when a suggestion is clicked
    $(document).on("click", ".suggestion", function () {
        const href = $(this).data("href"); 
        window.location.href = href; 
    });

    // Close suggestions when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest("#search, #results").length) {
            $("#results").hide();
        }
    });
});
    </script>
</body>
</html>
