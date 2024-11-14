<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];

// Fetch user profile data
$query = "SELECT username, fname, lname, birthday, email, gender, contact FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
} else {
    header("Location: logout.php");
    exit();
}

// Fetch the user's single shipping address
$addressQuery = "SELECT * FROM user_addresses WHERE username = ?";
$addressStmt = $conn->prepare($addressQuery);
$addressStmt->bind_param("s", $username);
$addressStmt->execute();
$address = $addressStmt->get_result()->fetch_assoc(); // Expect only one address

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Save profile updates
    if (isset($_POST['save_profile'])) {
        $contact = $_POST['contact'];

        $updateProfileQuery = "UPDATE users SET contact = ? WHERE username = ?";
        $stmt = $conn->prepare($updateProfileQuery);
        $stmt->bind_param("ss", $contact, $username);
        $stmt->execute();
    }

    // Save or update the shipping address
    if (isset($_POST['save_address'])) {
        // Convert to uppercase before saving
        $name = strtoupper($_POST['address_name']);
        $contact = $_POST['address_contact'];
        $email = $_POST['address_email'];
        $province = strtoupper($_POST['province']);
        $city = strtoupper($_POST['city']);
        $barangay = strtoupper($_POST['barangay']);
        $details = strtoupper($_POST['address_details']); // Street, subdivision, house number, etc.

        if ($address) {
            // Update existing address
            $updateAddressQuery = "UPDATE user_addresses SET name = ?, contact = ?, email = ?, province = ?, city = ?, barangay = ?, address_details = ? WHERE username = ?";
            $stmt = $conn->prepare($updateAddressQuery);
            $stmt->bind_param("ssssssss", $name, $contact, $email, $province, $city, $barangay, $details, $username);
        } else {
            // Insert new address
            $insertAddressQuery = "INSERT INTO user_addresses (username, name, contact, email, province, city, barangay, address_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertAddressQuery);
            $stmt->bind_param("ssssssss", $username, $name, $contact, $email, $province, $city, $barangay, $details);
        }
        $stmt->execute();

        // Refresh the page to reflect changes
        header("Location: account.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Babe Account</title>
    <link rel="stylesheet" href="account-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Sidebar Section -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="babelogo.png" alt="Babe Logo" class="logo">
        </div>

        <ul class="menu">
            <li><a href="home.php"><i class="fas fa-home"></i> <span>Homepage</span></a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="account.php" class="active"><i class="fas fa-user"></i> <span>Babe Account</span></a></li>
            <li><a href="purchased.php"><i class="fas fa-shopping-bag"></i> <span>Babe Purchased</span></a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span>Babe Cart</span></a></li>
            <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></a></li>
        </ul>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

       <!-- navbar -->
    <div class="navbar">
    <a href="#" onclick="toggleProfileDropdown(event)" class="profile-icon"><i class="fas fa-user"></i></a>
                <div class="profile-dropdown" id="profile-dropdown">
                    <span> <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                </div>
                <h2 class="navbar-title">BABE ACCOUNT</h2>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <div class="account-section">
            <!-- About You Section -->
            <form method="post" class="card about-you">
                <h3>About You</h3>
                <p><strong>Username:</strong> @<?= $userData['username']; ?></p>
                <p><strong>Name:</strong> <?= $userData['fname'] . ' ' . $userData['lname']; ?></p>
                <p><strong>Birthday:</strong> <?= $userData['birthday']; ?></p>
                <p><strong>Email:</strong> <?= $userData['email']; ?></p>
                <p><strong>Gender:</strong> <?= $userData['gender']; ?></p>
                <p><strong>Contact Number:</strong> <input type="text" name="contact" value="<?= $userData['contact']; ?>" required></p>
                <div class="profile-buttons">
                    <button type="submit" name="save_profile" class="save-btn">Save Changes</button>
                </div>
            </form>

            <!-- Address Book Section -->
            <div class="card address-book">
                <h3>Shipping Address</h3>
                <?php if ($address): ?>
                    <div class="address-item">
                        <p><strong>Name:</strong> <?= htmlspecialchars($address['name']); ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($address['contact']); ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($address['email']); ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($address['province'] . ', ' . $address['city'] . ', ' . $address['barangay'] . ', ' . $address['address_details']); ?></p>
                        <button class="edit-btn" id="editAddressBtn">Edit</button>
                    </div>
                <?php else: ?>
                    <!-- Show the "Add Address" form if no address exists -->
                    <button class="add-btn" id="addAddressBtn">Add New Address</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Section -->
    <div id="addressModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <form method="post" class="card address-form">
                <h3>Shipping Details</h3>
                <input type="hidden" name="address_id" id="address_id" value="<?= $address['id'] ?? ''; ?>">
                <p><strong>Name:</strong> <input type="text" name="address_name" id="address_name" value="<?= htmlspecialchars($address['name'] ?? ''); ?>" required></p>
                <p><strong>Contact Number:</strong> <input type="text" name="address_contact" id="address_contact" value="<?= htmlspecialchars($address['contact'] ?? ''); ?>" required></p>
                <p><strong>Email:</strong> <input type="email" name="address_email" id="address_email" value="<?= htmlspecialchars($address['email'] ?? ''); ?>" required></p>
                <p><strong>Province:</strong> <input type="text" name="province" id="province" value="<?= htmlspecialchars($address['province'] ?? ''); ?>" required></p>
                <p><strong>City:</strong> <input type="text" name="city" id="city" value="<?= htmlspecialchars($address['city'] ?? ''); ?>" required></p>
                <p><strong>Barangay:</strong> <input type="text" name="barangay" id="barangay" value="<?= htmlspecialchars($address['barangay'] ?? ''); ?>" required></p>
                <p><strong>Details (Street, Subdivision, etc.):</strong> <input type="text" name="address_details" id="address_details" value="<?= htmlspecialchars($address['address_details'] ?? ''); ?>" required></p>
                <div class="profile-buttons">
                    <button type="submit" name="save_address" class="save-btn">Save Address</button>
                    <button type="button" class="cancel-btn" id="cancelAddressForm">Cancel</button>
                </div>
            </form>
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

    const addAddressBtn = document.getElementById('addAddressBtn');
    const editAddressBtn = document.getElementById('editAddressBtn');
    const addressModal = document.getElementById('addressModal');
    const closeModal = document.getElementById('closeModal');
    const cancelAddressForm = document.getElementById('cancelAddressForm');

    // Hide the modal initially
    addressModal.style.display = 'none';
// Open the modal and ensure it's centered
function openModal() {
    addressModal.style.display = 'flex';  // Ensure 'flex' is used for centering
}

// Show the modal when "Add Address" or "Edit" is clicked
if (addAddressBtn) {
    addAddressBtn.addEventListener('click', () => {
        openModal();
    });
}

if (editAddressBtn) {
    editAddressBtn.addEventListener('click', () => {
        openModal();
    });
}

    // Hide the modal when "Close" or "Cancel" is clicked
    closeModal.addEventListener('click', () => {
        addressModal.style.display = 'none';
    });

    cancelAddressForm.addEventListener('click', () => {
        addressModal.style.display = 'none';
    });

    // Close the modal if clicked outside of it
    window.onclick = function(event) {
        if (event.target == addressModal) {
            addressModal.style.display = 'none';
        }
    };
    </script>
</body>
</html>
