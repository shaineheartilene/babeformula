<?php
require_once('tcpd/tcpdf.php'); // Corrected path to TCPDF

// Database connection
include('connect.php');

// Fetch total sales data for the report
$salesQuery = "
    SELECT 
        p.product_name,
        p.category,
        p.price,
        SUM(od.quantity) AS total_sales,
        SUM(od.quantity * od.price) AS total_product_sales
    FROM products p
    JOIN order_details od ON p.id = od.product_id
    GROUP BY p.id
";
$salesResult = $conn->query($salesQuery);
$totalRevenue = 0;

// Create a new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Product Sales Report', 0, 1, 'C');
$pdf->Ln(5);

// Table Headers
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(60, 10, 'Product Name', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Category', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Total Sales (Qty)', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Total Sales (₱)', 1, 0, 'C', 1);
$pdf->Cell(30, 10, 'Price (₱)', 1, 1, 'C', 1);

// Table Content
while ($row = $salesResult->fetch_assoc()) {
    $totalRevenue += $row['total_product_sales'];
    $pdf->Cell(60, 10, $row['product_name'], 1);
    $pdf->Cell(40, 10, $row['category'], 1);
    $pdf->Cell(30, 10, $row['total_sales'], 1, 0, 'C');
    $pdf->Cell(40, 10, '₱' . number_format($row['total_product_sales'], 2), 1, 0, 'C');
    $pdf->Cell(30, 10, '₱' . number_format($row['price'], 2), 1, 1, 'C');
}

// Total Revenue
$pdf->Ln(5);
$pdf->Cell(160, 10, 'Total Revenue:', 0, 0, 'R');
$pdf->Cell(30, 10, '₱' . number_format($totalRevenue, 2), 1, 1, 'C');

// Output PDF as download
$pdf->Output('product_report.pdf', 'D');
?>
