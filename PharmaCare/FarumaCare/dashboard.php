
<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

$TotalMedicines = 0;
$lowStock = 0;
$ExpiringSoon = 0;
$Expired = 0;
$TotalSales = 0;
$RecentSales = 0;
$RecentSales  = [];
$Error = '';

try{
    //Total Medicines

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines");
    $stmt->execute();
    $TotalMedicines = $stmt->fetchColumn();


    //lowStock

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines WHERE quantity < 10");
    $stmt->execute();
    $lowStock = $stmt->fetchColumn();



    //ExpiringSoon







?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

<strong>FarumaCare</strong>

<nav>
    <a href="medicine/list.php">Medicines</a>
    <a href="sales/list.php">Sales</a>
    <a href="Dashboard.php">Dashboard</a>
    <a href="Logout">Logout</a>
</nav>
    
</body>
</html>