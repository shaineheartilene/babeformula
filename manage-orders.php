<?php
session_start();
include 'connect.php';

// Redirect to login if not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit();
}

// Fetch orders with calculated total price from `order_details`
$query = "
    SELECT o.id, u.username AS customer, o.created_at,
           (SELECT SUM(od.quantity * od.price) FROM order_details od WHERE od.order_id = o.id) AS total_price
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";
$result = $conn->query($query);

// Handle status update
if (isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Ensure the status column exists before updating
    $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $newStatus, $orderId);

    if ($stmt->execute()) {
        header("Location: manage-orders.php");
        exit();
    } else {
        echo "Error updating order status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="manage-orders-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Sidebar Section -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="babelogo.png" alt="Babe Logo" class="logo">
        </div>
        <ul class="menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="admin-products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="manage-users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
            <li><a href="manage-orders.php" class="active"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
            <li><a href="message.php"><i class="fas fa-comments"></i> <span>Messages</span></a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
            <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
        </ul>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar -->
    <div class="navbar">
        <h2 class="navbar-title">Manage Orders</h2>
    </div>

    <div class="main-content">
        <!-- Orders Table -->
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Price (₱)</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['customer']) ?></td>
                            <td>₱<?= number_format($row['total_price'], 2) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <form method="POST" action="manage-orders.php">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <select name="status" required>
                                        <option value="to pay">To Pay</option>
                                        <option value="to ship">To Ship</option>
                                        <option value="to receive">To Receive</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="return/refund">Return/Refund</option>
                                    </select>
                                    <button type="submit" name="update_status" class="update-btn">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Sidebar toggle script
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebarToggle');
        const mainContent = document.querySelector('.main-content');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    </script>

</body>
</html>
