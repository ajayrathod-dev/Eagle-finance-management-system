<?php
session_start();
include("connection.php");

// Read the SQL script
$sql_script = file_get_contents('create_payment_members_table.sql');

// Execute the SQL query
if (mysqli_multi_query($link, $sql_script)) {
    echo "<div style='text-align:center; margin-top:50px;'>
            <h2 style='color:green;'>Payment Members table created successfully!</h2>
            <p>You will be redirected to the payment members page in 3 seconds...</p>
          </div>";
    header("refresh:3;url=payment_members.php");
} else {
    echo "<div style='text-align:center; margin-top:50px;'>
            <h2 style='color:red;'>Error creating Payment Members table: " . mysqli_error($link) . "</h2>
            <p>You will be redirected to the payment members page in 3 seconds...</p>
          </div>";
    header("refresh:3;url=payment_members.php");
}
?>