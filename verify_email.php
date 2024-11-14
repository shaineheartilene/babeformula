<?php

session_start();
include('connect.php'); 

$message = "";  

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $verify_query = "SELECT verify_token, verif_status FROM users WHERE verify_token = '$token' LIMIT 1";
    $verify_query_run = mysqli_query($conn, $verify_query);

    if (mysqli_num_rows($verify_query_run) > 0) {
        $row = mysqli_fetch_array($verify_query_run);
        
        if ($row['verif_status'] == "0") {
            $clicked_token = $row['verify_token'];
            
            $update_query = "UPDATE users SET verif_status='1' WHERE verify_token='$clicked_token' LIMIT 1";
            $update_query_run = mysqli_query($conn, $update_query);

            if ($update_query_run) {
                $message = "Email Verified Successfully! You can now log in.";
            } else {
                $message = "Verification Failed. Please try again.";
            }
        } else {
            $message = "Email Already Verified. You can log in.";
        }
    } else {
        $message = "This Token does not exist.";
    }
} else {
    $message = "Not Allowed";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="verify_email-style.css">
</head>

<body>

<div class="container">
    <p class="message"><?php echo $message; ?></p>
    <?php if ($message == "Email Verified Successfully! You can now log in." || $message == "Email Already Verified. You can log in.") { ?>
        <a href="index.php" class="login-btn">Log In</a>
    <?php } ?>
</div>

</body>
</html>
