<!-- php code  -->
<!-- php code to fetch the data from the database  -->
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//include the files for the php mailer 

require '../Configuration/PHPMailer/src/Exception.php';
require '../Configuration/PHPMailer/src/PHPMailer.php';
require '../Configuration/PHPMailer/src/SMTP.php';

include '../Configuration/config.php';

(isset($_GET['key'])) ? $key = $_GET['key'] : "admin_1";

$totalData = "";

try {
    // Create connection to the database using PDO
    $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute query to get user data
    $queryUserData = "SELECT b.FirstName, b.MiddleName, b.LastName, b.Citizenship, c.PrimaryPhone, c.Email, s.Status
                      FROM bio_data AS b
                      INNER JOIN user_status AS s ON b.Citizenship = s.userKey
                      INNER JOIN contact_info AS c ON b.Citizenship = c.Ctzn_no";

    $stmtUserData = $conn->prepare($queryUserData);
    $stmtUserData->execute();

    // Fetch the user data
    $userdata = []; // Initialize $userdata array
    while ($row = $stmtUserData->fetch(PDO::FETCH_ASSOC)) {
        $fullname = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName']; // Combine names
        $userdata[] = [
            'full_name' => $fullname,
            'contact' => $row['PrimaryPhone'],
            'email' => $row['Email'],
            'status' => $row['Status'],
            'key' => $row['Citizenship']
        ];
    }
} catch (PDOException $e) {
    // Handle errors
    echo 'Error in PDO: ' . $e->getMessage();
}

// =======================================================================================
// code for  accepting the data of the users 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['acceptBtn'])) {
        //get the key 
        $actionKey = $_POST['actionKey'];

        //update the database with the "active" query 
        try {
            $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE user_status SET Status = 'Active' WHERE userKey = '$actionKey'";

            $stmt = $conn->prepare($sql);
            $result = $stmt->execute();

            if ($result > 0) {
                echo " <script> window.location.href = '../PHP/send-user-mail.php?key=$actionKey&role=accept'; </script>";
            } else
                echo " <script> window.location.href = '../PHP/new-user-request.php'; </script>";
        } catch (PDOException $e) {
            // Handle errors
            echo 'Error in PDO: ' . $e->getMessage();
        }
    }
}


//-------------------------------------------------------------------------------------------------------------------------
//code for rejecting the data of the users 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rejectBtn'])) {
    //get the key 
    $actionKey = $_POST['actionKey'];
    // Redirect the user after successful deletion
    echo " <script> window.location.href = '../PHP/send-user-mail.php?key=$actionKey&role=reject'; </script>";
    exit();
}
?>
<!-- html code  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/admin-dashboard-ui.css">
    <title>New User Request</title>
</head>

<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo"></div>

                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span> <!-- icon for the close button -->
                </div>
            </div>

            <!-- side bar  -->
            <div class="sidebar">
                <a href="../Dashboard/admin-dashboard.php?key=<?php echo $key ?>">
                    <span class="material-icons-sharp"> grid_view</span>
                    <h3> Dashboard </h3>
                </a>

                <a href="../PHP/new-user-request.php?key=<?php echo $key ?>" class="active">
                    <span class="material-icons-sharp active"> manage_accounts </span>
                    <h3> New User Request </h3>
                </a>

                <!-- <a href=" ../PHP/admin-profile-setting.php?key=<?php echo $key; ?>">
                    <span class="material-icons-sharp"> settings</span>
                    <h3> Profile </h3>
                </a> -->

                <button id="logout">
                    <span class="material-icons-sharp"> logout</span>
                    <h3> Log Out </h3>
                </button>
            </div>
        </aside>


        <!-- main section  -->
        <main>
            <!-- div for displaying the data in the tabular form -->
            <div class="user-data-mobile">
                <table class="user-data w3-hoverable">
                    <thead>
                        <tr>
                            <th>SN.</th>
                            <th>Name </th>
                            <th>Email </th>
                            <th>Contact No.</th>
                            <th> Status </th>
                            <th class="w3-center"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userdata as $index => $d) : ?>
                            <tr>
                                <td>
                                    <?php echo $index + 1; ?>
                                </td> <!-- Use index to display iteration count -->
                                <td>
                                    <?php echo $d['full_name']; ?>
                                </td>
                                <td>
                                    <?php echo $d['email']; ?>
                                </td>
                                <td>
                                    <?php echo $d['contact'] ?>
                                </td>
                                <td>
                                    <?php echo $d['status'] ?>
                                </td>
                                <td>
                                    <div class="actionButton">
                                        <form method="post" name="action-method">
                                            <input type="hidden" name="actionKey" value="<?php echo $d['key'] ?>">
                                            <button name="acceptBtn" <?php
                                                                        if ($d['status'] != 'Pending') {
                                                                            echo "style='background-color: rgb(211, 211, 211);' disabled";
                                                                        } else {
                                                                            echo "style='background-color: var(--color-success);'";
                                                                        }
                                                                        ?>> Accept</button>


                                            <button name="rejectBtn" <?php
                                                                        if ($d['status'] == 'Active') {
                                                                            echo "style='background-color: rgb(211, 211, 211);' disabled";
                                                                        } else {
                                                                            echo "style='background-color: var(--color-danger);'";
                                                                        }
                                                                        ?>> Reject
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </main>

        <!-- end of the main section  -->
        <!-- start of the right section  -->
        <div class=" right">
            <!-- profile information  -->
            <div class="top">
                <div class="profile">
                    <div class="profile-img">
                        <img src="../Image/developer_profile.svg" alt="Profile photo of admin">
                    </div>
                </div>
            </div>

            <!-- notification section  -->
            <div id="noticeContainer"></div>
        </div>

        <div class="mobile-top">
            <button id="menu-btn"> <span class="material-icons-sharp"> menu </span> </button>
            <div class="profile">
                <div class="profile-img">
                    <img src="../Image/developer_profile.svg" alt="Profile photo of user">
                </div>
            </div>
        </div>

        <!-- end of the right section  -->
    </div>
    <script src="../Script/user-dashboard-script.js"></script>
    <script src="../Script/admin-notification-script.js"></script>
</body>

</html>