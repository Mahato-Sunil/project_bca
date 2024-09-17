<?php

//     this fetches the data from the  app and stores to the database 
//  DON'T CHANGE THE FILE'NAME AND ANY THING 


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//require the files for the php mailer 

require '../Configuration/PHPMailer/src/Exception.php';
require '../Configuration/PHPMailer/src/PHPMailer.php';
require '../Configuration/PHPMailer/src/SMTP.php';

// Including database credentials
require "../Configuration/config.php";

// Sanitizing input data
$user_name = isset($_POST['user_name']) ? trim($_POST['user_name'], FILTER_SANITIZE_STRING) : null;
$user_phone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : null;
$user_key = isset($_POST['user_ctzn']) ? trim($_POST['user_ctzn']) : null;
$latitude = isset($_POST['latitude']) ? filter_var($_POST['latitude'], FILTER_SANITIZE_STRING) : null;
$longitude = isset($_POST['longitude']) ? filter_var($_POST['longitude'], FILTER_SANITIZE_STRING) : null;
$time = isset($_POST['current_time']) ? filter_var($_POST['current_time'], FILTER_SANITIZE_STRING) : null;

// creating response array 
$response = array();

try {
    // Database connection
    $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare statement
    $sql = "INSERT INTO location (Name, Phone, Citizenship, Latitude, Longitude, Time) VALUES (:name, :phone, :ctzn,  :lat, :lon, :ctime)";
    $statement = $conn->prepare($sql);

    // Bind parameters
    $statement->bindParam(':name', $user_name);
    $statement->bindParam(':phone', $user_phone);
    $statement->bindParam(':lat', $latitude);
    $statement->bindParam(':lon', $longitude);
    $statement->bindParam(':ctzn', $user_key);
    $statement->bindParam(':ctime', $time);

    // Execute statement
    $conn->beginTransaction();
    $result = $statement->execute();

    if ($result) {
        $conn->commit();
        $response['success'] = true;
        $response['message'] = "Data Uploaded Successfully";

        //call to funtion to send the location data to the email ids 
        sendSos($user_name, $user_key, $latitude, $longitude, $time);

    } else {
        $conn->rollBack();
        $response['success'] = false;
        $response['message'] = "Data Upload Failed";
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Server Error : " . $e->getMessage();
}

// Sending JSON response
header('Content-Type: application/json');
echo json_encode($response);


// function to send the location data to the respective family members 
function sendSos($user_name, $user_key, $latitude, $longitude, $time)
{
    require '../Configuration/config.php';

    /* retrieve the family's email id  from the database */
    $familyEmail = [];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare statement
        $query = "SELECT Email FROM family_data WHERE Ctzn_id = :key";
        $statement = $conn->prepare($query);
        $statement->bindParam(':key', $user_key);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            foreach ($data as $d) {
                $familyEmail[] = [
                    'user_family_email' => $d['Email']
                ];
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    /*send the mail to the respective family's member email ids */
    $mail = new PHPMailer(true);

    //defining the message to be send 
    $htmlMsg = "<!DOCTYPE html>
    <html lang='en'>
    
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no'>
        <link href='https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css' rel='stylesheet'>
        <script src='https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js'></script>
        <title>EMERGENCY SOS !!</title>
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
                background-color: #fe4d4d;
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
    
            #map {
                position: absolute;
                top: 0;
                bottom: 0;
                width: 100%;
            }
        </style>
    </head>
    
    <body>
        <div class='email-container'>
            <div class='email-header'>
                <h1> Emergency SOS </h1>
            </div>
            <div class='email-body'>
    
                <p class='pwd'> Dear Valued User</p>
                <br>
                <p>This is an Emergency SOS. Please Respond it Immediately. </p>
                <div class='pwd'>
                    <p><b> Location Details :</b></p>
                    <p>Latitude: <b> $latitude </i></b></p>
                    <p>Longitude: <b> $longitude </b></p>
                    <p>Requested Time : <b> $time </b></p>
                    <br>
                </div>
                <h2> See In the Map : <a href='http:192.168.1.87/Map/user-map.php?lat=$latitude&lon=$longitude'>Locate User</a> </h2>
            </div>
            <p>Please Review the information and call the nearest police station as quickly as possible. </p>
        </div>
        <div class='email-footer'>
            <p>© 2024 Mero Tracker. All rights reserved.</p>
        </div>
        </div>
    </body>
    
    </html>";

    $altMsg = " Dear Valued User" . "\n"
        . "This is an Emergency Email. Please Respond it Immediately.  "
        . "\n" . "Your login details are as follows:"
        . "\n" . "Latitude : $latitude"
        . "\n" . "Longitude : $longitude"
        . "\n" . "Time : $time"
        . "\n" . "Please Review the information and call the nearest police station as quickly as possible."
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

        foreach ($familyEmail as $email) {

            $mail->addAddress(implode(',', $email));
        }    //Add a recipient

        $mail->addBCC('sunilmhto42@gmail.com', 'Copy Mail');

        //message details 
        $mail->isHTML(true);
        $mail->Subject = 'Emergency SOS ';
        $mail->Body = $htmlMsg;
        $mail->AltBody = $altMsg;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
