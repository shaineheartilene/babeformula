<?php
require 'connect.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Product_Sales_Report_" . date("Y-m-d") . ".xls");

// Start the table
echo "<table border='1'>";
echo "<tr><th>Product Name</th><th>Category</th><th>Total Sales (Quantity)</th><th>Total Product Sales (₱)</th><th>Price (₱)</th><th>Year</th></tr>";

$totalYearlyRevenue = 0;
$yearlySalesQuery = "
    SELECT 
        p.product_name,
        p.category,
        p.price,
        YEAR(o.order_date) AS year,
        SUM(od.quantity) AS total_sales,
        SUM(od.quantity * od.price) AS total_product_sales
    FROM products p
    JOIN order_details od ON p.id = od.product_id
    JOIN orders o ON od.order_id = o.id
    GROUP BY p.id, year
    ORDER BY year DESC
";
$yearlySalesResult = $conn->query($yearlySalesQuery);

if ($yearlySalesResult->num_rows > 0) {
    while ($row = $yearlySalesResult->fetch_assoc()) {
        $totalYearlyRevenue += $row['total_product_sales'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_sales']) . "</td>";
        echo "<td>₱" . number_format($row['total_product_sales'], 2) . "</td>";
        echo "<td>₱" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
        echo "</tr>";
    }
    echo "<tr><td colspan='3'><strong>Total Revenue:</strong></td><td colspan='3'><strong>₱" . number_format($totalYearlyRevenue, 2) . "</strong></td></tr>";
} else {
    echo "<tr><td colspan='6'>No sales data available</td></tr>";
}

echo "</table>";
$conn->close();
?>
