<?php
// Including the config file
require "../Configuration/config.php";

// Start the session
session_start();

// Function to generate a unique session key
function generateSessionKey($length = 16)
{
    return bin2hex(random_bytes($length));
}

// Getting user values
$username_inpt = $_POST['username'];
$user_pwd_inpt = $_POST['password'];
$user_role = $_POST['role'];

// SQL query to select the data from the database
$query = ($user_role === "admin") ? "SELECT * FROM admin_credentials" : "SELECT * FROM credentials";
$dbname = ($user_role === "admin") ? $admin_dbname : $user_dbname;
$user_credentials = [];

// Checking the connection
try {
    // Creating connection
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error)
        throw new Exception("Connection Failed: " . $conn->connect_error);

    // Execute the query
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $user_credentials[] = [
                'orig_username' => $row['Username'],
                'orig_hash' => $row['Hash'],
                'orig_key' => $row['reg_no']
            ];
        }
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
} catch (Exception $e) {
    die($e->getMessage());
}

// Initialize variable to store found user data
$isValid = false;

// Location based on the user role
$locationNext = ($user_role === "admin") ? "admin-dashboard.php" : "user-dashboard.php";
$loginNext = ($user_role === "admin") ? "../Admin/index.php" : "../login-page.php";

// Iterate over user credentials to find matching username and verify password
foreach ($user_credentials as $credential) {
    if ($username_inpt === $credential['orig_username'] && password_verify($user_pwd_inpt, $credential['orig_hash'])) {
        $isValid = true;

        // Set session variables
        $_SESSION[$user_role . '_username'] = $username_inpt;
        $_SESSION[$user_role . '_key'] = generateSessionKey();
        $_SESSION[$user_role . '_role'] = $user_role;
        $_SESSION[$user_role . '_credentials'] = $credential['orig_key'];

        echo "<script> window.alert('Welcome $username_inpt! We are pleased to see you again.'); </script>";
        echo "<script> window.location.href='../Dashboard/" . $locationNext . "?key=" . $credential['orig_key'] . "'; </script>";
        break;
    }
}

// Check if username and password are correct
if (!$isValid) {
    echo "<script>
             alert('Sorry! User Validation Failed');
             window.location.href='$loginNext';
          </script>";
    exit();
}
