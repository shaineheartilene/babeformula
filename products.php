<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Check if a category is selected
$category = isset($_GET['category']) ? $_GET['category'] : '';

// SQL query to get products based on selected category
if (!empty($category)) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->bind_param("s", $category);
} else {
    // If no category is selected, show all products
    $stmt = $conn->prepare("SELECT * FROM products");
}

$stmt->execute();
$result = $stmt->get_result();

// Handle add to cart action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1; // Default quantity to 1, can be changed later

    // Check if the product is already in the user's cart
    $cartCheckStmt = $conn->prepare("SELECT * FROM cart WHERE username = ? AND product_id = ?");
    $cartCheckStmt->bind_param("si", $username, $product_id);
    $cartCheckStmt->execute();
    $cartResult = $cartCheckStmt->get_result();

    if ($cartResult->num_rows > 0) {
        // If the product is already in the cart, update the quantity
        $updateStmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE username = ? AND product_id = ?");
        $updateStmt->bind_param("si", $username, $product_id);
        $updateStmt->execute();
    } else {
        // If the product is not in the cart, insert it
        $insertStmt = $conn->prepare("INSERT INTO cart (username, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sii", $username, $product_id, $quantity);
        $insertStmt->execute();
    }

    // Redirect to cart page or refresh products page
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="products-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Sidebar Section -->
<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <img src="babelogo.png" alt="Babe Logo" class="logo">
    </div>
    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
    <ul class="menu">
        <li><a href="home.php" class="<?= ($currentPage == 'home.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> <span>Homepage</span></a></li>
        <li><a href="products.php" class="<?= ($currentPage == 'products.php') ? 'active' : '' ?>"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="account.php" class="<?= ($currentPage == 'account.php') ? 'active' : '' ?>"><i class="fas fa-user"></i> <span>Babe Account</span></a></li>
        <li><a href="purchased.php" class="<?= ($currentPage == 'purchased.php') ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> <span>Babe Purchased</span></a></li>
        <li><a href="cart.php" class="<?= ($currentPage == 'cart.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> <span>Babe Cart</span></a></li>
        <li><a href="logout.php" class="logout <?= ($currentPage == 'logout.php') ? 'active' : '' ?>"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
    </ul>
</div>

<!-- Sidebar Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Navbar Section -->
<div class="navbar">
    <h2 class="navbar-title">BABE PRODUCTS</h2>
    <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
    <div class="profile-dropdown" id="profile-dropdown">
        <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
    </div>
</div>

<!-- Main Content Section -->
<div class="main-content">
    <!-- Search Bar and Dropdown Section -->
    <div class="search-container">
        <div class="search-bar-wrapper">
            <input type="text" class="search-bar" placeholder="Babe search....">
            <i class="fas fa-search search-icon"></i>
        </div>
        
        <form method="GET" action="products.php">
            <div class="dropdown-wrapper">
                <select name="category" class="dropdown" onchange="this.form.submit()">
                    <option value="">All Products</option>
                    <option value="Shampoo" <?= (isset($_GET['category']) && $_GET['category'] == 'Shampoo') ? 'selected' : '' ?>>Shampoo</option>
                    <option value="Conditioner" <?= (isset($_GET['category']) && $_GET['category'] == 'Conditioner') ? 'selected' : '' ?>>Conditioner</option>
                    <option value="Hair Spray" <?= (isset($_GET['category']) && $_GET['category'] == 'Hair Spray') ? 'selected' : '' ?>>Hair Spray</option>
                    <option value="Hair Mask" <?= (isset($_GET['category']) && $_GET['category'] == 'Hair Mask') ? 'selected' : '' ?>>Hair Mask</option>
                    <option value="Set" <?= (isset($_GET['category']) && $_GET['category'] == 'Set') ? 'selected' : '' ?>>Set</option>
                    <option value="Merch" <?= (isset($_GET['category']) && $_GET['category'] == 'Merch') ? 'selected' : '' ?>>Merch</option>
                </select>
                <i class="fas fa-chevron-down dropdown-icon"></i>
            </div>
        </form>
    </div>

    <!-- Display Selected Category Name -->
    <h2 class="category-title"><?= !empty($category) ? htmlspecialchars(ucfirst($category)) : 'All Products' ?></h2>

    <!-- Product Category Section with Dynamic Content -->
    <div class="product-category-box">
        <div class="product-grid">
            <?php
            // Display the products
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $outOfStock = $row['stocks'] == 0;  // Check if out of stock
                    echo '
                        <div class="product-card">
                            <img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['product_name']) . '" width="100">
                            <h3>' . htmlspecialchars($row['product_name']) . '</h3>
                            <h3>' . htmlspecialchars($row['size']) . '</h3>
                            <p class="price">₱' . htmlspecialchars($row['price']) . '</p>
                            <p>Stocks: ' . ($outOfStock ? 'Out of Stock' : htmlspecialchars($row['stocks'])) . '</p>';
                    
                    if (!$outOfStock) {
                        echo '<form method="POST" action="products.php">
                                <input type="hidden" name="product_id" value="' . htmlspecialchars($row['id']) . '">
                                <button class="cart-btn"><i class="fas fa-shopping-cart"></i></button>
                              </form>';
                    } else {
                        echo '<button class="cart-btn" disabled>Out of Stock</button>';
                    }

                    echo '</div>';
                }
            } else {
                echo "<p>No products available in this category.</p>";
            }
            ?>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebarToggle');
    const navbar = document.querySelector('.navbar');

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        navbar.classList.toggle('expanded');
    });

    function toggleProfileDropdown(event) {
        event.preventDefault();
        document.getElementById("profile-dropdown").classList.toggle("active");
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById("profile-dropdown");
        const profileIcon = document.querySelector(".profile-icon");
        if (!profileIcon.contains(event.target)) {
            dropdown.classList.remove("active");
        }
    });
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
