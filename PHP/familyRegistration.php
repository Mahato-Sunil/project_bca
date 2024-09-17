<!-- php code to manipulate the data -->

<?php

$key = isset($_GET['key']) ? $_GET['key'] : ''; //  get the unique key form the url 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['family-registration'])) {

    require "../Configuration/config.php";

    global $key;
    $name = $_POST['f-name'];
    $email = $_POST['f-email'];
    $contact = $_POST['f-contact'];
    $photo = $_POST['avatar'];

    //generating the unique family key 
    $firstParam = explode(' ', trim($name), 2)[0];
    $lastParam =  substr($key, -5);
    $family_key = $firstParam . $lastParam;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        $query = "INSERT INTO family_data (Name, family_key, Email, Contact, Photo, Ctzn_id) VALUES (:NAME, :FKEY, :EMAIL, :CONTACT, :PHOTO, :CTZN)"; // corrected SQL query

        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':NAME', $name);
        $stmt->bindParam(':FKEY', $family_key);
        $stmt->bindParam(':EMAIL', $email);
        $stmt->bindParam(':CONTACT', $contact);
        $stmt->bindParam(':PHOTO', $photo);
        $stmt->bindParam(':CTZN', $key);

        $stmt->execute();
        $pdo->commit();

        echo "<script> 
            alert('Family Registration Successful! \\nRedirecting to the Dashboard');
            window.location.href = '../Dashboard/user-dashboard.php?key=$key';
        </script>";
    } catch (PDOException $e) {
        // Error message
        echo "<script> 
            alert('USER REGISTRATION FAILED: " . $e->getMessage() . "');
            window.location.href = '../Dashboard/user-dashboard.php?key=$key';
        </script>";
        $pdo->rollBack();
    }
}
?>

<!-- HTML PAGE FOR REGISTRATION OF THE FAMILY MEMBERS  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Registration </title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Style/family-registration.css">
</head>

<body>
    <div class="top-bar">
        <div class="logo">
            <a href="../index.html">
                <img src="../Image/app_launcher.svg" alt="Smart Mobile Tracker">
            </a>
        </div>
        <h1> Family Registration </h1>
    </div>
    <div class="container">

        <form id="user-registration" name="user-family-registration" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>?key=<?php echo $key ?>">

            <!-- html code for the family information  -->
            <div class="form-contents">
                <label for="f-name"> Full Name <span> * </span></label>
                <input type="text" name="f-name" placeholder="Eg. Sunil Mahato" class="nameField" required>
                <small id="nameMsg" class="errorMsg"></small>

                <label for="f-contact"> Contact Number <span> * </span></label>
                <input type="tel" name="f-contact" placeholder="Eg. 9860650642" minlength="10" maxlength="10" required>
                <small id="pNumMsg" class="errorMsg"></small>

                <label for="f-email"> Email ID <span> * </span> </label>
                <input type="email" name="f-email" placeholder="Eg. abc@gmail.com" required>
                <small id="emailMsg" class="errorMsg"></small>

                <label for="f-photo"> Select Your Avatar : </label>
                <div class="gallery">
                    <img src="../Image/Avatar/avatar_1.png" alt="Image 1" class="avatar_img">
                    <img src="../Image/Avatar/avatar_2.png" alt="Image 2" class="avatar_img">
                    <img src="../Image/Avatar/avatar_3.png" alt="Image 3" class="avatar_img">
                    <img src="../Image/Avatar/avatar_4.png" alt="Image 4" class="avatar_img">
                    <img src="../Image/Avatar/avatar_5.png" alt="Image 5" class="avatar_img">
                </div>
                <input type="hidden" name="avatar" id="avatar">

                <br>
                <div class="btngrp">
                    <button class="w3-button w3-white w3-border w3-border-red w3-center w3-medium" onclick="window.location.href='../Dashboard/user-dashboard.php?key=<?php echo $key ?>'">
                        Cancel
                    </button>
                    <button class="w3-button w3-white w3-border w3-border-blue w3-center w3-medium" type="submit" id="submitBtn" onclick="console.log('buttonis clicked')" name="family-registration">
                        Register
                    </button>
                </div>

            </div>
        </form>
    </div>
    <script src="../Script/input-field-validation.js"></script>
    <script>
        // getting the source of the avatar 
        document.addEventListener('DOMContentLoaded', () => {
            let avatar = document.querySelectorAll('.avatar_img');
            avatar.forEach((event) => {
                event.addEventListener('click', (element) => {
                    // removing the styles from the images 
                    avatar.forEach((img) => {
                        img.style.filter = '';
                        img.style.background = '';
                        img.style.width = '';
                        img.style.height = '';
                        img.style.boxShadow = '';
                        img.style.borderRadius = '';
                        img.style.border = '';
                    });

                    // defining the styles for the selected div 
                    let styles = {
                        filter: 'brightness(100%)',
                        background: 'green',
                        'border-bottom': '0.3rem solid rgb(169, 255, 192)',
                        'border-top': '0.2rem solid rgb(169, 255, 192)',
                        'border-radius': '10px',
                        border: '2px solid green'
                    };

                    let url = new URL(element.target.src);
                    let img_src = url.pathname;

                    document.getElementById('avatar').value = img_src;

                    for (let property in styles) {
                        element.target.style[property] = styles[property];
                    }
                });
            });

        });
    </script>

</body>

</html>