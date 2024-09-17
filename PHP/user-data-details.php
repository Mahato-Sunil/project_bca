<!-- php code to populate the data from the database  -->
<?php
// including the config file 
require "../Configuration/config.php";

if (isset($_GET['key'])) {
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

    // get the citizenship number 
    $key = $_GET['key'];

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
            $full_name = $fname . " " . $mname . " " . $lname;

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


    // code to delete the whole details of the users
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-btn'])) {
        // Creating connection to the database using PDO
        try {
            $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);
            // Set the PDO error mode to exception 
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();

            // Prepare and execute DELETE statements for each table
            $deleteQuery = "DELETE FROM bio_data WHERE Citizenship = :CTZN";

            $statement = $conn->prepare($deleteQuery);
            $statement->bindParam(':CTZN', $key);
            $statement->execute();

            $conn->commit();

            // Redirect the user after successful deletion
            echo "<script> 
            alert('Data Successfully Deleted !');
            window.location.href = '../Dashboard/admin-dashboard.php'; 
            </script>";
            exit();
        } catch (PDOException $e) {
            // Handle database errors
            echo "<script> 
            alert('Failed To Delete Data !');
           
            </script>";
            echo "Error : " . $e->getMessage();
            $conn->rollBack();
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
    <link rel="stylesheet" href="../Style/user-details-ui.css">
    <title>Account Details</title>
</head>

<body>
    <div class="container">
        <div class="back-nav-btn" onclick="window.location.href='../Dashboard/admin-dashboard.php?key=admin_1'">
            <i class="material-icons">arrow_back</i>
            <p> Dashboard </p>
        </div>
        <div class="tabs">
            <div class="tab active">Account Details of :
                <?php echo $full_name ?>
            </div>
        </div>
        <div class="tab-content active">
            <div class="form-group">
                <label> Personal Details</label>
                <p>DOB :
                    <?php echo $dob ?>
                </p>
                <p>Citizenship No. :
                    <?php echo $key ?>
                </p>
            </div>

            <div class="form-group">
                <label>Contact Information </label>
                <p>Phone No. :
                    <?php echo $primary_phone ?>
                </p>
                <p>Secondary Phone No.:
                    <?php echo $secondary_phone ?>
                </p>
                <p>Email Id :
                    <?php echo $email ?>
                </p>
            </div>

            <div class="form-group">
                <label>Address Information</label>
                <table>
                    <thead>
                        <tr>
                            <th> </th>
                            <th> Province </th>
                            <th> District </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td> Permanent Address : </td>
                            <td>
                                <?php echo $perm_province ?>
                            </td>
                            <td>
                                <?php echo $perm_district ?>
                            </td>
                        </tr>

                        <tr>
                            <td> Temporary Address : </td>
                            <td>
                                <?php echo $temp_province ?>
                            </td>
                            <td>
                                <?php echo $temp_district ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <button name="user-location-data" id="openModal" class="btn"> View Recent
                    Location Data </button>
            </div>

            <!-- html code to show the location in the model box  -->

            <div id="myModal" class="modal">
                <div class="modal-content">
                    <iframe src="../PHP/userdata.php?key=<?php echo $key ?>"></iframe>
                </div>
            </div>


            <div class="form-group">
                <form name="user-delete" id="user-delete" method="post">
                    <label> Account Deletion </label>
                    <div class="form-group">
                        <p> This action is Permanent and cannot be undone</p>
                        <button class="delete" name="delete-btn"> Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../Script/user-dashboard-script.js"></script>
    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModal");

        btn.onclick = function() {
            modal.style.display = "block";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>