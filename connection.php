<?php

date_default_timezone_set("Asia/Kolkata");
$DB_SERVER = "localhost";
$DB_NAME = "finance";
$username = "root";
$password = "";
$link = mysqli_connect($DB_SERVER, $username, $password, $DB_NAME);
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}else{
    //echo "connected";
}


?>