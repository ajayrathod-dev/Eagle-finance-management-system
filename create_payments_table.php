<?php
// Include database connection
include('connection.php');

// Read SQL file
$sql = file_get_contents('create_payments_table.sql');

// Execute SQL query
if (mysqli_query($link, $sql)) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 5px;'>"
       . "Payments table created successfully."
       . "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;'>"
       . "Error creating table: " . mysqli_error($link)
       . "</div>";
}

// Redirect to payment page after 3 seconds
echo "<script>setTimeout(function(){ window.location.href = 'payment.php'; }, 3000);</script>";
?>