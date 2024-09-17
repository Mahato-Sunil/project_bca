<!-- php code -->
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// require the config file 
require "../Configuration/config.php";

//retrive the information from the user after creating the post request 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pwd_reset_btn'])) {
    $user_email = $_POST['email'];
    $user_id = "";
    $isRegister = false;

    // Creating connection to the database using PDO
    try {
        $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "SELECT Ctzn_no FROM contact_info
              WHERE Email  = :EMAIL";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':EMAIL', $user_email);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $isRegister = true;
            $user_id = $data['Ctzn_no'];
        }
    } catch (PDOException $e) {
        echo 'Error in PDO: ' . $e->getMessage();
    }

    // check if the user exists 
    if ($isRegister) {
        //send the email 
        sendResetMail($user_id, $user_email);
    }
}
//function to send the user mail 
function sendResetMail($user_id, $user_email)
{
    require "../Configuration/config.php";

    //require the files for the php mailer 
    require '../Configuration/PHPMailer/src/Exception.php';
    require '../Configuration/PHPMailer/src/PHPMailer.php';
    require '../Configuration/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);
    $htmlMsg = "
    <!DOCTYPE html>
    <html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background-color: #ffffff;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #dddddd;
        }
     .email-header {
                background-color: #71b2f4;
                color: #ffffff;
                padding: 0.5rem;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }

        .email-body {
            text-align: left;
            padding: 20px;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999999;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class='email-container'>
          <div class='email-header'>
                <h1> Mero Tracker </h1>
          </div>
        <div class='email-body'>
            <h1>Password Reset Request</h1>
            <p>Dear User,</p>
            <p>We received a request to reset your password. Click the button below to reset it.</p>
            <p>If you did not request a password reset, please ignore this email.</p>
            <p style='text-align: center;'>
                <a href='http://192.168.43.85/php/new-password-creator.php?token={$user_id}' class='button'>Reset Password</a>
            </p>
            <p>Thank you,<br> Mero Tracker</p>
        </div>
        <div class='email-footer'>
            <p>&copy; 2024 Mero Tracker. All rights reserved.</p>
        </div>
    </div>
</body>
</html>";

    //defining the server settings 
    try {
        //smtp details 
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = $mail_host;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_username;
        $mail->Password = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //recipients 
        $mail->setFrom('noreply@merotracker.com', 'Mero Tracker');
        $mail->addAddress($user_email);     //Add a recipient
        $mail->addBCC('sunilmhto42@gmail.com', 'Copy Mail');

        //message details 
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = $htmlMsg;
        $mail->send();
        echo '<script> window.location.href = \'../email-success-redirect.html\' </script>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!-- html code -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="../Style/password-reset-ui.css">
</head>

<body>
    <div class="container">
        <div class="reset-box">
            <h2> Reset Your Password </h2>
            <form id="pwd_reset_form" name="pwd_reset_form" method="post">
                <label for="email">Enter your Email associated with your Account :</label>
                <input type="email" id="email" name="email" placeholder="Email address" required>
                <button type="submit" name="pwd_reset_btn">Send Reset Link</button>
            </form>
        </div>
    </div>
</body>

</html>