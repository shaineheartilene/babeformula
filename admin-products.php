<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit();
}

// Database connection
include('connect.php');

// Check if viewing the report or inventory
$view = isset($_GET['view']) ? $_GET['view'] : '';

// Fetch data based on selected view
if ($view === 'report') {
    $reportQuery = "SELECT product_name, category, price, size, stocks, description FROM products";
    $reportResult = $conn->query($reportQuery);
} elseif ($view === 'inventory') {
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
    
    $inventoryQuery = "SELECT product_name, category, price, stocks, size, description 
                       FROM products " . 
                       ($categoryFilter ? "WHERE category = ?" : "");
    
    if ($categoryFilter) {
        $stmt = $conn->prepare($inventoryQuery);
        $stmt->bind_param("s", $categoryFilter);
        $stmt->execute();
        $inventoryResult = $stmt->get_result();
    } else {
        $inventoryResult = $conn->query($inventoryQuery);
    }
    
    $categoryList = $conn->query("SELECT DISTINCT category FROM products");
} else {
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    if (!empty($category)) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->bind_param("s", $category);
    } else {
        $stmt = $conn->prepare("SELECT * FROM products");
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $categoryQuery = "SELECT DISTINCT category FROM products";
    $categoryResult = $conn->query($categoryQuery);
}



// Handle product insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit_id'])) {
    $productName = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $stocks = $_POST['stocks'];
    $description = $_POST['description'];
    
    $targetDir = "products/";
    $imageName = basename($_FILES["product_image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $imageName;
    
    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath)) {
        $insertStmt = $conn->prepare("INSERT INTO products (product_name, category, price, size, image_path, stocks, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param('ssdssis', $productName, $category, $price, $size, $targetFilePath, $stocks, $description);
        $insertStmt->execute();

        header("Location: admin-products.php");
        exit();
    } else {
        echo "File upload error. Please check directory permissions.";
    }
}

// Handle product update
if (isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $productName = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $stocks = $_POST['stocks'];
    $description = $_POST['description'];
    
    $imagePath = null;
    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["product_image"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $imageName;
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath)) {
            $imagePath = $targetFilePath;
        }
    }
    
    if ($imagePath) {
        $updateStmt = $conn->prepare("UPDATE products SET product_name = ?, category = ?, price = ?, size = ?, image_path = ?, stocks = ?, description = ? WHERE id = ?");
        $updateStmt->bind_param("ssdsisii", $productName, $category, $price, $size, $imagePath, $stocks, $description, $editId);
    } else {
        $updateStmt = $conn->prepare("UPDATE products SET product_name = ?, category = ?, price = ?, size = ?, stocks = ?, description = ? WHERE id = ?");
        $updateStmt->bind_param("ssdissi", $productName, $category, $price, $size, $stocks, $description, $editId);
    }
    
    $updateStmt->execute();
    header("Location: admin-products.php");
    exit();
}

// Handle product delete
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $deleteStmt->bind_param("i", $deleteId);
    $deleteStmt->execute();
    
    header("Location: admin-products.php");
    exit();
}

// Handle product update
if (isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $productName = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $stocks = $_POST['stocks'];
    $description = $_POST['description'];
    
    // Check if a new image is uploaded
    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["product_image"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $imageName;
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath)) {
            $imagePath = $targetFilePath;
        }
    }
    
    // Retrieve current image path if no new image was uploaded or if upload failed
    if (empty($imagePath)) {
        $imageResult = $conn->query("SELECT image_path FROM products WHERE id = $editId");
        $imageRow = $imageResult->fetch_assoc();
        $imagePath = $imageRow['image_path'];
    }
    
    // Update the product with the existing or new image path
    $updateStmt = $conn->prepare("UPDATE products SET product_name = ?, category = ?, price = ?, size = ?, image_path = ?, stocks = ?, description = ? WHERE id = ?");
    $updateStmt->bind_param("ssdsisii", $productName, $category, $price, $size, $imagePath, $stocks, $description, $editId);
    $updateStmt->execute();
    
    header("Location: admin-products.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="admin-products-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <img src="babelogo.png" alt="Babe Logo" class="logo">
    </div>
    <ul class="menu">
        <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="admin-products.php" class="active"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="manage-users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
        <li><a href="manage-orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="message.php"><i class="fas fa-comments"></i> <span>Messages</span></a></li>
        <li><a href="reviews.php"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
    </ul>
</div>

<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="navbar">
    <h2 class="navbar-title">Manage Products</h2>
</div>

<div class="main-content">
    <div class="search-container">
        <a href="admin-products.php?"><button class="add-product-btn">Products</button></a>
        <a href="admin-products.php?view=report"><button class="add-product-btn">Product Report</button></a>
        <a href="admin-products.php?view=inventory"><button class="add-product-btn">Inventory</button></a>
</div>


    <?php if ($view === 'report'): ?>
    <!-- Product Report Section -->
    <div class="report-section">
        <h3>Product Sales Report</h3>
        <a href="generate-product-sales-report.php" class="add-product-btn">Download Sales Report</a>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Total Sales (Quantity)</th>
                    <th>Total Product Sales (₱)</th>
                    <th>Price (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch total sales data
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
                ?>

                <?php if ($salesResult->num_rows > 0): ?>
                    <?php while ($row = $salesResult->fetch_assoc()): ?>
                        <?php $totalRevenue += $row['total_product_sales']; ?>
                        <tr>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= htmlspecialchars($row['total_sales']) ?></td>
                            <td>₱<?= number_format($row['total_product_sales'], 2) ?></td>
                            <td>₱<?= number_format($row['price'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <!-- Display Total Revenue at the end of the report -->
                    <tr>
                        <td colspan="3" style="font-weight: bold;">Total Revenue:</td>
                        <td colspan="2" style="font-weight: bold;">₱<?= number_format($totalRevenue, 2) ?></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="5">No sales data available</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <?php elseif ($view === 'inventory'): ?>
        <!-- Inventory Section with Sort Button -->
        <div class="inventory-section">
            <h3>Inventory</h3>
            <div style="text-align: right; margin-bottom: 10px;">
                <form method="GET" action="admin-products.php">
                    <input type="hidden" name="view" value="inventory">
                    <label for="category">Sort by Category: </label>
                    <select name="category" id="category" onchange="this.form.submit()">
                        <option value="">All</option>
                        <?php while ($cat = $categoryList->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($cat['category']) ?>" 
                                <?= $cat['category'] === $categoryFilter ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price (₱)</th>
                        <th>Size</th>
                        <th>Stocks</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($inventoryResult->num_rows > 0): ?>
                        <?php while ($row = $inventoryResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td>₱<?= number_format($row['price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['size']) ?> ml</td>
                                <td><?= htmlspecialchars($row['stocks']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No products available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <!-- Default Product Management Section -->
        <center><h2 class="category-title"><?= !empty($category) ? htmlspecialchars(ucfirst($category)) : 'All Products' ?></h2></center>
        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <button class="add-product-btn" id="openModalBtn">Add Product</button>
        </div>

        <div class="product-grid">
            
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                        <div class="product-card">
                            <img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['product_name']) . '" width="100">
                            <h3>' . htmlspecialchars($row['product_name']) . '</h3>
                            <h3>' . htmlspecialchars($row['size']) . ' ml</h3>
                            <h3>Stocks: ' . htmlspecialchars($row['stocks']) . '</h3>
                            <p class="price">₱' . htmlspecialchars($row['price']) . '</p>
                            <p>' . htmlspecialchars($row['description']) . '</p>
                            <button class="edit-btn" onclick="openEditModal(' . $row['id'] . ', \'' . $row['product_name'] . '\', \'' . $row['category'] . '\', ' . $row['price'] . ', \'' . $row['size'] . '\', \'' . $row['stocks'] . '\', \'' . $row['description'] . '\')">Edit</button>
                            
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row['id'] . '">
                                <button type="submit" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</button>
                            </form>
                        </div>
                    ';
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for Adding Product -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <h3>Add New Product</h3>
        <form action="admin-products.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <select name="category" required>
                <option value="" disabled selected>Select Category</option>
                <option value="Shampoo">Shampoo</option>
                <option value="Conditioner">Conditioner</option>
                <option value="Hair Spray">Hair Spray</option>
                <option value="Hair Mask">Hair Mask</option>
                <option value="Set">Set</option>
                <option value="Merch">Merch</option>
            </select>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="text" name="size" placeholder="Size (ml)" required>
            <input type="number" name="stocks" placeholder="Stocks" required>
            <textarea name="description" placeholder="Product Description"></textarea>
            <input type="file" name="product_image" accept="image/*" required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</div>

<!-- Modal for Editing Product -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeEditModalBtn">&times;</span>
        <h3>Edit Product</h3>
        <form action="admin-products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="text" name="product_name" id="edit_product_name" placeholder="Product Name" required>
            <select name="category" id="edit_category" required>
                <option value="" disabled>Select Category</option>
                <option value="Shampoo">Shampoo</option>
                <option value="Conditioner">Conditioner</option>
                <option value="Hair Spray">Hair Spray</option>
                <option value="Hair Mask">Hair Mask</option>
                <option value="Set">Set</option>
                <option value="Merch">Merch</option>
            </select>
            <input type="number" step="0.01" name="price" id="edit_price" placeholder="Price" required>
            <input type="text" name="size" id="edit_size" placeholder="Size (ml)" required>
            <input type="number" name="stocks" id="edit_stocks" placeholder="Stocks" required>
            <textarea name="description" id="edit_description" placeholder="Product Description"></textarea>
            <input type="file" name="product_image" accept="image/*">
            <button type="submit">Update Product</button>
        </form>
    </div>
</div>


<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const navbar = document.querySelector('.navbar');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        navbar.classList.toggle('expanded');
    });

    const modal = document.getElementById('addProductModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');

    openModalBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
        // JavaScript function to open the Edit Product modal and populate it with product details
    function openEditModal(id, name, category, price, size, stocks, description) {
        // Set the values of the modal input fields with the product details
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_product_name').value = name;
        document.getElementById('edit_category').value = category;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_size').value = size;
        document.getElementById('edit_stocks').value = stocks;
        document.getElementById('edit_description').value = description;

        // Display the Edit Product modal
        document.getElementById('editProductModal').style.display = 'block';
    }

    // Close the Edit Product modal
    document.getElementById('closeEditModalBtn').addEventListener('click', () => {
        document.getElementById('editProductModal').style.display = 'none';
    });

    // Close the Edit Product modal when clicking outside the modal content
    window.addEventListener('click', (event) => {
        if (event.target == document.getElementById('editProductModal')) {
            document.getElementById('editProductModal').style.display = 'none';
        }
    });

    const salesReportBtn = document.getElementById('salesReportBtn');
    const salesReportModal = document.getElementById('salesReportModal');
    const closeSalesReportModalBtn = document.getElementById('closeSalesReportModalBtn');

    // Open the modal
    salesReportBtn.addEventListener('click', () => {
        salesReportModal.style.display = 'block';
    });

    // Close the modal
    closeSalesReportModalBtn.addEventListener('click', () => {
        salesReportModal.style.display = 'none';
    });

    // Close the modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target == salesReportModal) {
            salesReportModal.style.display = 'none';
        }
    });
    
</script>

</body>
</html>

<?php
if ($view !== 'report' && $view !== 'inventory') {
    $stmt->close();
}
$conn->close();
?>
