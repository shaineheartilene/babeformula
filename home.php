<?php
session_start();
include 'connect.php';
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Home </title>
    <link rel="stylesheet" href="home-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- Sidebar Section -->
    <div class="sidebar" id="sidebar">

        <div class="logo-container">
            <img src="babelogo.png" alt="Babe Logo" class="logo">
        </div>

        
        <?php
            $currentPage = basename($_SERVER['PHP_SELF']); 
        ?>
        
        <ul class="menu">
            <li><a href="home.php" class="<?= ($currentPage == 'home.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> <span>Homepage</span></a></li>
            <li><a href="products.php" class="<?= ($currentPage == 'products.php') ? 'active' : '' ?>"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="account.php" class="<?= ($currentPage == 'account.php') ? 'active' : '' ?>"><i class="fas fa-user"></i> <span>Babe Account</span></a></li>
            <li><a href="purchased.php" class="<?= ($currentPage == 'purchased.php') ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> <span>Babe Purchased</span></a></li>
            <li><a href="cart.php" class="<?= ($currentPage == 'cart.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> <span>Babe Cart</span></a></li>
            <li><a href="logout.php" class="logout <?= ($currentPage == 'logout.php') ? 'active' : '' ?>"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
        </ul>

    </div>

    <!-- sidebar-->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- navbar -->
    <div class="navbar">
    <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
                <div class="profile-dropdown" id="profile-dropdown">
                    <span> <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                </div>
    </div>

    <!-- Main -->
    <div class="main-content">

        <div class="hero-section">
            <div class="hero-content">
                <bold><h1>Hair care <br>solutions.<br>Crafted with <br>intention. ♥</h1></bold>

                <br><p>Come on, babe. Love yourself a bit (or a lot) better! Experience the power of intentionally crafted products for your hair's every need with Babe Formula. You deserve nothing less!</p>
                
                <br><a href="learn-more.php" class="btn-learn-more">Learn More!</a>
            </div>

            <div class="hero-image">
                <img src="ceo-paula.png" alt="CEO-Paula" />
            </div>

        </div>


        <!-- Reviews -->
        <div class="reviews-section">

            <div class="review-card">
                <img src="reviews/viy.png" alt="viy">
                <p>Sobrang hiyang ako sa Avo Babe! Ang ganda sa buhok at ang bango. 10/10 ♥♥</p>
                <span>@viycortez</span>
            </div>

            <div class="review-card">
                <img src="reviews/blythe.png" alt="blythe">
                <p>May shampoo po akong gamit sainyo - Babe Bonbon Duo! Super daming nag sasabi ang bango-bango ng ulo ko!🌸</p>
                <span>@blythe</span>
            </div>

            <div class="review-card">
                <img src="reviews/mpacquiaoo.png" alt="mpacquiaoo">
                <p>The shampoo and conditioner smells amazing! The hair mask tamed my frizz and it was never like that before. Hehehe. In other words, I really loved all of your products!♥</p>
                <span>@mpacquiaoo</span>
            </div>

            <div class="review-card">
                <img src="reviews/kate.png" alt="kate">
                <p>I am loving them so much! It makes my hair so soft and shiny. Bye bye frizzy, and damaged curly hair. Super effective po talaga.🌸</p>
                <span>@valdezkate_</span>
            </div>

        </div>
        
        <?php include('footer.php'); ?>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebarToggle');
        const mainContent = document.querySelector('.main-content');
        const navbar = document.querySelector('.navbar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbar.classList.toggle('expanded');
        });
        function toggleProfileDropdown(event) {
            event.preventDefault();
            var dropdown = document.getElementById("profile-dropdown");
            dropdown.classList.toggle("active");
        }

        document.addEventListener('click', function(event) {
            var dropdown = document.getElementById("profile-dropdown");
            var profileIcon = document.querySelector(".profile-icon");
            if (!profileIcon.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });
    </script>

</body>
</html>