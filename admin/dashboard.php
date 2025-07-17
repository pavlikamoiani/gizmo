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
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <span class="sidebar-logo">Gizmo <b>Admin</b></span>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="dashboard.php"><span class="sidebar-icon">🏠</span>Dashboard</a></li>
                    <li>
                        <a href="../components/admin/categories-list.php">
                            <span class="sidebar-icon">📦</span>Categories
                        </a>
                    </li>
                    <li>
                        <a href="../components/admin/products-list.php">
                            <span class="sidebar-icon">🛒</span>Products
                        </a>
                    </li>
                    <li>
                        <a href="export-excel.php">
                            <span class="sidebar-icon">⬇️</span>Export Excel
                        </a>
                    </li>
                    <li>
                        <form id="importExcelFormSidebar" action="import-excel.php" method="post"
                            enctype="multipart/form-data" style="display:inline;">
                            <label for="excelFileSidebar" style="margin-bottom:0;display:inline-block;cursor:pointer;">
                                <span class="sidebar-icon">⬆️</span>Import Excel
                                <input type="file" id="excelFileSidebar" name="excelFile" accept=".xlsx,.xls"
                                    style="display:none;"
                                    onchange="document.getElementById('importExcelFormSidebar').submit();">
                            </label>
                        </form>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="sidebar-logout">Logout</a>
            </div>
        </aside>
        <main class="main-content">
            <header class="main-header">
                <h1>Dashboard</h1>
                <div class="main-header-actions">
                    <span class="main-header-user">Admin</span>
                </div>
            </header>

            <div id="autoHideAlert" class="alert success-alert" style="display:none;">
                <span class="alert-icon">✔️</span>
                Welcome to your admin dashboard!
            </div>
            <script>
                if (!sessionStorage.getItem('dashboardAlertShown')) {
                    var alert = document.getElementById('autoHideAlert');
                    if (alert) {
                        alert.style.display = 'flex';
                        setTimeout(function () {
                            alert.style.display = 'none';
                        }, 5000);
                    }
                    sessionStorage.setItem('dashboardAlertShown', '1');
                }
            </script>
            <div class="dashboard-cards">
                <div class="dashboard-card card-blue">
                    <div class="card-title">10468</div>
                    <div class="card-desc">Sub Categories</div>
                    <div class="card-chart"></div>
                </div>
                <div class="dashboard-card card-cyan">
                    <div class="card-title">450</div>
                    <div class="card-desc">Products</div>
                    <div class="card-chart"></div>
                </div>
                <div class="dashboard-card card-yellow">
                    <div class="card-title">12</div>
                    <div class="card-desc">Categories</div>
                    <div class="card-chart"></div>
                </div>
                <div class="dashboard-card card-red">
                    <div class="card-title">5</div>
                    <div class="card-desc">Admins</div>
                    <div class="card-chart"></div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>