<?php
session_start();
include('connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
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
            <h2>You have registered with Babe Formula</h2>
            <h3>Verify your email address using the link below to activate your account:</h3>
            <br>
            <a href='http://localhost/babeformula/verify_email.php?token=$verify_token'>Click here to verify your email</a>
        ";

        $mail->Body = $email_template;
        $mail->send();

    } catch (Exception $e) {
        $_SESSION['status'] = "Email sending failed: " . $mail->ErrorInfo;
    }
}

if (isset($_POST["btn_register"])) {
    $lname = $_POST["cust_lastname"];
    $fname = $_POST["cust_firstname"];
    $birthdate = $_POST["cust_birthdate"];
    $email = $_POST["cust_email"];
    $contact = $_POST["cust_cellno"];
    $username = $_POST["cust_username"];
    $password = $_POST["cust_password"];
    $confirmpassword = $_POST["cust_confirmpassword"];
    $verify_token = md5(rand());  
    
    $duplicate = mysqli_query($conn, "SELECT * FROM tblcustomer_login WHERE cust_username = '$username' OR cust_email = '$email'");
    
    if (mysqli_num_rows($duplicate) > 0) {
        $_SESSION['status'] = "Username or Email already exists!";
        header("location: register.php");
    } else {

        if ($password == $confirmpassword) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO tblcustomer_login (cust_lastname, cust_firstname, cust_birthdate, cust_email, cust_cellno, cust_username, cust_password, verify_token) 
                      VALUES ('$lname', '$fname', '$birthdate', '$email', '$contact', '$username', '$hashed_password', '$verify_token')";

            $query_run = mysqli_query($conn, $query);
            
            if ($query_run) {
                sendemail_verify($email, $verify_token);

                $_SESSION['status'] = "Registration Successful! Please verify your email address to complete the process.";
                header("location: register.php");
            } else {
                $_SESSION['status'] = "Registration Failed!";
                header("location: register.php");
            }
        } else {
            $_SESSION['status'] = "Passwords do not match!";
            header("location: register.php");
        }
    }
}
?>
