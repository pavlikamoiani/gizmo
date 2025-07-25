<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'gizmo';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>


<!-- $host = 'localhost';
$dbname = 'gizmocomge475_gizmo'; 
$user = 'gizmocomge475_admin'; 
$password = 'kY82La1deohdrZ'; -->
<!-- kY82La1deohdrZ -->