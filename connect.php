<?php
$conn = new mysqli('localhost', 'root', '', 'dbbabes');

// Check connection
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}
?>
