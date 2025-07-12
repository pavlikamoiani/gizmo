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
    <h1>Welcome, Admin!</h1>
    <p><a href="logout.php">Logout</a></p>
    <button id="openAddCategoryModal" class="add-category-btn">Add Category</button>
    <?php include '../components/admin/add-categories.php'; ?>
    <script>
        document.getElementById('openAddCategoryModal').onclick = function() {
            document.getElementById('addCategoryModal').style.display = 'block';
        };
        document.getElementById('closeAddCategoryModal').onclick = function() {
            document.getElementById('addCategoryModal').style.display = 'none';
        };
        window.onclick = function(event) {
            if (event.target == document.getElementById('addCategoryModal')) {
                document.getElementById('addCategoryModal').style.display = 'none';
            }
        };
    </script>
</body>
</html>
