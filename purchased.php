<?php
session_start();
include 'connect.php';
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

// Fetch the user's purchased products
$username = $_SESSION['username'];
$orderQuery = "SELECT id, total_amount, order_date FROM orders WHERE username = ? ORDER BY order_date DESC";
$orderStmt = $conn->prepare($orderQuery);
$orderStmt->bind_param("s", $username);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

$orders = [];
while ($order = $orderResult->fetch_assoc()) {
    // Fetch related order details (products)
    $orderDetailsQuery = "SELECT order_details.product_id, order_details.quantity, products.price, products.product_name, products.image_path 
                          FROM order_details 
                          JOIN products ON order_details.product_id = products.id
                          WHERE order_id = ?";
    $orderDetailsStmt = $conn->prepare($orderDetailsQuery);
    $orderDetailsStmt->bind_param("i", $order['id']);
    $orderDetailsStmt->execute();
    $orderDetailsResult = $orderDetailsStmt->get_result();
    
    $order['details'] = $orderDetailsResult->fetch_all(MYSQLI_ASSOC);
    $orders[] = $order;
    
    $orderDetailsStmt->close();
}

$orderStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchased Products</title>
    <link rel="stylesheet" href="purchased-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <img src="babelogo.png" alt="Babe Logo" class="logo">
    </div>
    <ul class="menu">
        <li><a href="home.php"><i class="fas fa-home"></i> <span>Homepage</span></a></li>
        <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="account.php"><i class="fas fa-user"></i> <span>Babe Account</span></a></li>
        <li><a href="purchased.php" class="active"><i class="fas fa-shopping-bag"></i> <span>Babe Purchased</span></a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span>Babe Cart</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
    </ul>
</div>

<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="navbar">
    <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
    <div class="profile-dropdown" id="profile-dropdown">
        <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
    </div>
    <h2 class="navbar-title">BABE PURCHASED</h2>
</div>

<div class="main-content">
    <h1>Your Purchased Products</h1>

    <div class="cart-items">
        <?php if (empty($orders)): ?>
            <p>You have not made any purchases yet.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
               <!-- <h2>Order ID: <?= htmlspecialchars($order['id']); ?></h2>
                <p><b>Total Amount:</b> ₱<?= number_format($order['total_amount'], 2); ?></p>
                <p><b>Order Date:</b> <?= htmlspecialchars($order['order_date']); ?></p>-->
                <?php foreach ($order['details'] as $product): ?>
                    <div class="cart-item" data-product-id="<?= htmlspecialchars($product['product_id']); ?>">
                        <img src="<?= htmlspecialchars($product['image_path']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>" class="cart-item-image">
                        <div class="cart-item-details">
                            <h3><?= htmlspecialchars($product['product_name']); ?></h3>
                            <p>₱<?= number_format($product['price'], 2); ?></p>
                            <p>Quantity: <?= htmlspecialchars($product['quantity']); ?></p> <!-- Display quantity without buttons -->
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
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
</script>

</body>
</html>
