<?php
session_start();
include 'connect.php';

if (!isset($_SESSION["username"])) {
    header("Location: admin-login.php");
    exit();
}

// Fetching Summary Data
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as revenue FROM orders"))['revenue'];

// Fetching Revenue Data Over Time for Line Chart
$revenue_data = mysqli_query($conn, "SELECT DATE(order_date) as date, SUM(total_amount) as daily_revenue FROM orders GROUP BY DATE(order_date) ORDER BY date ASC");

// Prepare data for the revenue chart
$dates = [];
$revenues = [];
while ($row = mysqli_fetch_assoc($revenue_data)) {
    $dates[] = $row['date'];
    $revenues[] = $row['daily_revenue'];
}

// Fetching Top-Selling Products for Bar Chart
$top_selling_products = mysqli_query($conn, "
    SELECT products.product_name, SUM(order_details.quantity) as total_sold
    FROM order_details
    JOIN products ON order_details.product_id = products.id
    GROUP BY order_details.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");

// Prepare data for the top-selling products chart
$product_names = [];
$product_sales = [];
while ($row = mysqli_fetch_assoc($top_selling_products)) {
    $product_names[] = $row['product_name'];
    $product_sales[] = $row['total_sold'];
}

// Fetching Recent Orders
$recent_orders = mysqli_query($conn, "SELECT id, username, total_amount, order_date FROM orders ORDER BY order_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Sidebar Section -->
<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <img src="babelogo.png" alt="Babe Logo" class="logo">
    </div>

    <ul class="menu">
        <li><a href="admin-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="admin-products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="manage-users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
        <li><a href="manage-orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="message.php"><i class="fas fa-comments"></i> <span>Messages</span></a></li>
        <li><a href="reviews.php"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
    </ul>
</div>

<!-- Sidebar Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Main Content -->
<div class="main-content">
    <div class="navbar">
        <span class="navbar-title">Admin Dashboard</span>
        <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
        <div class="profile-dropdown" id="profile-dropdown">
            <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="overview-section">
        <div class="card">
            <i class="fas fa-box card-icon"></i>
            <h3>Total Products</h3>
            <p><?= $total_products ?></p>
        </div>
        <div class="card">
            <i class="fas fa-shopping-cart card-icon"></i>
            <h3>Total Orders</h3>
            <p><?= $total_orders ?></p>
        </div>
        <div class="card">
            <i class="fas fa-users card-icon"></i>
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        <div class="card">
            <i class="fas fa-coins card-icon"></i>
            <h3>Total Revenue</h3>
            <p>₱<?= number_format($total_revenue, 2) ?></p>
        </div>
    </div>

    <div class="charts-wrapper">
        <!-- Revenue Chart -->
        <div class="chart-container small-chart">
            <h3>Revenue Over Time</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <!-- Top-Selling Products Chart -->
        <div class="chart-container small-chart">
            <h3>Top-Selling Products</h3>
            <canvas id="topSellingProductsChart"></canvas>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="recent-orders-section">
        <h3>Recent Orders</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['username'] ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const navbar = document.querySelector('.navbar');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        navbar.classList.toggle('expanded');
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [{
                label: 'Daily Revenue (₱)',
                data: <?= json_encode($revenues) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top-Selling Products Chart
    const topSellingProductsCtx = document.getElementById('topSellingProductsChart').getContext('2d');
    const topSellingProductsChart = new Chart(topSellingProductsCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($product_names) ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?= json_encode($product_sales) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
