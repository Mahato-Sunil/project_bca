<!-- php code to populate the data from the database  -->
<?php
// including the config file 

require "../Configuration/config.php";
//  global declaration for the data from database 
$fname = "";
$mname = "";
$lname = "";
$dob = "";

$primary_phone = "";
$secondary_phone = "";
$email = "";

$perm_province = "";
$perm_district = "";

$temp_province = "";
$temp_district = "";

// get the citizenship number 
$key = (isset($_GET['key'])) ? $_GET['key'] : '';
global $key;
// creating connection  to the database using PDO
try {
    $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);

    // set the pdo error mode to exception 
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // prepare sql statement and bind the parameters
    //  query to select the  data from database 
    $query = "SELECT * FROM bio_data
                            INNER JOIN contact_info ON bio_data.Citizenship = contact_info.Ctzn_no                            
                            INNER JOIN current_address ON bio_data.Citizenship = current_address.Ctzn_no                            
                            INNER JOIN permanent_address ON bio_data.Citizenship = permanent_address.Ctzn_no                            
                            WHERE bio_data.Citizenship = :CTZN";

    $statement = $conn->prepare($query);
    $statement->bindParam(':CTZN', $key);

    //  execute the statment 
    $statement->execute();

    // fetching the result 
    $data = $statement->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $fname = $data['FirstName'];
        $mname = $data['MiddleName'];
        $lname = $data['LastName'];

        $dob = $data['DOB'];

        $primary_phone = $data['PrimaryPhone'];
        $secondary_phone = $data['SecondaryPhone'];
        $email = $data['Email'];

        $temp_province = $data['Province'];
        $temp_district = $data['District'];

        $perm_province = $data['Province_p'];
        $perm_district = $data['District_p'];

    }
} catch (PDOException $e) {
    echo 'Error in PDO : ' . $e->getMessage();
}
?>

<!-- php script to update the data from the  -->
<?php
// include the config fie
//only run the script if the  user clicks on the submit button 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit-btn'])) {

        $key = (isset($_GET['key'])) ? $_GET['key'] : '';

        // get the new data from the user fields
        $edit_fname = $_POST['first-name'];
        $edit_mname = $_POST['middle-name'];
        $edit_lname = $_POST['last-name'];

        $edit_dob = $_POST['dob'];
        $edit_ctzn = $_POST['citizenship'];

        $edit_primary_phone = $_POST['contact-number'];
        $edit_secondary_phone = $_POST['sec-conct-num'];
        $edit_email = $_POST['email'];

        $edit_perm_province = $_POST['p-province'];
        $edit_perm_district = $_POST['p-district'];

        $edit_temp_province = $_POST['t-province'];
        $edit_temp_district = $_POST['t-district'];

        // creating the connection to the data base and updating the database
        try {
            // Creating connection to the database using PDO
            $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "UPDATE bio_data b
          INNER JOIN contact_info c ON b.Citizenship = c.Ctzn_no
          INNER JOIN current_address ca ON b.Citizenship = ca.Ctzn_no
          INNER JOIN permanent_address pa ON b.Citizenship = pa.Ctzn_no
          SET b.FirstName = :FNAME, 
              b.MiddleName = :MNAME, 
              b.LastName = :LNAME, 
              b.DOB = :DOB_C, 
              b.Citizenship = :CTZN,
              c.PrimaryPhone = :P_PHONE, 
              c.SecondaryPhone = :S_PHONE, 
              c.Email = :EMAIL, 
              c.Ctzn_no = :CTZN,
              ca.Province = :PROVINCE, 
              ca.District = :DISTRICT, 
              ca.Ctzn_no = :CTZN,
              pa.Province_p = :PROVINCE_P, 
              pa.District_p = :DISTRICT_P, 
              pa.Ctzn_no = :CTZN
          WHERE b.Citizenship = :ctznId";

            // preparing the statement
            $statement = $conn->prepare($query);

            // binding the statements
            $statement->bindParam(':CTZN', $edit_ctzn);
            $statement->bindParam(':FNAME', $edit_fname);
            $statement->bindParam(':MNAME', $edit_mname);
            $statement->bindParam(':LNAME', $edit_lname);
            $statement->bindParam(':DOB_C', $edit_dob);

            $statement->bindParam(':P_PHONE', $edit_primary_phone);
            $statement->bindParam(':S_PHONE', $edit_secondary_phone);
            $statement->bindParam(':EMAIL', $edit_email);

            $statement->bindParam(':PROVINCE', $edit_temp_province);
            $statement->bindParam(':DISTRICT', $edit_temp_district);

            $statement->bindParam(':PROVINCE_P', $edit_perm_province);
            $statement->bindParam(':DISTRICT_P', $edit_perm_district);

            $statement->bindParam(':ctznId', $key);
            // execute the statment
            $result = $statement->execute();

            $loc = "../Dashboard/user-dashboard.php?key=" . $edit_ctzn;

            if ($result > 0) {
                echo " <script>
                        alert('Data is updated Successfully \\nRedirecting to the Dashboard');
                        window.location.href ='../Dashboard/user-dashboard.php?key=" . $key . "';
                        </script>
                     ";
            } else {
                echo "
                    <script>
                       alert('Failed To Update Data !');
                          window.location.href = '../edit-data.php?key=" . $key . "';
                    </script>";
            }

        } catch (PDOException $e) {
            echo 'Error in PDO :' . $e->getMessage();
        }
    }
}
?>
<!-- html part  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../Style/registration-ui.css">
    <title>Edit Criminal Data</title>
</head>

<body>
    <div class="container">
        <div class="top-bar">
            <div class="logo">
                <a href="../index.html">
                    <img src="../Image/app_launcher.svg" alt="Smart Mobile Tracker">
                </a>
            </div>
            <small class="text-muted"> Click on the Respective Field to Edit </small>
        </div>

        <!-- back navigation button  -->
         
        <div class="back-nav-btn"
            onclick="window.location.href='../Dashboard/user-dashboard.php?key=<?php echo $key ?>'">
            <i class="material-icons">arrow_back</i>
            <p> Dashboard </p>
        </div>

        <form id="user-registration" name="user-data-edit" method="post"
            action="<?php echo $_SERVER['PHP_SELF']; ?>?key=<?php echo $key; ?>">

            <!-- code for the personal information  -->
            <div class="form-contents">
                <h3> Edit Personal Information :</h3>
                <label for="first-name"> First_Name : Middle_Name : Last_Name <span> * </span></label>
                <input type="text" name="first-name" value="<?php echo $fname ?>" requried>
                <input type="text" name="middle-name" value="<?php echo $mname ?>">
                <input type="text" name="last-name" value="<?php echo $lname ?>" required>

                <label for="dob"> DOB [dd-mm-yyyy] <span> * </span> </label>
                <input type="text" name="dob" value='<?php echo $dob ?>' required>

                <label for="citizenship"> Citizenship Number <span> * </span></label>
                <input type="text" name="citizenship" value="<?php echo $key ?>" maxlength="14" required>

            </div>

            <!-- html code for the contact section  -->
            <div class="form-contents">
                <h3> Edit Contact Information :</h3>

                <label for="contact-number">Contact Number <span> * </span></label>
                <input type="tel" name="contact-number" value="<?php echo $primary_phone ?>" maxlength="10" required>

                <label for="sec-conct-num"> Secondary Mobile Number </label>
                <input type="tel" name="sec-conct-num" value="<?php echo $secondary_phone ?>" maxlength="10">

                <label for="email"> Email Address <span> * </span></label>
                <input type="email" name="email" value="<?php echo $email ?>" required>
            </div>

            <!-- html code for the location section  -->
            <div class="form-contents">
                <h3> Edit Location Information</h3>
                <h2> Temporary Address </h2>
                <label for="t-province">Province <span> * </span></label>
                <input type="text" name="t-province" id="t-province" value="<?php echo $temp_province ?>" required>

                <label for="t-district">District <span> * </span></label>
                <input type="text" name="t-district" id="t-district" value="<?php echo $temp_district ?>" required>

                <div class="isSameAddressDiv">
                    <input type="checkbox" name="isSameAddress" id="isSameAddress">
                    <label for="isSameAddress"> Permanent Address is same as Temporary Address. </label>
                </div>

                <h2> Permanent Address </h2>

                <label for="p-province">Province <span> * </span> </label>
                <input type="text" name="p-province" id="p-province" value="<?php echo $perm_province ?>" required>

                <label for="p-district">District <span> * </span></label>
                <input type="text" name="p-district" id=p_district" value="<?php echo $perm_district ?>" required>

                <div class="page-nav-button">
                    <input type="submit" name="edit-btn" value="Edit The Data">
                </div>
            </div>
        </form>

    </div>
    <script src=" ../Script/input-field-validation.js">
    </script>
</body>

</html>