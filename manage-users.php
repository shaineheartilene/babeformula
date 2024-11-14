Shaine Heartilene Palo Amargo
<?php
session_start();
include 'connect.php';

// Redirect to login if not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit();
}

// Fetch all users
$query = "SELECT username, CONCAT(fname, ' ', lname) AS fullname, birthday, gender, email, contact FROM users WHERE role = 'user'";
$result = $conn->query($query);

// Handle user deletion
if (isset($_GET['delete'])) {
    $usernameToDelete = $_GET['delete'];
    $deleteQuery = "DELETE FROM users WHERE username = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('s', $usernameToDelete);
    if ($stmt->execute()) {
        header("Location: manage-users.php");
        exit();
    } else {
        echo "Error deleting user.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="manage-user-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Sidebar Section -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="babelogo.png" alt="Babe Logo" class="logo">
        </div>

        <ul class="menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="admin-products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="manage-users.php" class="active"><i class="fas fa-users"></i> <span>Users</span></a></li>
            <li><a href="manage-orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
            <li><a href="message.php"><i class="fas fa-comments"></i> <span>Messages</span></a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
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
                <h2 class="navbar-title">BABE USERS</h2>
    </div>

    <div class="main-content">

        <!-- Total Users Card -->
        <div class="total-users">
            <h2>Total Users: </h2>
            <p><?php echo $result->num_rows; ?></p>
        </div>

        <!-- Users Table -->
        <table class="users-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Birthday</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>@<?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['birthday']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td>
                                <a href="manage-users.php?delete=<?php echo htmlspecialchars($row['username']); ?>" class="delete-btn"><i class="fas fa-times"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <script>
        // Sidebar toggle script
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebarToggle');
        const mainContent = document.querySelector('.main-content');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    </script>

</body>
</html>