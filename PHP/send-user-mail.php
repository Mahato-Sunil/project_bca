<!-- This Code is Contributed By 
    Sunil Mahato 
-->

<?php
// Imp Library  >> should always be at the start of the program 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//require the files for the php mailer 
// including the PHPMailer Library 
// PHPMailer File is inside "Configuration" Folder

require '../Configuration/PHPMailer/src/Exception.php';
require '../Configuration/PHPMailer/src/PHPMailer.php';
require '../Configuration/PHPMailer/src/SMTP.php';

//get the details from the users 
if (isset($_SERVER['REQUEST_METHOD']) == 'GET') {
    $full_name = "";
    $email = "";

    
    //update the credentials  based in the role 
    if ($role != 'reject') {
        updateCredentials($userKey, $email, $full_name);
        echo "<script> window.location.href='new-user-request.php'</script>";
        exit();
    } else {
        sendRejectionMail($full_name, $email);
        deleteUserData($userKey);
    }
}

// function to update the credentials 
function updateCredentials($userKey, $email, $full_name)
{
    require "../Configuration/config.php";

    // generate the random password 
    $user_login_password = generatePwd(10);
    echo $user_login_password;

    $hashedPwd = password_hash($user_login_password, PASSWORD_DEFAULT);

    try {
        // Create connection to the database using PDO
        $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "UPDATE credentials SET Hash = :HASH WHERE reg_no = :KEY";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':HASH', $hashedPwd);
        $stmt->bindParam(':KEY', $userKey);

        $result = $stmt->execute();

        if ($result > 0)
            echo "Database Updated";
        else
            echo "Database Failed To Update";
    } catch (PDOException $e) {
        echo 'Error in PDO : ' . $e->getMessage();
    }

    //call the send mail function to send the email 
    sendConfirmationMail($full_name, $email, $user_login_password);
}

//function to generate the random password 
function generatePwd($length)
{
    $bytes = random_bytes($length);
    $password = bin2hex($bytes);
    return substr($password, 0, $length);
}

//function to send the mail 
function sendConfirmationMail($full_name, $email, $user_login_password)
{
    require '../Configuration/config.php';

    //create the mail object 
    $mail = new PHPMailer(true);

    //defining the message to be send 
    $htmlMsg = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Confirmation Email</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
    
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
    
            .email-header {
                background-color: #71b2f4;
                color: #ffffff;
                padding: 0.5rem;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }
    
            .email-body {
                padding: 20px;
                text-align: center;
            }
    
            .email-body .pwd {
                margin: 1.5rem 1rem;
                text-align: left;
            }
    
            .email-footer {
                text-align: center;
                padding: 10px;
                font-size: 12px;
                color: #888888;
            }
        </style>
    </head>
    
    <body>
        <div class='email-container'>
            <div class='email-header'>
                <h1> Mero Tracker </h1>
            </div>
            <div class='email-body'>
    
                <p class='pwd'> Dear <b> $full_name </b></p>
                <br>
                <p>Thank you for choosing us as your safety partner. Your account has been successfully created.</p>
                <div class='pwd'>
                    <p><b>Your login details are as follows:</b></p>
                    <p>Username: <b> Please Use your Email as the default Username.</i></b></p>
                    <p>Password: <b> $user_login_password </b></p>
                    <br>
                </div>
                <p>Please keep this information secure and do not share it with anyone.</p>
                <p>If you did not sign up for this account, please ignore this email.</p>
            </div>
            <div class='email-footer'>
                <p>© 2024 Mero Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    
    </html>";

    $altMsg = " Dear $full_name" . "\n"
        . "Thank you for choosing us as your safety partner. Your account has been successfully created. "
        . "\n" . "Your login details are as follows:"
        . "\n" . "Username: Please Use your Email as the default Username."
        . "\n" . "Password:" . $password
        . "\n" . "Please keep this information secure and do not share it with anyone."
        . "\n" . "If you did not sign up for this account, please ignore this email."
        . "\n" . "Thank You";

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

        //receipients 
        $mail->setFrom('noreply@merotracker.com', 'Mero Tracker');
        $mail->addAddress($email);     //Add a recipient
        $mail->addBCC('sunilmhto42@gmail.com', 'Copy Mail');

        //message details 
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation Mail';
        $mail->Body = $htmlMsg;
        $mail->AltBody = $altMsg;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

}

// function to send the rejection mail 
function sendRejectionMail($full_name, $email)
{
    require '../Configuration/config.php';

    //create the mail object 
    $mail = new PHPMailer(true);

    //defining the message to be send 
    $htmlMsg = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Rejection Email</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
    
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
    
            .email-header {
                background-color: #71b2f4;
                color: #ffffff;
                padding: 0.5rem;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }
    
            .email-body {
                padding: 20px;
                text-align: center;
            }
    
            .email-body .pwd {
                margin: 1.5rem 1rem;
                text-align: left;
            }
    
            .email-footer {
                text-align: center;
                padding: 10px;
                font-size: 12px;
                color: #888888;
            }
        </style>
    </head>
    
    <body>
        <div class='email-container'>
            <div class='email-header'>
                <h1> Mero Tracker </h1>
            </div>
            <div class='email-body'>
    
                <p class='pwd'> Dear <b> $full_name </b></p>
                <br>
                <p>We are sorry to inform you that your request for registration to our system has be cancelled. It may be due to incorrect details provided.</p>
                <p> Please Submit another application form  with correct and valid details </p>
                <p>If you did not sign up for this account, please ignore this email.</p>
            </div>
            <div class='email-footer'>
                <p>© 2024 Mero Tracker. All rights reserved.</p>
            </div>
        </div>
    </body>
    
    </html>";

    $altMsg = " Dear $full_name" . "\n"
        . "Sorry ! Your account registration has been failed. "
        . "\n" . "Please Apply With Correct Credentials and information"
        . "\n" . "Username: Please Use your Email as the default Username."
        . "\n" . "If you did not sign up for this account, please ignore this email."
        . "\n" . "Thank You";

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

        //receipients 
        $mail->setFrom('noreply@merotracker.com', 'Mero Tracker');
        $mail->addAddress($email);     //Add a recipient
        $mail->addBCC('sunilmhto42@gmail.com', 'Copy Mail');

        //message details 
        $mail->isHTML(true);
        $mail->Subject = 'Rejection Mail';
        $mail->Body = $htmlMsg;
        $mail->AltBody = $altMsg;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// function to delete the user data 
function deleteUserData($userkey)
{
    require "../Configuration/config.php";

    try {
        $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->beginTransaction();

        // Prepare and execute DELETE statements for each table
        $deleteQueries = "DELETE FROM bio_data WHERE Citizenship = :CTZN";

        $statement = $conn->prepare($deleteQueries);
        $statement->bindParam(':CTZN', $userkey);
        $statement->execute();

        $conn->commit();
        echo " <script> window.location.href = '../PHP/new-user-request.php'; </script>";
    } catch (PDOException $e) {
        // Handle database errors
        echo "<script>  alert('Failed To Reject The Application !');  
                        window.location.href = '../PHP/new-user-request.php';   
               </script>";

        echo "Error : " . $e->getMessage();
        $conn->rollBack();
    }

}