<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    
    <link rel="stylesheet" href="forgotpass-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="forgotpass-container">
        <div class="logo-container">
            <img src="babelogo.png" alt="Babe Logo" class="logo">
        </div>

        <div class="form-content">
            <h1>Forgot Password?</h1>
            <p>Enter your email to reset your password.</p>
            <br><br>

            <form action="forgotpass-handler.php" method="post">
                <div class="input-group">
                    <input type="email" name="email" id="email" required placeholder="Enter your email">
                    <label for="email">Email</label>
                </div>

                <input type="submit" value="Reset Password">

                <p class="back-to-login">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
