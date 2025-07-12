<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin/add-categories.css">
</head>

<body>
    <button id="goToCategories" class="categories-btn"
        onclick="window.location.href='../components/admin/categories-list.php'">Categories</button>
    <p><a href="logout.php">Logout</a></p>
</body>

</html>