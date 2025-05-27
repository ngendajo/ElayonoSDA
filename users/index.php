<?php
session_start();
include 'includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Elayono Dashboard</title>
  <link rel="icon" href="../images/sdalogo.png" type="image/x-icon">
  <link rel="stylesheet" href="style.css">
  
</head>
<body>
<div class="sidebar">
    <h2>Menu</h2>
    <a href="index.php?page=dashboard">Dashboard</a>
    <a href="index.php?page=users">Users</a>
    <a href="index.php?page=elders">Upload Users</a>
    <a href="index.php?page=sliders">Sliders</a>
    <a href="index.php?page=messages">Messages</a>
    <a href="index.php?page=departments">Departments</a>
    <a href="index.php?page=news">News</a>
    <a href="index.php?page=books">Books</a>
    <a href="index.php?page=letters">Letters</a>
    <a href="index.php?page=ssl">SS Lessons</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="main">
    <h1 style="text-align: center;">Welcome, <?php echo $_SESSION['names']; ?> (<?php echo $_SESSION['user_type']; ?>)</h1>
    <div class="content">
      <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

        $allowed_pages = [
          'dashboard', 'users', 'elders', 'sliders',
          'messages', 'departments', 'news', 'books', 'letters','ssl'
        ];

        if (in_array($page, $allowed_pages)) {
          include "$page.php";
        } else {
          echo "<h1>Page not found</h1>";
        }
      ?>
    </div>
  </div>

</div>
</body>
</html>
