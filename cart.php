<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Fetch the logged-in user's username
$username = $_SESSION['username'];

// Fetch the user's cart items using the username
$query = "SELECT products.id AS product_id, products.product_name, products.price, products.image_path, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if the cart is empty
if ($result->num_rows === 0) {
    echo "<p>Your cart is empty.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Babe Cart</title>
    <link rel="stylesheet" href="cart-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Sidebar Section -->
<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <img src="babelogo.png" alt="Babe Logo" class="logo">
    </div>
    <ul class="menu">
        <li><a href="home.php"><i class="fas fa-home"></i> <span>Homepage</span></a></li>
        <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="account.php"><i class="fas fa-user"></i> <span>Babe Account</span></a></li>
        <li><a href="purchased.php"><i class="fas fa-shopping-bag"></i> <span>Babe Purchased</span></a></li>
        <li><a href="cart.php" class="active"><i class="fas fa-shopping-cart"></i> <span>Babe Cart</span></a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
    </ul>
</div>

<!-- Sidebar Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- navbar -->
<div class="navbar">
    <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
                <div class="profile-dropdown" id="profile-dropdown">
                    <span> <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                </div>
                <h2 class="navbar-title">BABE CART</h2>
    </div>

<!-- Main Content Section -->
<div class="main-content">
    <h2>Your Cart</h2>

    <div class="cart-items">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="cart-item" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                <img src="<?= htmlspecialchars($row['image_path']); ?>" alt="<?= htmlspecialchars($row['product_name']); ?>" class="cart-item-image">
                <div class="cart-item-details">
                    <h3><?= htmlspecialchars($row['product_name']); ?></h3>
                    <p>₱<?= number_format($row['price'], 2); ?></p>
                    <div class="quantity-control">
                        <button class="quantity-btn decrease">-</button>
                        <input type="text" value="<?= htmlspecialchars($row['quantity']); ?>" class="quantity-input" readonly>
                        <button class="quantity-btn increase">+</button>
                    </div>
                </div>
                <button class="remove-btn"><i class="fas fa-trash"></i></button>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Proceed to Checkout Button -->
    <form action="checkout.php" method="POST">
        <input type="hidden" name="username" value="<?= htmlspecialchars($username); ?>">
        <button type="submit" class="checkout-btn">Proceed to Checkout</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cartItems = document.querySelectorAll('.cart-item');

        cartItems.forEach(item => {
            const productId = item.getAttribute('data-product-id');
            const decreaseBtn = item.querySelector('.decrease');
            const increaseBtn = item.querySelector('.increase');
            const removeBtn = item.querySelector('.remove-btn');
            const quantityInput = item.querySelector('.quantity-input');

            // Decrease quantity
            decreaseBtn.addEventListener('click', () => updateQuantity(productId, -1));

            // Increase quantity
            increaseBtn.addEventListener('click', () => updateQuantity(productId, 1));

            // Remove item
            removeBtn.addEventListener('click', () => removeItem(productId));
        });

        function updateQuantity(productId, change) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&change=${change}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Error updating quantity');
                }
            });
        }

        function removeItem(productId) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Error removing item');
                }
            });
        }
    });
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
