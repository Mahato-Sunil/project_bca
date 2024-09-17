<?php
// including the config file 
include "../Configuration/config.php";

//  global declaration for the data from database 
$full_name = "";
$dob = "";

$primary_phone = "";
$secondary_phone = "";
$email = "";

$perm_province = "";
$perm_district = "";

$temp_province = "";
$temp_district = "";

$bio = "";

$family_data_array = [];

if (isset($_GET['key'])) {

    // get the citizenship number 
    $key = $_GET['key'];

    // creating connection  to the database using PDO
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // prepare sql statement and bind the parameters

        //  query to select the  data from database 
        $query = "SELECT * FROM bio_data
                LEFT JOIN contact_info ON bio_data.Citizenship = contact_info.Ctzn_no                            
                LEFT JOIN current_address ON bio_data.Citizenship = current_address.Ctzn_no                            
                LEFT JOIN permanent_address ON bio_data.Citizenship = permanent_address.Ctzn_no
                LEFT JOIN user_setting ON bio_data.Citizenship = user_setting.user_key                            
                WHERE bio_data.Citizenship = :CTZN";

        $fquery = "SELECT * FROM family_data WHERE Ctzn_id = :key";

        $stmt = $pdo->prepare($query);
        $fstmt = $pdo->prepare($fquery);

        $stmt->bindParam(':CTZN', $key);
        $fstmt->bindParam(':key', $key);

        //  execute the statment 
        $stmt->execute();
        $fstmt->execute();

        // fetching the users data 
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            foreach ($data as $row) {
                $fname = $row['FirstName'];
                $mname = $row['MiddleName'];
                $lname = $row['LastName'];
                $full_name = $fname . " " . $mname . " " . $lname;
                $dob = $row['DOB'];

                $primary_phone = $row['PrimaryPhone'];
                $secondary_phone = $row['SecondaryPhone'];
                $email = $row['Email'];

                $temp_province = $row['Province'];
                $temp_district = $row['District'];

                $perm_province = $row['Province_p'];
                $perm_district = $row['District_p'];

                $bio = $row['Bio'];
            }
        }

        // fetching the user's family data 

        $fdata = $fstmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fdata as $row) {
            $family_data_array[] = [
                'f_name' => $row['Name'],
                'f_contact' => $row['Contact'],
                'f_email' => $row['Email'],
                'f_photo' => $row['Photo'],
                'f_key' => $row['family_key']
            ];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
$pdo = null;    // freeing the resources 

//deleting the data of the users 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-btn'])) {

    $deleteKey = $_POST['deleteKey'];   //get the key to delete the data 

    // Creating connection to the database using PDO
    try {
        $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);
        // Set the PDO error mode to exception 
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->beginTransaction();

        // Prepare and execute DELETE statements for each table
        $deleteQuery = "DELETE FROM family_data WHERE family_key  = :KEY";

        $statement = $conn->prepare($deleteQuery);
        $statement->bindParam(':KEY', $deleteKey);
        $statement->execute();

        $conn->commit();

        // Redirect the user after successful deletion
        echo "<script>  alert('Family Data Removed !');  window.location.href = '{$_SERVER['PHP_SELF']}?key={$key}'; </script>";
        exit();
        $pdo = null;
    } catch (PDOException $e) {
        // Handle database errors
        echo "<script> alert('Failed To Delete Data !'); window.location.href = '{$_SERVER['PHP_SELF']}?key={$key}'; </script>";
        exit();
        echo "Error : " . $e->getMessage();
        $pdo = null;
        $conn->rollBack();
    }
}
?>

<!-- html code  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../Style/user-dashboard-ui.css">
</head>

<body>
    <div class="container">
        <!-- side bar for the dashboard -->
        <aside>
            <div class="top">
                <div class="logo"></div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span> <!-- icon for the close button -->
                </div>
            </div>

            <!-- side bar  -->
            <div class="sidebar">
                <a href="#">
                    <span class="material-icons-sharp" class="active"> grid_view</span>
                    <h3> Dashboard </h3>
                </a>

                <a href="../PHP/edit-data.php?key=<?php echo $key; ?>">
                    <span class="material-icons-sharp">edit</span>
                    <h3> Edit Data </h3>
                </a>

                <a href='../PHP/familyRegistration.php?key=<?php echo $key ?>'">
                    <span class=" material-icons-sharp"> group_add </span>
                    <h3> Add Family </h3>
                </a>

                <a href=" ../PHP/profile-setting.php?key=<?php echo $key; ?>">
                    <span class="material-icons-sharp"> settings</span>
                    <h3> Profile </h3>
                </a>

                <button id="logout">
                    <span class="material-icons-sharp"> logout</span>
                    <h3> Log Out </h3>
                </button>
            </div>
        </aside>


        <!-- main section  -->
        <main>

            <!-- html code to show the important family members  -->
            <div class="w3-container">
                <?php foreach ($family_data_array as $fdata) : ?>
                    <div class="w3-card-4 w3-margin w3-left w3-mobile" style="width:26.5rem">

                        <header class="w3-container w3-light-grey w3-margin-bottom">
                            <h2>
                                <?php echo $fdata['f_name']; ?>
                            </h2>
                            <form name="deleteFamily" method="post">
                                <input type="hidden" name="deleteKey" value="<?php echo $fdata['f_key'] ?>">
                                <button type="submit" class="material-icons-sharp family-del-btn" name="delete-btn"> delete_forever </button>
                            </form>
                        </header>
                        <img src="../<?php echo $fdata['f_photo']; ?>" alt="Avatar" class="w3-left w3-circle w3-margin-right w3-margin-left" style="width:60px">
                        <p> <strong> Phone No. : </strong>
                            <?php echo $fdata['f_contact']; ?>
                        </p>
                        <p> <strong> Email : </strong>
                            <?php echo $fdata['f_email']; ?>
                        </p>
                        <br>
                    </div>

                <?php endforeach; ?>
            </div>


            <div class="w3-card-4 w3-margin w3-mobile" style="width:60%;">
                <header class="w3-container w3-light-grey w3-center">
                    <h2>Get The App </h2>
                </header>
                <div class="w3-container" style="padding: 1rem;">
                    <hr>
                    <p class="w3-center"> Scan the QR code to download the App </p>
                    <img src="../Image/mobile-tracker.svg" alt="APP QR Code" class="w3-border w3-padding" style="position: relative; width:60%; height:18rem; top:-3rem; margin:3.5rem auto -3rem;">
                    <h2 class="w3-center"> Or </h2>
                    <p class="w3-center"> Open the following link in your mobile's Browser.

                        <a href="../app-download.html" style="color: var(--color-btn);">
                            https://trackme.c1.is/app-download.html </a>
                    </p>
                </div>
            </div>

        </main>

        <!-- end of the main section  -->

        <!-- start of the right section  -->
        <div class="right">
            <!-- profile information  -->
            <div class="top">
                <div class="info">
                    <strong>
                        <?php echo $full_name ?>
                    </strong>
                    <small style="display:block" class="text-muted">
                        <?php echo $bio ?>
                    </small>
                </div>

                <div class="profile">
                    <div class="profile-img">
                        <img src="../Image/user-profile.png" alt="Profile photo of user">
                    </div>
                </div>
            </div>

            <!-- notification section  -->
            <input type="hidden" id="hidden_key" value="<?php echo $key ?>">
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
    <script src="../Script/user-dashboard-script.js"></script>
    <script src="../Script/user-notification-script.js"></script>
</body>

</html>