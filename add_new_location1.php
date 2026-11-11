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




$part_code=mysqli_real_escape_string($link, $_POST['loc_code']);
$part_name=mysqli_real_escape_string($link, $_POST['rack_code']);
$part_desc=mysqli_real_escape_string($link, $_POST['shelf_code']);
$description=mysqli_real_escape_string($link, $_POST['description']);
$additioan_info1=$part_code."-".$part_name."-".$part_desc;

$column_type = "VARCHAR(20) DEFAULT 0";



             $sql = "INSERT INTO `add_new_location`(`location_code`, `rack_code`, `shelf_code`, `description`, `location_code_rack_code_shelf_code`,`by`)
             VALUES ('$part_code','$part_name','$part_desc','$description','$additioan_info1','$u_uname')";
       
            if (mysqli_query($link, $sql)) {
              
              mysqli_query($link,"ALTER TABLE add_new_part ADD COLUMN $part_code $column_type");
         
            $p="New Location Successfully";
             header("Location:add_new_location.php?success=$p");

            
            }
            else
            {
                $p="Location Not Created";
              header("Location:add_new_location.php?error=$p");
             }
    









           
        
      
