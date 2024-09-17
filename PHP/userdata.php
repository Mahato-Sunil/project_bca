<!-- php code 
<?php
// Declarations
require "../Configuration/config.php";

// Creating the connection with the database
$conn = new mysqli($host, $username, $password, $user_dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "";

// Check if a specific user ID is provided
if (isset($_GET['key'])) {
    $key = $_GET['key'];
    // Query to select data for a specific user
    $sql = "SELECT * FROM location WHERE Citizenship = '$key' ORDER BY Time DESC";
} else {
    // Query to select all data, ordered by timestamp or ID in descending order
    $sql = "SELECT * FROM location ORDER BY Time DESC"; // Assuming 'Time' is a timestamp column
}

$result = $conn->query($sql);
// Check if query was successful
if ($result) {
    // Fetch all data as an associative array
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $userdata = [];
    // Create an array of data to store the variables
    foreach ($data as $row) {
        $userdata[] = [
            'Id' => $row['Id'],
            'Name' => $row['Name'],
            'Phone' => $row['Phone'],
            'Citizenship' => $row['Citizenship'],
            'Latitude' => $row['Latitude'],
            'Longitude' => $row['Longitude'],
            'Time' => $row['Time']
        ];
    }
} else {
    echo "Error: " . $conn->error;
}
// Close connection
$conn->close();
?> -->


<!-- html code  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Display Section</title>
    <style>
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: var(--card-box-padding);
        }

        .user-data {
            position: relative;
            width: 100%;
            left: 0;
            margin-top: 11.5%;
            height: fit-content;
            border-collapse: separate;
            border-spacing: 0;
            background: white
        }

        .user-data td,
        .user-data th {
            padding: 0.5rem;
            border-bottom: 1px solid #ddd;
            height: 3rem;
        }

        .user-data tr {
            cursor: pointer;
        }

        .user-data tr:hover {
            background-color: #ddd;
        }

        .user-data th {
            background: whitesmoke;
        }


        /* reponsive design  */
        @media screen and (max-width : 600px) {
            .user-data-mobile {
                width: 95%;
                height: fit-content;
                overflow-x: auto;
                white-space: nowrap;
            }

            .user-data th,
            .user-data td {
                width: fit-content;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>User Information</h1>
        <small>Click on respective field to view details </small>
        <hr>
        <div class="user-data-mobile">
            <table class="user-data">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Citizenship</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userdata as $d) : ?>
                        <tr>
                            <td>
                                <?php echo $d['Id']; ?>
                            </td>
                            <td class="name">
                                <?php echo $d['Name']; ?>
                            </td>
                            <td class="phone">
                                <?php echo $d['Phone']; ?>
                            </td>
                            <td class="key">
                                <?php echo $d['Citizenship']; ?>
                            </td>
                            <td class="lat">
                                <?php echo $d['Latitude']; ?>
                            </td>
                            <td class="lon">
                                <?php echo $d['Longitude']; ?>
                            </td>
                            <td class="time">
                                <?php echo $d['Time']; ?>
                            </td>
                        </tr>
                        </a>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../Script/map_script.js">
    </script>
</body>

</html>