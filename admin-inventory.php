<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit();
}

// Database connection
include('connect.php');

// Fetch products
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!empty($category)) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->bind_param("s", $category);
} else {
    $stmt = $conn->prepare("SELECT * FROM products");
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for dropdown
$categoryQuery = "SELECT DISTINCT category FROM products";
$categoryResult = $conn->query($categoryQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="admin-inventory-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Sidebar Section -->
<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <img src="babelogo.png" alt="Babe Logo" class="logo">
    </div>
    <ul class="menu">
        <li><a href="admin-dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="admin-products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="manage-users.php"><i class="fas fa-user"></i> <span>Users</span></a></li>
        <li><a href="manage-orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="message.php"><i class="fas fa-message"></i> <span>Message</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
    </ul>
</div>

<!-- Sidebar Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>


<!-- Navbar Section -->
<div class="navbar">
    <h2 class="navbar-title">INVENTORY</h2>
    <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
    <div class="profile-dropdown" id="profile-dropdown">
        <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
    </div>
</div>

<!-- Main Content Section -->
<div class="main-content">
        <form method="GET" action="admin-inventory.php">
            <div class="dropdown-wrapper">
                <select name="category" class="dropdown" onchange="this.form.submit()">
                    <option value="">All Products</option>
                    <?php while ($categoryRow = $categoryResult->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($categoryRow['category']) ?>" <?= ($category === $categoryRow['category']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($categoryRow['category'])) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <i class=""></i>
                <div class="back-button">
        <a href="admin-products.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back
        </a>
            </div>
            
        </form>
        
    </div>

    <!-- Inventory Table Section -->
    <div class="inventory-table-container">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Date Added</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '
            <tr>
                <td>' . htmlspecialchars($row['product_name']) . '</td>
                <td>' . htmlspecialchars($row['description']) . '</td>
                <td>' . (isset($row['date_added']) ? htmlspecialchars($row['date_added']) : '') . '</td>
                <td>' . ($row['stocks'] > 0 ? 'Available' : 'Out of Stock') . '</td>
                <td>₱' . htmlspecialchars(number_format($row['price'], 2)) . '</td>
                <td>' . htmlspecialchars($row['stocks']) . '</td>
            </tr>
            ';
        }
    } else {
        echo '<tr><td colspan="6">No products available.</td></tr>';
    }
    ?>
</tbody>

        </table>
    </div>
</div>
</div>
<script>
     const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebarToggle');
        const mainContent = document.querySelector('.main-content');
        const navbar = document.querySelector('.navbar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbar.classList.toggle('expanded');
        });
        function toggleProfileDropdown(event) {
            event.preventDefault();
            var dropdown = document.getElementById("profile-dropdown");
            dropdown.classList.toggle("active");
        }

        document.addEventListener('click', function(event) {
            var dropdown = document.getElementById("profile-dropdown");
            var profileIcon = document.querySelector(".profile-icon");
            if (!profileIcon.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });
</script>

</body>
</html>