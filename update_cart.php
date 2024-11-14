<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['username']) || !isset($_POST['product_id']) || !isset($_POST['change'])) {
    echo json_encode(['success' => false]);
    exit();
}

$username = $_SESSION['username'];
$product_id = $_POST['product_id'];
$change = (int) $_POST['change'];

// Update the cart quantity
$query = "UPDATE cart SET quantity = quantity + ? WHERE username = ? AND product_id = ? AND quantity + ? >= 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("isii", $change, $username, $product_id, $change);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
