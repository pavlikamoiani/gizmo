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
    <link rel="stylesheet" href="../css/admin/dashboard.css">
</head>

<body class="dashboard-bg">
    <div class="dashboard-container">
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <div class="dashboard-btns">
            <button id="goToCategories" class="dashboard-btn"
                onclick="window.location.href='../components/admin/categories-list.php'">Categories</button>
            <button id="goToProducts" class="dashboard-btn"
                onclick="window.location.href='../components/admin/products-list.php'">Products</button>
        </div>
        <p class="dashboard-logout"><a href="logout.php">Logout</a></p>
    </div>
</body>

</html>