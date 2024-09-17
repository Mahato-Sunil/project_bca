<?php
require "PHP/check_session.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Style/login-page-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Sharp" rel="stylesheet">
    <title>Login Page</title>
</head>

<body>
    <div class="container">

        <!-- code for the login button  -->
        <div class="login-box" id="user">
            <div class="heading">
                <h1 onclick="window.location.href='index.html'"><i class="material-icons w3-xxlarge">home</i></h1>
            </div>
            <!-- image source  -->
            <img src="./Image/login-image.svg" alt="Login Page Demo" class="login-img">

            <strong id="msg"></strong>
            <form name="login" method="post" action="./PHP/validate-password.php">
                <input type="hidden" value="user" name="role">
                <input type="text" name="username" placeholder="Username or Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" name="submit" value="Login">
            </form>

            <div class="msg">
                <small style="color: blue;"> <a href='./PHP/password_reset_form.php'> Forget Password </a>
                </small>
            </div>
            <div class="msg">
                <small> Don't Have an Account ? <a href="./Registration/user-registration.html"> Sign Up Now
                    </a></small>
            </div>
        </div>
    </div>
</body>

</html>