<?php
session_start();
include 'connect.php'; 

$adminUsername = $adminPassword = "";
$adminUsernameErr = $adminPasswordErr = $adminLoginErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["admin_username"])) {
        $adminUsernameErr = "Username is required";
    } else {
        $adminUsername = htmlspecialchars($_POST["admin_username"]);
    }

    if (empty($_POST["admin_password"])) {
        $adminPasswordErr = "Password is required";
    } else {
        $adminPassword = htmlspecialchars($_POST["admin_password"]);
    }

    if (empty($adminUsernameErr) && empty($adminPasswordErr)) {
        $adminUsername = $conn->real_escape_string($adminUsername);
       
        $query = "SELECT * FROM users WHERE username = '$adminUsername' AND role = 'admin'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dbPassword = $row['password'];

            
            if (password_verify($adminPassword, $dbPassword)) {
                $_SESSION["username"] = $adminUsername;
                $_SESSION["role"] = 'admin'; 
                header("Location: admin-dashboard.php");
                exit();
            } else {
                $adminLoginErr = "Invalid username or password.";
            }
        } else {
            $adminLoginErr = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="login-style.css">
</head>
<body>
        <div class="logo-container">
            <img src="babelogo.png" alt="Logo" class="logo">
        </div>
        <br><br>
        
    <div class="login-container">
        <h1 class="login-header">Admin Login</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <span class="error"><?php echo $adminLoginErr; ?></span>

            <div class="input-group">
                <input type="text" name="admin_username" value="<?php echo $adminUsername; ?>" placeholder="Username">
                <span class="error"><?php echo $adminUsernameErr; ?></span>
            </div>

            <div class="input-group">
                <input type="password" name="admin_password" placeholder="Password">
                <span class="error"><?php echo $adminPasswordErr; ?></span>
            </div>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
