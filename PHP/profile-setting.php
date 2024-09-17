<?php
// require the config file 
require "../Configuration/config.php";

// Declaration for the user data
$key = (isset($_GET['key'])) ? $_GET['key'] : '';
$user_name = "";
$alt_email = "";
$bio = "";

// Creating connection to the database using PDO
try {
    $conn = new PDO("mysql:host=$host; dbname=$user_dbname", $username, $password);
    // Set the PDO error mode to exception 
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL statement and bind the parameters
    $query = "SELECT user_setting.*, credentials.Username 
              FROM user_setting
              RIGHT JOIN credentials ON credentials.reg_no = user_setting.user_key
              WHERE credentials.reg_no = :KEY";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':KEY', $key);
    // Execute the statement 
    $stmt->execute();

    // Fetching the result 
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $alt_email = ($data['Alternate_Email'] != "") ? $data['Alternate_Email'] : "Alternate Email Not Set";
        $user_name = ($data['Username'] != "") ? $data['Username'] : "Username Not Found";
        $bio = $data['Bio'];
    }
} catch (PDOException $e) {
    echo 'Error in PDO: ' . $e->getMessage();
}

// Code to update the data to the database 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save-user-setting'])) {
    $new_user_name = $_POST['username'];
    $new_alt_email = $_POST['alt-email'];
    $new_bio = $_POST['bio'];

    // Storing the data to the server 
    try {
        $conn->beginTransaction();

        // Prepare SQL statement and bind the parameters
        $query = "UPDATE credentials SET Username = :NEW_USER_NAME  WHERE reg_no = :KEY";
        $query1 = "UPDATE user_setting SET Alternate_Email = :ALT_EMAIL, Bio = :BIO WHERE user_key = :KEY";

        $stmt = $conn->prepare($query);
        $stmt1 = $conn->prepare($query1);

        $stmt->bindParam(':KEY', $key);
        $stmt->bindParam(':NEW_USER_NAME', $new_user_name);

        $stmt1->bindParam(':ALT_EMAIL', $new_alt_email);
        $stmt1->bindParam(':BIO', $new_bio);
        $stmt1->bindParam(':KEY', $key);

        // Execute the statement 
        $stmt->execute();
        $stmt1->execute();

        $conn->commit();

        // Redirect the user back to the updated page
        echo "<script> 
        alert('Congrats ! Profile Updated ..');
        window.location.href = '{$_SERVER['PHP_SELF']}?key={$key}';
        </script>";
        exit();
    } catch (PDOException $e) {
        // Error message
        echo "<script> 
            alert('Sorry ! Failed To Update Profile!');
            </script>";
        echo $e->getMessage();
        $conn->rollBack();
    }
}

// code to update the password of the user 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pwd-change-btn'])) {
    $new_pwd = $_POST['password'];
    $new_pwd_confirm = $_POST['confirm_pwd'];
    $new_hash = password_hash($new_pwd_confirm, PASSWORD_DEFAULT);

    if ($new_pwd !== $new_pwd_confirm) {
        echo "<script> alert('The password doesn't match') </script>";
        exit();
    }

    // Storing the data to the server 
    try {
        $conn->beginTransaction();

        // Prepare SQL statement and bind the parameters
        $query = "UPDATE credentials SET HASH = :NEW_HASH WHERE reg_no = :KEY";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':KEY', $key);
        $stmt->bindParam(':NEW_HASH', $new_hash);

        // Execute the statement 
        $stmt->execute();

        $conn->commit();

        // Redirect the user back to the updated page
        echo "<script>  alert('Dear User \\n Password has been Successfully Updated!'); </script>";
        session_start();
        $_SESSION = array();    // Unset all session variables
        session_destroy();      // Destroy the session
        header("Location: ../login-page.php");
        exit();
    } catch (PDOException $e) {
        // Error message
        echo "<script> alert('Sorry ! Failed To Update Profile!'); </script>";
        echo $e->getMessage();
        $conn->rollBack();
    }
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
            window.location.href = '../index.html'; 
            </script>";
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        echo "<script> 
            alert('Failed To Delete Data !');
            window.location.href = '../Dashboard/user-dashboard.php?key=$key';
            </script>";
        echo "Error : " . $e->getMessage();
        $conn->rollBack();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/user-setting-ui.css">
    <title>Profile Settings</title>
</head>

<body>
    <div class="container">
        <div class="back-nav-btn" onclick="window.location.href='../Dashboard/user-dashboard.php?key=<?php echo $key ?>'">
            <i class="material-icons">arrow_back</i>
            <p> Dashboard </p>
        </div>
        <div class="tabs">
            <div class="tab active">Profile</div>
        </div>
        <div class="tab-content active">
            <form name="user-setting" id="user-setting" method="post">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <small class="text-muted">( Max 50 characters )</small>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_name) ?>" placeholder="Enter your username" maxlength="50">

                </div>
                <div class="form-group">
                    <label for="bio">Your bio</label>
                    <small class="text-muted" id="bio_msg">Words Must be less than 50 characters </small>
                    <textarea id="bio" name="bio" rows="4" placeholder="Write a short introduction"><?php echo $bio ?></textarea>
                </div>
                <div class="form-group">
                    <label for="alt-email">Alternative contact email</label>
                    <small class="text-muted"><?php echo htmlspecialchars($alt_email) ?></small>
                    <input type="email" id="alt-email" name="alt-email" placeholder="example@example.com">
                    <small id="emailMsg" class="errorMsg"></small>
                </div>

                <div class="form-group">
                    <button type="submit" id="btn" name="save-user-setting">Save changes</button>
                </div>
            </form>

            <form method="post" name="password">
                <div class="form-group">
                    <label for="alt-email">Change Password</label>
                    <small class="text-muted"> New Password : </small>
                    <input type="password" id="new_pwd" name="password">
                    <small class="text-muted"> Confirm Password : </small>
                    <input type="password" id="confirm_new_pwd" name="confirm_pwd">
                    <small id="pwdMsg" class="errorMsg"></small>
                    <button name="pwd-change-btn" id="pwd-change-btn"> Change Password</button>
                </div>
            </form>

            <div class="form-group">
                <form name="user-delete" id="user-delete" method="post">
                    <label> Account Deletion </label>
                    <div class="form-group">
                        <p> This action is Permanent and cannot be cancelled</p>
                        <button class="delete" name="delete-btn"> Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../Script/user-setting-script.js"></script>
</body>

</html>