<?php
session_start();
include 'connect.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_verify($email, $verify_token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->Username = "babeformula1@gmail.com";  
        $mail->Password = "gfan tirp flob dxxr";     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom("babeformula1@gmail.com", "Babe Formula");  
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = "Email Verification from Babe Formula";

        $email_template = "
            <h2>Welcome to Babe Formula!</h2>
            <h3>Verify your email address using the link below:</h3>
            <br>
            <a href='http://localhost/babeformula/verify_email.php?token=$verify_token'>Click here to verify your email</a>
        ";

        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        $_SESSION['status'] = "Email sending failed: " . $mail->ErrorInfo;
    }
}

$username = $fname = $lname = $birthday = $gender = $contact = $email = $password = $confirm_password = "";
$usernameErr = $fnameErr = $lnameErr = $birthdayErr = $genderErr = $contactErr = $emailErr = $passwordErr = $confirmPasswordErr = $successMessage = $errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) { $usernameErr = "Username is required"; } else { $username = htmlspecialchars($_POST["username"]); }
    if (empty($_POST["fname"])) { $fnameErr = "First name is required"; } else { $fname = htmlspecialchars($_POST["fname"]); }
    if (empty($_POST["lname"])) { $lnameErr = "Last name is required"; } else { $lname = htmlspecialchars($_POST["lname"]); }
    if (empty($_POST["birthday"])) { $birthdayErr = "Birthday is required"; } else { $birthday = htmlspecialchars($_POST["birthday"]); }
    if (empty($_POST["gender"])) { $genderErr = "Gender is required"; } else { $gender = htmlspecialchars($_POST["gender"]); }
    if (empty($_POST["contact"])) { $contactErr = "Contact number is required"; } else { $contact = htmlspecialchars($_POST["contact"]); }
    if (empty($_POST["email"])) { $emailErr = "Email is required"; } else { $email = htmlspecialchars($_POST["email"]); }
    if (empty($_POST["password"])) { $passwordErr = "Password is required"; } else { $password = htmlspecialchars($_POST["password"]); }
    if (empty($_POST["confirm_password"])) { $confirmPasswordErr = "Confirm Password is required"; } else { $confirm_password = htmlspecialchars($_POST["confirm_password"]); }

    if ($password !== $confirm_password) {
        $confirmPasswordErr = "Passwords do not match";
    }
    
    if (empty($usernameErr) && empty($fnameErr) && empty($lnameErr) && empty($birthdayErr) && empty($genderErr) && empty($contactErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        $username = $conn->real_escape_string($username);
        $fname = $conn->real_escape_string($fname);
        $lname = $conn->real_escape_string($lname);
        $birthday = $conn->real_escape_string($birthday);
        $gender = $conn->real_escape_string($gender);
        $contact = $conn->real_escape_string($contact);
        $email = $conn->real_escape_string($email);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
        $verify_token = md5(rand());  
        
        $checkQuery = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            $errorMessage = "Username or email already exists.";
        } else {
            $insertQuery = "INSERT INTO users (username, fname, lname, birthday, gender, contact, email, password, verify_token) 
                            VALUES ('$username', '$fname', '$lname', '$birthday', '$gender', '$contact', '$email', '$hashedPassword', '$verify_token')";

            if ($conn->query($insertQuery) === TRUE) {
                sendemail_verify($email, $verify_token);  
                $_SESSION["success_message"] = "Registration successful! Please check your email for verification.";
                header("Location: register.php");
                exit();
            } else {
                $errorMessage = "Error: " . $insertQuery . "<br>" . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN UP</title>
    <link rel="stylesheet" href="register-style.css">
</head>
<body>
    <div class="logo-container">
        <img src="babelogo.png" alt="Logo" class="logo">
    </div>
    <div class="register-container">
        <div class="register-box">
            <h1 class="register-header">Sign Up</h1>
            
            <?php if (isset($_SESSION["success_message"])): ?>
                <div class="success-message">
                    <p><?php echo $_SESSION["success_message"]; ?></p>
                    <button onclick="location.href='index.php'" class="login-btn">Log In</button>
                </div>
                
                <!-- <script>
                    // Show an alert to notify user to check email
                    alert('Registration successful! Please check your email for the verification link.');
                </script> -->

                <?php unset($_SESSION["success_message"]); ?>
            <?php else: ?>

           
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php if ($errorMessage): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>

                <div class="form-row">
                    <div class="input-group">
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>">
                        <label for="username">*Username</label>
                        <span class="error"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="input-group">
                        <input type="text" id="fname" name="fname" value="<?php echo $fname; ?>">
                        <label for="fname">*First Name</label>
                        <span class="error"><?php echo $fnameErr; ?></span>
                    </div>
                    <div class="input-group">
                        <input type="text" id="lname" name="lname" value="<?php echo $lname; ?>">
                        <label for="lname">*Last Name</label>
                        <span class="error"><?php echo $lnameErr; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <input type="date" name="birthday" id="birthday" value="<?php echo $birthday; ?>">
                        <label for="birthday">*Birthday</label>
                        <span class="error"><?php echo $birthdayErr; ?></span>
                    </div>
                    
                    <div class="input-group gender-group">
                        <select id="gender" name="gender">
                            <option value="" disabled selected>Click to Select</option>
                            <option value="male" <?php if ($gender == 'male') echo 'selected'; ?>>Male</option>
                            <option value="female" <?php if ($gender == 'female') echo 'selected'; ?>>Female</option>
                            <option value="prefer_not_to_say" <?php if ($gender == 'prefer_not_to_say') echo 'selected'; ?>>Prefer not to say</option>
                        </select>
                        <label for="gender">Gender</label>
                        <span class="error"><?php echo $genderErr; ?></span>
                    </div>

                    <div class="input-group">
                        <input type="text" id="contact" name="contact" value="<?php echo $contact; ?>">
                        <label for="contact">Contact #</label>
                        <span class="error"><?php echo $contactErr; ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>">
                        <label for="email">*Email</label>
                        <span class="error"><?php echo $emailErr; ?></span>
                    </div>
                    <div class="input-group">
                        <input type="password" id="password" name="password" value="">
                        <label for="password">*Password</label>
                        <span class="error"><?php echo $passwordErr; ?></span>
                    </div>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="confirm_password" value="">
                        <label for="confirm_password">*Confirm Password</label>
                        <span class="error"><?php echo $confirmPasswordErr; ?></span>
                    </div>
                </div>

                <input type="submit" value="SIGN UP">
                
                <p class="account-message">Already have an account? 
                    <a href="index.php" class="login-link">Log In</a>
                </p>

            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
