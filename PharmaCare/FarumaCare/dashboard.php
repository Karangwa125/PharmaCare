<?php
session_start();

// Access control: no session, no entry
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

$totalMedicines = 0;
$lowStock       = 0;
$expiringSoon   = 0;
$expired        = 0;
$todaySales     = 0;
$recentSales    = [];
$error          = '';

try {
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines");
    $stmt->execute();
    $totalMedicines = $stmt->fetchColumn();

    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines WHERE quantity <= :limit");
    $stmt->bindValue(':limit', 10, PDO::PARAM_INT);
    $stmt->execute();
    $lowStock = $stmt->fetchColumn();

    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines
                           WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $expiringSoon = $stmt->fetchColumn();

    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM medicines WHERE expiry_date < CURDATE()");
    $stmt->execute();
    $expired = $stmt->fetchColumn();

    
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_price), 0) FROM sales WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $todaySales = $stmt->fetchColumn();

    // Last 5 sales (JOIN across three tables)
    $stmt = $pdo->prepare("SELECT m.name AS medicine, s.quantity_sold, s.total_price, u.username, s.created_at
                           FROM sales s
                           JOIN medicines m ON s.medicine_id = m.id
                           JOIN users u ON s.user_id = u.id
                           ORDER BY s.created_at DESC
                           LIMIT 5");
    $stmt->execute();
    $recentSales = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'Could not load dashboard data. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <title>FarumaCare - Dashboard</title>
</head>
<body>
    <div class="D1">
        <img src="image/Logo2.png" alt="FarumaCare Logo" style="width: 150px; height: 150px; margin-left:38px">

          <div class="nav-links">
        <a href="dashboard.php" class="active">Dashboard</a><br>
        <a href="medicines/list.php">Medicines</a><br>
        <a href="sales/list.php">Sales</a><br>


        <div class="cards">
        <div class="card">
            <span class="card-label">Total medicines</span>
            <span class="card-value"><?= (int)$totalMedicines ?></span>
        </div>
        <div class="card warning">
            <span class="card-label">Low stock</span>
            <span class="card-value"><?= (int)$lowStock ?></span>
        </div>
        <div class="card warning">
            <span class="card-label">Expiring in 30 days</span>
            <span class="card-value"><?= (int)$expiringSoon ?></span>
        </div>
        <div class="card danger">
            <span class="card-label">Expired</span>
            <span class="card-value"><?= (int)$expired ?></span>
        </div>
        <div class="card">
            <span class="card-label">Today's sales</span>
            <span class="card-value"><?= number_format((float)$todaySales, 2) ?></span>
        </div>
    </div>

        
       
    </div>
    <div class="icon"> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.0122 2.25C12.7462 2.25846 13.4773 2.34326 14.1937 2.50304C14.5064 2.57279 14.7403 2.83351 14.7758 3.15196L14.946 4.67881C15.0231 5.37986 15.615 5.91084 16.3206 5.91158C16.5103 5.91188 16.6979 5.87238 16.8732 5.79483L18.2738 5.17956C18.5651 5.05159 18.9055 5.12136 19.1229 5.35362C20.1351 6.43464 20.8889 7.73115 21.3277 9.14558C21.4223 9.45058 21.3134 9.78203 21.0564 9.9715L19.8149 10.8866C19.4607 11.1468 19.2516 11.56 19.2516 11.9995C19.2516 12.4389 19.4607 12.8521 19.8157 13.1129L21.0582 14.0283C21.3153 14.2177 21.4243 14.5492 21.3297 14.8543C20.8911 16.2685 20.1377 17.5649 19.1261 18.6461C18.9089 18.8783 18.5688 18.9483 18.2775 18.8206L16.8712 18.2045C16.4688 18.0284 16.0068 18.0542 15.6265 18.274C15.2463 18.4937 14.9933 18.8812 14.945 19.3177L14.7759 20.8444C14.741 21.1592 14.5122 21.4182 14.204 21.4915C12.7556 21.8361 11.2465 21.8361 9.79803 21.4915C9.48991 21.4182 9.26105 21.1592 9.22618 20.8444L9.05736 19.32C9.00777 18.8843 8.75434 18.498 8.37442 18.279C7.99451 18.06 7.5332 18.0343 7.1322 18.2094L5.72557 18.8256C5.43422 18.9533 5.09403 18.8833 4.87678 18.6509C3.86462 17.5685 3.11119 16.2705 2.6732 14.8548C2.57886 14.5499 2.68786 14.2186 2.94485 14.0293L4.18818 13.1133C4.54232 12.8531 4.75147 12.4399 4.75147 12.0005C4.75147 11.561 4.54232 11.1478 4.18771 10.8873L2.94516 9.97285C2.6878 9.78345 2.5787 9.45178 2.67337 9.14658C3.11212 7.73215 3.86594 6.43564 4.87813 5.35462C5.09559 5.12236 5.43594 5.05259 5.72724 5.18056L7.12762 5.79572C7.53056 5.97256 7.9938 5.94585 8.37577 5.72269C8.75609 5.50209 9.00929 5.11422 9.05817 4.67764L9.22824 3.15196C9.26376 2.83335 9.49786 2.57254 9.8108 2.50294C10.5281 2.34342 11.26 2.25865 12.0122 2.25ZM12.0124 3.7499C11.5583 3.75524 11.1056 3.79443 10.6578 3.86702L10.5489 4.84418C10.4471 5.75368 9.92003 6.56102 9.13042 7.01903C8.33597 7.48317 7.36736 7.53903 6.52458 7.16917L5.62629 6.77456C5.05436 7.46873 4.59914 8.25135 4.27852 9.09168L5.07632 9.67879C5.81513 10.2216 6.25147 11.0837 6.25147 12.0005C6.25147 12.9172 5.81513 13.7793 5.0771 14.3215L4.27805 14.9102C4.59839 15.752 5.05368 16.5361 5.626 17.2316L6.53113 16.8351C7.36923 16.4692 8.33124 16.5227 9.12353 16.9794C9.91581 17.4361 10.4443 18.2417 10.548 19.1526L10.657 20.1365C11.5466 20.2878 12.4555 20.2878 13.3451 20.1365L13.4541 19.1527C13.5549 18.2421 14.0828 17.4337 14.876 16.9753C15.6692 16.5168 16.6332 16.463 17.4728 16.8305L18.3772 17.2267C18.949 16.5323 19.4041 15.7495 19.7247 14.909L18.9267 14.3211C18.1879 13.7783 17.7516 12.9162 17.7516 11.9995C17.7516 11.0827 18.1879 10.2206 18.9258 9.67847L19.7227 9.09109C19.4021 8.25061 18.9468 7.46784 18.3748 6.77356L17.4783 7.16737C17.113 7.32901 16.7178 7.4122 16.3187 7.41158C14.849 7.41004 13.6155 6.30355 13.4551 4.84383L13.3462 3.8667C12.9007 3.7942 12.4526 3.75512 12.0124 3.7499ZM11.9997 8.24995C14.0708 8.24995 15.7497 9.92888 15.7497 12C15.7497 14.071 14.0708 15.75 11.9997 15.75C9.92863 15.75 8.2497 14.071 8.2497 12C8.2497 9.92888 9.92863 8.24995 11.9997 8.24995ZM11.9997 9.74995C10.7571 9.74995 9.7497 10.7573 9.7497 12C9.7497 13.2426 10.7571 14.25 11.9997 14.25C13.2423 14.25 14.2497 13.2426 14.2497 12C14.2497 10.7573 13.2423 9.74995 11.9997 9.74995Z" fill="green" />
</svg></div>

<div>
     <a href="logout.php" class="btn-logout">Logout</a>
</div>
    

    </div>
<div class="D2">
  
<main class="container">
<div class="BC1">
    <h2 class="welcome">
        Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
        <span class="role-badge"><?= htmlspecialchars($_SESSION['role']) ?></span>
    </h2>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <h3 class="section-title">Overview</h3>
    
    <h3 class="section-title">Quick actions</h3>
    <div class="actions">
        <a class="btn" href="medicines/add.php">Add medicine</a>
        <a class="btn btn-outline" href="sales/add.php">Record sale</a>
    </div>

    <h3 class="section-title">Recent sales</h3>
    <?php if (empty($recentSales)): ?>
        <p class="empty">No sales recorded yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Medicine</th><th>Qty</th><th>Total</th><th>Sold by</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['medicine']) ?></td>
                            <td><?= (int)$sale['quantity_sold'] ?></td>
                            <td><?= number_format((float)$sale['total_price'], 2) ?></td>
                            <td><?= htmlspecialchars($sale['username']) ?></td>
                            <td><?= htmlspecialchars($sale['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    </div>

</main>
</div>
</body>
</html>