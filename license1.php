<?php
session_start();
include("connection.php");

if (isset($_COOKIE['uuid'])) {
    $u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);
} else {
    $u_uname = '';
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = mysqli_query($link, $sql4);

if ($result4->num_rows > 0) {
    while ($row4 = $result4->fetch_assoc()) {
        $u_uname = mysqli_real_escape_string($link, $row4['user_id']);
        $br_id = mysqli_real_escape_string($link, $row4['user_id']);
    }
} else {
    header("location:logout.php");
    exit;
}

 $vh_no = mysqli_real_escape_string($link, $_POST['vh_no']);
    $c_name = mysqli_real_escape_string($link, $_POST['c_name']);
    $c_mobile = mysqli_real_escape_string($link, $_POST['c_mobile']);
    $alt_mob = mysqli_real_escape_string($link, $_POST['alt_mob']);
    $licence_number = mysqli_real_escape_string($link, $_POST['licence_number']);
    $licence_renewal = mysqli_real_escape_string($link, $_POST['licence_renewal']);
    $permit_renual = mysqli_real_escape_string($link, $_POST['Permit_renual']);
    $fitness_renewal = mysqli_real_escape_string($link, $_POST['fitness_renewal']);
    $insurance_renewal = mysqli_real_escape_string($link, $_POST['insurance_renewal']);
    $tax_renewal = mysqli_real_escape_string($link, $_POST['tax_renewal']);

    $sql = "INSERT INTO vehicle_license 
            (vh_no, c_name, c_mobile, alt_mob, licence_number, licence_renewal, permit_renual, fitness_renewal, insurance_renewal, tax_renewal,`by`) 
            VALUES 
            ('$vh_no','$c_name','$c_mobile','$alt_mob','$licence_number','$licence_renewal','$permit_renual','$fitness_renewal','$insurance_renewal','$tax_renewal','$by')";

    if (mysqli_query($link, $sql)) {
        $p = "New License Created Successfully";
    header("Location: license.php?success=$p");
    } else {
        $p = "License Not Created: ";
    header("Location: license.php?error=$p");
    } 
    
  
?>