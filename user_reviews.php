<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's reviews
$sql = "SELECT r.review_id, r.song_id, r.rating, r.review_text, r.review_date, s.title AS song_title
        FROM reviews r
        JOIN songs s ON r.song_id = s.song_id
        WHERE r.user_id = ? 
        ORDER BY r.review_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
    $review_id = (int) $_POST['delete_review_id'];

    $delete_stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $review_id, $user_id);
    if ($delete_stmt->execute()) {
        $delete_message = "Review deleted successfully.";
    } else {
        $delete_message = "Error deleting review.";
    }
    $delete_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Reviews - WeekndWIK</title>

  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">

  <style>
    .review-item {
      position: relative;
      padding: 10px;
      border: 1px solid #ddd;
      margin-bottom: 10px;
      display: block;
      color: inherit;
      border-radius: 8px;
    }

    .menu-button {
      color: white;
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: 1px solid white;
      font-size: 24px;
      cursor: pointer;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 60px;
      right: 10px;
      background-color: #fff;
      border: 1px solid #ddd;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 100;
    }

    .dropdown-menu a {
      display: block;
      padding: 8px 12px;
      text-decoration: none;
      color: #333;
    }

    .dropdown-menu a:hover {
      background-color: #f0f0f0;
    }

    .menu-container:hover .dropdown-menu {
      display: block;
    }
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="topnav">
    <div class="logo">WeekndWIK</div>
    <div class="nav-center" id="navCenter">
      <a href="index.php">HOME</a>
      <a href="album.php">ALBUM</a>
      <a href="song.php">SONG</a>
      <a href="favourite.php" class="active">MY FAV</a>
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

  <div class="row">
    <div class="col-3"></div>
    <div class="col-6">
      <h2>Your Reviews</h2>

      <?php if (isset($delete_message)): ?>
        <p><?= htmlspecialchars($delete_message); ?></p>
      <?php endif; ?>

      <?php if ($result->num_rows > 0): ?>
        <div class="reviews-list">
          <?php while ($review = $result->fetch_assoc()): 
            $reviewId = (int) $review['review_id']; ?>
            <div class="review-item">
              <h3><?= htmlspecialchars($review['song_title']); ?></h3>
              <p>Rating: <?= htmlspecialchars($review['rating']); ?>/5</p>
              <p>Comment: <?= htmlspecialchars($review['review_text']); ?></p>
              <p>Date: <?= htmlspecialchars($review['review_date']); ?></p>

              <div class="menu-container">
                <button class="menu-button" type="button">⋮</button>
                <div class="dropdown-menu">
                  <a href="songdetail_display.php?song_id=<?= htmlspecialchars((int)$review['song_id']); ?>">
                    <span class="material-symbols-outlined">edit</span>
                  </a>
                  <a href="#" onclick="if(confirm('Are you sure you want to delete this review?')) {
                      document.getElementById('delete-form-<?= $reviewId ?>').submit();
                    } return false;">
                    <span class="material-symbols-outlined">delete</span>
                  </a>
                  <form id="delete-form-<?= $reviewId ?>" method="POST" style="display:none;">
                    <input type="hidden" name="delete_review_id" value="<?= $reviewId ?>">
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p>You haven't reviewed any songs yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleNav() {
      document.getElementById('navCenter').classList.toggle('show');
      document.getElementById('navRight').classList.toggle('show');
    }

    // Toggle dropdowns
    document.querySelectorAll('.menu-button').forEach(button => {
      button.addEventListener('click', function (e) {
        e.stopPropagation(); // prevent window click from closing it immediately
        const menu = this.nextElementSibling;
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
      });
    });

    window.addEventListener('click', function () {
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
      });
    });
  </script>
</body>
</html>
