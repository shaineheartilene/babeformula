<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>

    <link rel="stylesheet" href="learn-more-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <!-- Sidebar Section -->
    <div class="sidebar" id="sidebar">

        <div class="logo-container">
            <img src="babelogo.png" alt="Babe Logo" class="logo">
        </div>

        <?php
            $currentPage = basename($_SERVER['PHP_SELF']); // Get current page name
        ?>

        <ul class="menu">
            <li><a href="home.php" class="<?= ($currentPage == 'home.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> <span>Homepage</span></a></li>
            <li><a href="products.php" class="<?= ($currentPage == 'products.php') ? 'active' : '' ?>"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="account.php" class="<?= ($currentPage == 'account.php') ? 'active' : '' ?>"><i class="fas fa-user"></i> <span>Babe Account</span></a></li>
            <li><a href="purchased.php" class="<?= ($currentPage == 'purchased.php') ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> <span>Babe Purchased</span></a></li>
            <li><a href="cart.php" class="<?= ($currentPage == 'cart.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> <span>Babe Cart</span></a></li>
            <br>
            <li><a href="logout.php" class="logout <?= ($currentPage == 'logout.php') ? 'active' : '' ?>"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
        </ul>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Section -->
    <div class="navbar">
        <h2 class="navbar-title">ABOUT US</h2>
    </div>
        
    <div class="main-content">
        <div class="about-babe-container">
            <!-- About Babe Formula Section -->
            <section class="about-section">
                <h2>About Babe Formula</h2>
                <p>
                    Babe Formula is a brand dedicated to providing clean, conscious, and effective hair care products. Our products are sulfate-free, paraben-free, and silicone-free, ensuring your hair gets the best care without harmful chemicals. With Babe Formula, you can experience the perfect blend of science and nature, curated to keep your hair healthy and happy.
                </p>
            </section>

            <!-- Our Mission Section -->
            <section class="mission-section">
                <h2>Our Mission</h2>
                <p>
                    At Babe Formula, our mission is to revolutionize the way people care for their hair by offering clean and sustainable hair care solutions. We believe that beauty should never come at the cost of health, which is why we are committed to creating products that are free of harsh chemicals while delivering exceptional results.
                </p>
            </section>

            <!-- Our Vision Section -->
            <section class="vision-section">
                <h2>Our Vision</h2>
                <p>
                    Our vision is to become a global leader in the clean beauty movement by empowering individuals to embrace hair care that is good for them and the planet. We aim to build a community of conscious consumers who prioritize health, sustainability, and beauty that reflects their values.
                </p>
            </section>

            <!-- Meet the Founder Section -->
            <section class="founder-section">
                <h2>Meet the Founder: Paula Terese</h2>
                <p>
                    Babe Formula was founded by Paula Terese, a visionary entrepreneur with a passion for clean beauty. Paula's journey began with her frustration over the lack of effective yet safe hair care products in the market. Determined to fill that gap, she created Babe Formula—a brand that embodies her belief that beauty should never compromise health. With years of research and a commitment to clean ingredients, Paula has successfully built a brand that resonates with conscious consumers worldwide.
                </p>
            </section>

            <!-- Origins of Babe Formula Products -->
            <section class="origin-section">
                <h2>The Origin of Babe Formula Products</h2>
                <p>
                    Babe Formula products are crafted with the finest ingredients sourced globally, ensuring the highest quality. From natural oils and extracts to scientifically-backed formulas, our products are made in certified laboratories to guarantee safety and efficacy. Each product is carefully designed to be sulfate-free, paraben-free, and silicone-free, making it suitable for all hair types. 
                </p>
            </section>

            <!-- Sustainability Section -->
            <section class="sustainability-section">
                <h2>Commitment to Sustainability</h2>
                <p>
                    At Babe Formula, sustainability is at the core of our business. We are committed to reducing our environmental impact by using recyclable packaging, ethically sourcing ingredients, and partnering with environmentally conscious suppliers. We believe that every small step counts toward building a greener future for the planet.
                </p>
            </section>

            <!-- Product Innovation Section -->
            <section class="innovation-section">
                <h2>Product Innovation</h2>
                <p>
                    We constantly strive for innovation in hair care. Our team of scientists and beauty experts work together to create groundbreaking formulas that deliver visible results. Each product undergoes rigorous testing to ensure it is safe, effective, and gentle on both your hair and the environment.
                </p>
            </section>

            <!-- Customer Testimonials Section -->
            <section class="testimonials-section">
                <h2>What Our Customers Are Saying</h2>
                <blockquote>
                    "Babe Formula completely transformed my hair! It's healthier, shinier, and I love that it's free of harsh chemicals." - Sarah L.
                </blockquote>
                <blockquote>
                    "I've struggled to find a hair care line that aligns with my clean beauty philosophy, but Babe Formula is the perfect match. The results speak for themselves." - Emily P.
                </blockquote>
            </section>

        </div>
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
    </script>

</body>
</html>
