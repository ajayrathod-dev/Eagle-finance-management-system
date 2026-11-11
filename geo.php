<!DOCTYPE html>
<html>
<body>
<h1>lets track location</h1>
<p>Click the button to get your coordinates.</p>

<button onclick="getLocation()">Try It</button>


<input type='text' id="location">
<?php
// Database credentials
$host = '127.0.0.1'; // Use this for database host
$db_username = 'amarnat2_balaji'; // Replace with your database username
$db_password = 'RshRsh@#2407'; // Replace with your database password
$db_database = 'amarnat2_dairy'; // Replace with your database name

// Establish database connection
$conn = mysqli_connect($host, $db_username, $db_password, $db_database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}



$sql = "INSERT INTO gps (latitude,longitude,time)
VALUES ('latitude', 'longitude', 'time')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>


<script>
const x = document.getElementById("location");

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
  x.innerHTML = "Latitude: " + position.coords.latitude + 
  "<br>Longitude: " + position.coords.longitude;
}
//window.location.replace("https://www.google.co.in/maps");
</script>

</body>
</html>

