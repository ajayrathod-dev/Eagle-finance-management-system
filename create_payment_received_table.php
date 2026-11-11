<?php
// Include database connection
include("connection.php");

// SQL to create payment_received table
$sql = "CREATE TABLE IF NOT EXISTS payment_received (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    payment_member_id INT(11) NOT NULL,
    amount_received DECIMAL(10,2) NOT NULL,
    notes TEXT,
    received_date DATETIME NOT NULL,
    FOREIGN KEY (payment_member_id) REFERENCES payment_members(id) ON DELETE CASCADE
)";

// Execute query
if (mysqli_query($link, $sql)) {
    echo "Table payment_received created successfully";
} else {
    echo "Error creating table: " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>