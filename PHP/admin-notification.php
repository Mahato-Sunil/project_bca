<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Function to query and send the data to the client side
function queryData()
{
    require "../Configuration/config.php"; // Include the required configuration

    // Creating connection to the database using PDO
    try {
        $conn = new PDO("mysql:host=$host;dbname=$user_dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Querying the database
        $sql = "SELECT * FROM Notification ORDER BY NoticeTime desc"; // Modify to fetch the latest notification
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetching the result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            foreach ($result as $data) {
                $notice_body = $data['NoticeMsg'];
                $notice_time = $data['NoticeTime'];
                sendMessage($notice_body, $notice_time);
            }
        }
    } catch (PDOException $e) {
        sendMessage("Error in PDO: " . $e->getMessage(), null);
    }
}

// Function to send a message to the client
function sendMessage($notice_body, $notice_time)
{
    echo "data: " . json_encode(['noticeMsg' => $notice_body, 'noticeTime' => $notice_time]) . "\n\n";
    ob_flush();
    flush();
}

// Send initial headers immediately
ob_end_clean();
flush();

// Send data periodically
while (true) {
    queryData();
    sleep(5); // Sleep for five seconds
}