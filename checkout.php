<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch cart items for confirmation with more product info
$query = "SELECT product_name, price, products.image_path, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total price and store cart data in an array
$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
    $cart_items[] = $row; // Save each row to an array
}

$stmt->close();

// Fetch user's shipping address
$addressQuery = "SELECT name, contact, email, province, city, barangay, address_details FROM user_addresses WHERE username = ?";
$addressStmt = $conn->prepare($addressQuery);
$addressStmt->bind_param("s", $username);
$addressStmt->execute();
$shippingAddress = $addressStmt->get_result()->fetch_assoc();

$addressStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout-style.css">
</head>
<body>

<div class="checkout-container">
    <h1>Checkout</h1>

    <div class="checkout-content">
        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            <table class="checkout-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?= htmlspecialchars($item['image_path']); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" class="checkout-image">
                            <?php else: ?>
                                <img src="placeholder.jpg" alt="No image available" class="checkout-image">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['product_name']); ?></td>
                        <td><?= htmlspecialchars($item['quantity']); ?></td>
                        <td>₱<?= number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h3 class="total">Total: ₱<?= number_format($total, 2); ?></h3>

            <div class="checkout-buttons">
                <form action="process_payment.php" method="POST">
                    <button type="submit" class="confirm-btn">Confirm and Pay</button>
                </form>

                <form action="cart.php" method="GET">
                    <button type="submit" class="cancel-btn">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Shipping Address Section -->
        <div class="shipping-address">
            <h2>Shipping Address</h2>
            <?php if ($shippingAddress): ?>
                <p><strong>Name:</strong> <?= htmlspecialchars($shippingAddress['name']); ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($shippingAddress['contact']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($shippingAddress['email']); ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($shippingAddress['address_details'] . ', ' . $shippingAddress['barangay'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['province']); ?></p>
            <?php else: ?>
                <p>No shipping address found. Please update your address in your account settings.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
