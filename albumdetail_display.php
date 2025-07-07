<?php
session_start();
include 'db_connect.php';

// Get album details
$album_id = $_GET['album_id'];
$album_stmt = $conn->prepare("SELECT title, description, cover_image_url, release_year FROM albums WHERE album_id = ?");
$album_stmt->bind_param("i", $album_id);
$album_stmt->execute();
$album = $album_stmt->get_result()->fetch_assoc();

// Get songs for this album
$song_stmt = $conn->prepare("SELECT song_id, title, duration FROM songs WHERE album_id = ?");
$song_stmt->bind_param("i", $album_id);
$song_stmt->execute();
$songs = $song_stmt->get_result();

// Calculate total duration
$song_count = 0;
$total_duration_seconds = 0;
$song_list = [];

while ($song = $songs->fetch_assoc()) {
    $duration = $song['duration'];
    list($h, $m, $s) = explode(":", $duration);
    $seconds = ($h * 3600) + ($m * 60) + $s;
    $total_duration_seconds += $seconds;
    $song_list[] = $song;
    $song_count++;
}

$formatted_duration = sprintf("%02d:%02d:%02d", floor($total_duration_seconds / 3600), ($total_duration_seconds / 60) % 60, $total_duration_seconds % 60);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WeekndWIK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts & CSS -->
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">

    <!-- Inline Style -->
    <style>
        .album-info {
            padding: 20px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 20px;
        }

        .album-cover {
            max-width: 300px;
            height: auto;
            border-radius: 10px;
        }

        .songs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .songs-table th, .songs-table td {
            padding: 10px;
            border-bottom: 1px solid #333;
            text-align: left;
        }

        .songs-table th {
            color: #aaa;
        }

        .songs-table td {
            color: #eee;
            font-size: 16px;
        }

        .song-title {
            display: flex;
            align-items: center;
        }

        .cover-image {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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

    <!-- Album Info -->
    <div class="container">
        <div class="row album-info">
            <div class="col-3">
                <img src="<?= htmlspecialchars($album['cover_image_url']) ?>" alt="Album Cover" class="album-cover">
            </div>
            <div class="col-7">
                <h2><?= htmlspecialchars($album['title']) ?></h2>
                <p><?= htmlspecialchars($album['description']) ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($album['release_year']) ?></p>
                <p><strong>Total Songs:</strong> <?= $song_count ?> Songs</p>
                <p><strong>Duration:</strong> <?= $formatted_duration ?></p>
            </div>
        </div>

        <!-- Songs Table -->
        <div style="padding: 0 20px;">
            <table class="songs-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($song_list) > 0): ?>
                        <?php foreach ($song_list as $i => $song): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <div class="song-title">
                                        <a href="songdetail_display.php?song_id=<?= htmlspecialchars($song['song_id']) ?>">
                                            <img src="<?= htmlspecialchars($album['cover_image_url']) ?>" alt="Cover Image" class="cover-image">
                                            <span><?= htmlspecialchars($song['title']) ?></span>
                                        </a>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($song['duration']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No songs found for this album.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleNav() {
            document.getElementById('navCenter').classList.toggle('show');
            document.getElementById('navRight').classList.toggle('show');
        }
    </script>
</body>
</html>
