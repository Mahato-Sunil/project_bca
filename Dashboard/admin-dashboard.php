<?php

require "../PHP/check_session.php";

// Include configuration file
include "../Configuration/config.php";

//get the key 
$key = $_GET['key'];

$totalData = "";

try {
    // Create connection to the database using PDO
    $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //for the notification 
    // Prepare and execute query to get total data count
    $requestData = "SELECT COUNT(*) AS totalRequest
     FROM user_status
     WHERE Status = 'Pending'";

    $stmtTotalRequest = $conn->prepare($requestData);
    $stmtTotalRequest->execute();

    // Fetch the total data count
    $totalRequest = $stmtTotalRequest->fetch(PDO::FETCH_ASSOC)['totalRequest'];


    //check for the user and display the data accordingly 
    // Prepare and execute query to get total data count
    $queryTotalData = "SELECT COUNT(*) AS totalData
                       FROM bio_data AS b
                       INNER JOIN contact_info AS c ON b.Citizenship = c.Ctzn_no
                       INNER JOIN user_status AS u ON b.Citizenship = u.userKey
                       WHERE u.Status = 'active'";

    $stmtTotalData = $conn->prepare($queryTotalData);
    $stmtTotalData->execute();

    // Fetch the total data count
    $totalData = $stmtTotalData->fetch(PDO::FETCH_ASSOC)['totalData'];

    // Prepare and execute query to get user data
    $queryUserData = "SELECT b.FirstName, b.MiddleName, b.LastName, b.Citizenship, c.PrimaryPhone, c.Email 
                      FROM bio_data AS b
                      INNER JOIN contact_info AS c ON b.Citizenship = c.Ctzn_no
                      INNER JOIN user_status AS u ON b.Citizenship = u.userKey
                       WHERE u.Status = 'Active'";

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
            'key' => $row['Citizenship']
        ];
    }
} catch (PDOException $e) {
    // Handle errors
    echo 'Error in PDO: ' . $e->getMessage();
}
?>

<!-- html code  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../Style/admin-dashboard-ui.css">
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
                <a href="#" class="active">
                    <span class="material-icons-sharp"> grid_view</span>
                    <h3> Dashboard </h3>
                </a>

                <a href="../PHP/new-user-request.php?key=<?php echo $key ?>">
                    <span class="material-icons-sharp"> manage_accounts </span>
                    <h3> New User Request </h3>
                    <small class="notification-dot">
                        <?php echo $totalRequest ?>
                    </small>
                </a>

                <!-- <a href=" ../PHP/admin-profile-setting.php?key=<?php echo $key; ?>">
                    <span class="material-icons-sharp"> settings</span>
                    <h3> Profile </h3>
                </a> -->

                <button id="logout" name="logout-btn">
                    <span class="material-icons-sharp"> logout</span>
                    <h3> Log Out </h3>
                </button>
            </div>
        </aside>


        <!-- main section  -->
        <main>

            <!-- div for the card section  -->
            <div class="main-card">
                <div class="card">
                    <div class="card-text">
                        <h3>
                            <?php echo $totalData ?>
                        </h3>
                        <h3> Users </h3>
                    </div>
                    <div class="card-image">
                        <img src="../Image/Avatar/avatar_1.png" alt="new User image">
                    </div>
                </div>

                <div class="card">
                    <div class="card-text">
                        <h3>
                            <?php echo $totalData ?>
                        </h3>
                        <h3> Happy Families </h3>
                    </div>
                    <div class="card-image">
                        <img src="../Image/Avatar/avatar_1.png" alt="new User image">
                    </div>
                </div>
            </div>

            <!-- div for displaying the data in the tabular form -->
            <div class="user-data-mobile">
                <table class="user-data w3-hoverable">
                    <thead>
                        <tr>
                            <th>SN.</th>
                            <th>Name </th>
                            <th>Email </th>
                            <th>Contact No.</th>
                            <th></th>
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
                                    <span class="material-icons-sharp" style="width: 6rem; text-align:center" onclick="window.location.href='../PHP/user-data-details.php?key=<?php echo $d['key']; ?>'">
                                        tips_and_updates </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </main>

        <!-- end of the main section  -->

        <!-- start of the right section  -->
        <div class="right">
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

            <div class="mobile-notice">
                <span class="material-icons-sharp"> notifications </span>
            </div>

            <div class="profile">
                <div class="profile-img">
                    <img src="../Image/developer_profile.svg" alt="Profile photo of user">
                </div>
            </div>
        </div>
    </div>
    <!-- end of the right section  -->
    <script src="../Script/user-dashboard-script.js"></script>
    <script src="../Script/admin-notification-script.js"></script>
</body>

</html>