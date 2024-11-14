<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['username']) || !isset($_POST['product_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$username = $_SESSION['username'];
$product_id = $_POST['product_id'];

// Delete the item from the cart
$query = "DELETE FROM cart WHERE username = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $username, $product_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>

