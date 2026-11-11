<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Me Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            flex-direction: column;
        }
        table {
            width: 50%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h2>Location Tracker</h2>
    <table>
        <thead>
            <tr>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database credentials
            $host = '127.0.0.1'; // Use this for database host
            $db_username = 'amarnat2_balaji'; // Replace with your database username
            $db_password = 'RshRsh@#2407'; // Replace with your database password
            $db_database = 'amarnat2_dairy'; // Replace with your database name

            // Establish database connection
            $conn = new mysqli($host, $db_username, $db_password, $db_database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch data from database
            $sql = "SELECT id, latitude, longitude, time FROM gps";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["latitude"] . "</td><td>" . $row["longitude"] . "</td><td>" . $row["time"] . "</td>";
                    echo "<td><button onclick=\"showPopup('" . $row["latitude"] . "', '" . $row["longitude"] . "')\">View Location</button></td></tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No results</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <script>
    function showPopup(latitude, longitude) {
        // Show a confirmation popup
        if (confirm("Do you want to view this location on Google Maps?")) {
            // If the user clicks "OK", redirect to Google Maps with the latitude and longitude
            window.location.href = "https://www.google.com/maps?q=" + latitude + "," + longitude;
        }
    }
    </script>
</body>
</html>
