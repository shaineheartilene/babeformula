<?php
session_start();
include 'connect.php';

$username = $password = "";
$usernameErr = $passwordErr = $loginErr = "";

$successMessage = "";
if (isset($_SESSION["success_message"])) {
    $successMessage = $_SESSION["success_message"];
    unset($_SESSION["success_message"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = htmlspecialchars($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = htmlspecialchars($_POST["password"]);
    }

    if (empty($usernameErr) && empty($passwordErr)) {
        $username = $conn->real_escape_string($username);

        
        $query = "SELECT password, verif_status FROM users WHERE username = '$username'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dbPassword = $row['password'];
            $verif_status = $row['verif_status'];

            
            if ($verif_status == 1) {
                
                if (password_verify($password, $dbPassword)) {
                    $_SESSION["username"] = $username;
                    header("Location: home.php");
                    exit();
                } else {
                    $loginErr = "Invalid username or password.";
                }
            } else {
                $loginErr = "Please verify your email before logging in.";
            }
        } else {
            $loginErr = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="login-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <div class="logo-container">
        <img src="babelogo.png" alt="Logo" class="logo">
    </div>
    <br><br>

    <div class="login-container">
        <br><br><h1 class="login-header">Log In</h1><br><br>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php if ($successMessage): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>
            <span class="error"><?php echo $loginErr; ?></span>

            <div class="input-group">
                <input type="text" id="username" name="username" value="<?php echo $username; ?>">
                <span class="error"><?php echo $usernameErr; ?></span>
                <label for="username"><center>Username</center></label><br>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" value="<?php echo $password; ?>">
                <span class="error"><?php echo $passwordErr; ?></span>
                <label for="password"><center>Password</center></label>
            </div>

            <br><br>
            <input type="submit" value="LOGIN">
            <br><br>

            <p class="forgot-pass">
                <a href="forgotpass.php">
                    Forgot Password?
                    <i class="fa-duotone fa-solid fa-unlock"></i>
                </a>

                &nbsp;&nbsp;&nbsp;
                <a href="register.php" class="register-link">Sign Up</a>
            </p>
        </form>
    </div>
</body>
</html>