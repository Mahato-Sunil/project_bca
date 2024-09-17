<?php
// including the config file 

require "../Configuration/config.php";

//check if the user with the email is already registered to the system or not
try {
    // get the  email and ctzn from the user 
    $user_ctzn = $_POST['citizenship'];
    $user_email = $_POST['email'];
    $isDataAvailable = false;

    $pdo = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = "SELECT Email, Ctzn_no FROM contact_info";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dataArray = [];
    if ($data) {
        $dataArray = [];
        foreach ($data as $row) {
            $dataArray[] = [
                'db_email' => $row['Email'],
                'db_ctzn' => $row['Ctzn_no']
            ];
        }
    }

    //checking for the data 

    foreach ($dataArray as $check) {
        if ($check['db_email'] == $user_email || $check['db_ctzn'] == $user_ctzn) {
            $isDataAvailable = true;
            break;
        }
    }


    if (!$isDataAvailable)
        registerUser($host, $user_dbname, $username, $password);
    else {
        echo "<script> alert('The User is already registered !'); 
               window.location.href = '../Registration/user-registration.html'
              </script>";

        exit();
    }
    // Close the connection
    $pdo = null;
} catch (PDOException $e) {
    // Error message
    echo "<script> 
    alert('USER REGISTRATION FAILED !');
    window.history.back()';
    </script>";
    echo $e->getMessage();
}


function registerUser($host, $user_dbname, $username, $password)
{
    try {
        // Create a PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();   //ensuring that all the transaction are successful 

        //getting the values from the user 
        $fname = $_POST['first-name'];
        $mname = $_POST['middle-name'];
        $lname = $_POST['last-name'];
        $dob = $_POST['dob'];
        $citizenship = $_POST['citizenship'];

        $primary_phone = $_POST['contact-number'];
        $secondary_phone = $_POST['sec-conct-num'];
        $email = $_POST['email'];

        $perm_province = $_POST['p-province'];
        $perm_district = $_POST['p-district'];

        $temp_province = $_POST['t-province'];
        $temp_district = $_POST['t-district'];

        //for password 
        $username_inpt = $email;
        $status = "Pending";

        // Define the SQL queries
        $sql1 = "INSERT INTO bio_data (FirstName, MiddleName, LastName, DOB, Citizenship) VALUES (:fname, :mname, :lname, :dob, :citizenship)";
        $sql2 = "INSERT INTO contact_info (PrimaryPhone, SecondaryPhone, Email, Ctzn_no) VALUES (:primary_phone, :secondary_phone, :email, :citizenship)";
        $sql3 = "INSERT INTO current_address (Province, District, Ctzn_no) VALUES (:perm_province, :perm_district, :citizenship)";
        $sql4 = "INSERT INTO permanent_address (Province_p, District_p, Ctzn_no) VALUES (:temp_province, :temp_district,:citizenship)";
        $sql5 = "INSERT INTO credentials (Username, reg_no) VALUES (:username_inpt, :citizenship)";
        $sql6 = "INSERT INTO user_setting (user_key) VALUES (:user_key)";
        $sql7 = "INSERT INTO user_status (Status, userKey) VALUES (:status, :userKey)";

        // Prepare SQL statements
        $stmt1 = $pdo->prepare($sql1);
        $stmt2 = $pdo->prepare($sql2);
        $stmt3 = $pdo->prepare($sql3);
        $stmt4 = $pdo->prepare($sql4);
        $stmt5 = $pdo->prepare($sql5);
        $stmt6 = $pdo->prepare($sql6);
        $stmt7 = $pdo->prepare($sql7);

        // Bind parameters
        $stmt1->bindParam(':fname', $fname);
        $stmt1->bindParam(':mname', $mname);
        $stmt1->bindParam(':lname', $lname);
        $stmt1->bindParam(':dob', $dob);
        $stmt1->bindParam(':citizenship', $citizenship);

        $stmt2->bindParam(':primary_phone', $primary_phone);
        $stmt2->bindParam(':secondary_phone', $secondary_phone);
        $stmt2->bindParam(':email', $email);
        $stmt2->bindParam(':citizenship', $citizenship);

        $stmt3->bindParam(':perm_province', $perm_province);
        $stmt3->bindParam(':perm_district', $perm_district);
        $stmt3->bindParam(':citizenship', $citizenship);

        $stmt4->bindParam(':temp_province', $temp_province);
        $stmt4->bindParam(':temp_district', $temp_district);
        $stmt4->bindParam(':citizenship', $citizenship);

        $stmt5->bindParam(':username_inpt', $username_inpt);
        $stmt5->bindParam(':citizenship', $citizenship);

        $stmt6->bindParam(':user_key', $citizenship);

        $stmt7->bindParam(':status', $status);
        $stmt7->bindParam(':userKey', $citizenship);

        // Execute the queries
        $stmt1->execute();
        $stmt2->execute();
        $stmt3->execute();
        $stmt4->execute();
        $stmt5->execute();
        $stmt6->execute();
        $stmt7->execute();

        $pdo->commit();

        echo "<script> 
        window.location.href = '../success.html';
        </script>";
    } catch (PDOException $e) {
        // Error message
        echo "<script> 
        alert('USER REGISTRATION FAILED !');
        window.location.href = '../Registration/user-registration.html';
        </script>";
        echo $e->getMessage();
        $pdo->rollBack();
    }

    // Close the connection
    $pdo = null;
}
