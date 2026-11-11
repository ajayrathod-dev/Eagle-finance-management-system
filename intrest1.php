<?php
session_start();
include("connection.php");

// Check login session
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
        $br_id   = mysqli_real_escape_string($link, $row4['user_id']);
    }
} else {
    header("location:logout.php");
    exit;
}

// Function to handle file uploads
function uploadPhoto($fileInput, $prefix) {
    global $link;
    $photo_path = '';
    
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES[$fileInput]["name"], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $target_file)) {
            $photo_path = mysqli_real_escape_string($link, $target_file);
        }
    }
    
    return $photo_path;
}

// Handle file uploads
$customer_photo_path = uploadPhoto('customer_photo', 'customer');
$surety1_photo_path = uploadPhoto('surety1_photo', 'surety1');
$surety2_photo_path = uploadPhoto('surety2_photo', 'surety2');

// Get form values
$account_no           = mysqli_real_escape_string($link, $_POST['account_no']);
$finance_name         = mysqli_real_escape_string($link, $_POST['finance_name']);
$c_name               = mysqli_real_escape_string($link, $_POST['c_name']);
$c_father_name        = mysqli_real_escape_string($link, $_POST['c_father_name']);
$c_pan_no             = mysqli_real_escape_string($link, $_POST['c_pan_no']);
$c_dep_chop_no        = mysqli_real_escape_string($link, $_POST['c_dep_chop_no']);
$address              = mysqli_real_escape_string($link, $_POST['address']);
$phone_no             = mysqli_real_escape_string($link, $_POST['phone_no']);
$c_mobile             = mysqli_real_escape_string($link, $_POST['c_mobile']);
$loan_amount          = mysqli_real_escape_string($link, $_POST['loan_amount']);
$interest_rate        = mysqli_real_escape_string($link, $_POST['interest_rate']);
$total_loan_amount    = mysqli_real_escape_string($link, $_POST['total_loan_amount']);
$loan_date            = mysqli_real_escape_string($link, $_POST['loan_date']);
$emi_date             = mysqli_real_escape_string($link, $_POST['emi_date']);
$daily_interest       = mysqli_real_escape_string($link, $_POST['daily_interest_amount']);
$loan_term            = mysqli_real_escape_string($link, $_POST['loan_term']);
$monthly_emi          = mysqli_real_escape_string($link, $_POST['monthly_emi']);
$paid_by              = mysqli_real_escape_string($link, $_POST['paid_by']);
$cheque_no            = mysqli_real_escape_string($link, $_POST['cheque_no']);
$bank_name            = mysqli_real_escape_string($link, $_POST['bank_name']);

// Surety 1 details
$surety1_name         = mysqli_real_escape_string($link, $_POST['surety1_name']);
$surety1_father_name  = mysqli_real_escape_string($link, $_POST['surety1_father_name']);
$surety1_address      = mysqli_real_escape_string($link, $_POST['surety1_address']);
$surety1_phone        = mysqli_real_escape_string($link, $_POST['surety1_phone']);
$surety1_pan          = mysqli_real_escape_string($link, $_POST['surety1_pan']);
$surety1_dep_chop_no  = mysqli_real_escape_string($link, $_POST['surety1_dep_chop_no']);
$surety1_bank_name    = mysqli_real_escape_string($link, $_POST['surety1_bank_name']);

// Surety 2 details
$surety2_name         = mysqli_real_escape_string($link, $_POST['surety2_name']);
$surety2_father_name  = mysqli_real_escape_string($link, $_POST['surety2_father_name']);
$surety2_address      = mysqli_real_escape_string($link, $_POST['surety2_address']);
$surety2_phone        = mysqli_real_escape_string($link, $_POST['surety2_phone']);
$surety2_pan          = mysqli_real_escape_string($link, $_POST['surety2_pan']);
$surety2_dep_chop_no  = mysqli_real_escape_string($link, $_POST['surety2_dep_chop_no']);
$surety2_bank_name    = mysqli_real_escape_string($link, $_POST['surety2_bank_name']);

// Calculate remaining amount (initially same as total loan amount)
$remaining_amount = $total_loan_amount;

// Insert query with all new fields
$sql = "INSERT INTO `intrest` (
    `account_no`, `finance_name`, `c_name`, `c_father_name`, `c_pan_no`, `c_dep_chop_no`,
    `address`, `phone_no`, `c_mobile`, 
    `loan_amount`, `interest_rate`, `total_loan_amount`, `loan_date`, `emi_date`, 
    `daily_interest_amount`, `loan_term`, `monthly_emi`, `remaining_amount`, `by`,
    `paid_by`, `cheque_no`, `bank_name`, `customer_photo_path`, `surety1_photo_path`, `surety2_photo_path`,
    `surety1_name`, `surety1_father_name`, `surety1_address`, `surety1_phone`, `surety1_pan`, `surety1_dep_chop_no`, `surety1_bank_name`,
    `surety2_name`, `surety2_father_name`, `surety2_address`, `surety2_phone`, `surety2_pan`, `surety2_dep_chop_no`, `surety2_bank_name`
) VALUES (
    '$account_no', '$finance_name', '$c_name', '$c_father_name', '$c_pan_no', '$c_dep_chop_no',
    '$address', '$phone_no', '$c_mobile',
    '$loan_amount', '$interest_rate', '$total_loan_amount', '$loan_date', '$emi_date',
    '$daily_interest', '$loan_term', '$monthly_emi', '$remaining_amount', '$u_uname',
    '$paid_by', '$cheque_no', '$bank_name', '$customer_photo_path', '$surety1_photo_path', '$surety2_photo_path',
    '$surety1_name', '$surety1_father_name', '$surety1_address', '$surety1_phone', '$surety1_pan', '$surety1_dep_chop_no', '$surety1_bank_name',
    '$surety2_name', '$surety2_father_name', '$surety2_address', '$surety2_phone', '$surety2_pan', '$surety2_dep_chop_no', '$surety2_bank_name'
)";

if (mysqli_query($link, $sql)) {
    $p = "New Loan Entry Created Successfully";
    header("Location: intrest.php?success=$p");
    exit;
} else {
    $p = "Loan Entry Not Created: " . mysqli_error($link);
    header("Location: intrest.php?error=$p");
    exit;
}
?>