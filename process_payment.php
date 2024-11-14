<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Start a database transaction to ensure all operations are executed as a single unit
$conn->begin_transaction();

try {
    // Fetch cart items for processing
    $query = "SELECT product_id, quantity FROM cart WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
    $stmt->close();

    if (empty($cart_items)) {
        throw new Exception("No items in cart.");
    }

    // Calculate the total and create the order record
    $total = 0;
    foreach ($cart_items as $item) {
        $productQuery = "SELECT price FROM products WHERE id = ?";
        $productStmt = $conn->prepare($productQuery);
        $productStmt->bind_param("i", $item['product_id']);
        $productStmt->execute();
        $productResult = $productStmt->get_result()->fetch_assoc();
        $total += $productResult['price'] * $item['quantity'];
        $productStmt->close();
    }

    $orderQuery = "INSERT INTO orders (username, total_amount, order_date) VALUES (?, ?, NOW())";
    $orderStmt = $conn->prepare($orderQuery);
    $orderStmt->bind_param("sd", $username, $total);
    $orderStmt->execute();
    $order_id = $orderStmt->insert_id;
    $orderStmt->close();

    // Insert order details and update product stock
    foreach ($cart_items as $item) {
        $orderDetailQuery = "INSERT INTO order_details (order_id, product_id, quantity) VALUES (?, ?, ?)";
        $orderDetailStmt = $conn->prepare($orderDetailQuery);
        $orderDetailStmt->bind_param("iii", $order_id, $item['product_id'], $item['quantity']);
        $orderDetailStmt->execute();
        $orderDetailStmt->close();

        // Update stock in products table
        $updateStockQuery = "UPDATE products SET stocks = stocks - ? WHERE id = ?";
        $updateStockStmt = $conn->prepare($updateStockQuery);
        $updateStockStmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $updateStockStmt->execute();
        $updateStockStmt->close();
    }

    // Clear the user's cart
    $clearCartQuery = "DELETE FROM cart WHERE username = ?";
    $clearCartStmt = $conn->prepare($clearCartQuery);
    $clearCartStmt->bind_param("s", $username);
    $clearCartStmt->execute();
    $clearCartStmt->close();

    // Commit transaction
    $conn->commit();

    // Redirect to a success page
    header("Location: purchased.php");
    exit();

} catch (Exception $e) {
    // Roll back transaction if any error occurs
    $conn->rollback();
    echo "Error processing payment: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
</head>
<body>
    <h1>Payment Successful</h1>
    <p>Thank you for your purchase! Your order has been placed successfully.</p>
    <a href="purchased.php">View Order History</a>
</body>
</html>
