<?php

session_start();
include("connection.php");

$current_time = date("M d, Y H:i:s");

if (isset($_COOKIE['uuid'])) {
    $u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);
}
else {
    $u_uname = '';
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = mysqli_query($link, $sql4);

if ($result4->num_rows > 0) {
    while ($row4 = $result4->fetch_assoc()) {
        $u_uname = mysqli_real_escape_string($link, $row4['user_id']);
        $br_id = mysqli_real_escape_string($link, $row4['user_id']);
    }
}
else {
// header("location:logout.php");
// exit;
}

$sql44 = "SELECT * FROM admin ";
$result44 = mysqli_query($link, $sql44);

if ($result44->num_rows > 0) {
    while ($row44 = $result44->fetch_assoc()) {
        $unit_rate = mysqli_real_escape_string($link, $row44['unit_rate']);
    }
}

// Get statistics for dashboard
$license_count = 0;
$intrest_count = 0;
$payments_total = 0;
$payment_received_total = 0;
$total_loan_amount = 0;
$remaining_loan_amount = 0;
$payment_members_count = 0;
$total_payment_members_amount = 0;
$upcoming_renewals_count = 0;

// Get RTO Reminder count
$result2 = mysqli_query($link, "SELECT COUNT(*) as count FROM vehicle_license");
if ($result2) {
    $row = mysqli_fetch_assoc($result2);
    $license_count = $row['count'];
}

// Get Total Loans count
$result25 = mysqli_query($link, "SELECT COUNT(*) as count FROM intrest");
if ($result25) {
    $row = mysqli_fetch_assoc($result25);
    $intrest_count = $row['count'];
}

// Get Total Payments amount
$result3 = mysqli_query($link, "SELECT SUM(amount) as total FROM payments");
if ($result3) {
    $row = mysqli_fetch_assoc($result3);
    $payments_total = $row['total'] ? $row['total'] : 0;
}

// Get Total Received Payments amount
$result4 = mysqli_query($link, "SELECT SUM(amount) as total FROM payment_received");
if ($result4) {
    $row = mysqli_fetch_assoc($result4);
    $payment_received_total = $row['total'] ? $row['total'] : 0;
}

// Get Total Loan Amount and Remaining Amount
$result5 = mysqli_query($link, "SELECT SUM(total_loan_amount) as total_loan, SUM(remaining_amount) as remaining FROM intrest");
if ($result5) {
    $row = mysqli_fetch_assoc($result5);
    $total_loan_amount = $row['total_loan'] ? $row['total_loan'] : 0;
    $remaining_loan_amount = $row['remaining'] ? $row['remaining'] : 0;
}

// Get Payment Members count
$result6 = mysqli_query($link, "SELECT COUNT(*) as count FROM payment_members");
if ($result6) {
    $row = mysqli_fetch_assoc($result6);
    $payment_members_count = $row['count'];
}

// Get Total Payment Members Amount (sum of payment_received linked to payment_members)
$result7 = mysqli_query($link, "SELECT SUM(pr.amount) as total FROM payment_received pr 
                               JOIN payment_members pm ON pr.payment_member_id = pm.id");
if ($result7) {
    $row = mysqli_fetch_assoc($result7);
    $total_payment_members_amount = $row['total'] ? $row['total'] : 0;
}

// Get Upcoming Renewals count (renewals within next 30 days)
$result8 = mysqli_query($link, "SELECT COUNT(*) as count FROM vehicle_license 
                               WHERE fitness_renewal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                               OR licence_renewal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                               OR Permit_renual BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                               OR insurance_renewal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                               OR tax_renewal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
if ($result8) {
    $row = mysqli_fetch_assoc($result8);
    $upcoming_renewals_count = $row['count'];
}

include('header.php');
?>

<style>
.table-responsive {
    border-radius: 10px;
}
.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
}
.table th {
    font-weight: 600;
    font-size: 0.85rem;
    white-space: nowrap;
}
.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}
.badge {
    font-size: 0.7rem;
}
.card {
    box-shadow: 0 0.15rem 1rem rgba(0, 0, 0, 0.05);
}
.widgets-icons-2 {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<div class="page-wrapper">
    <div class="page-content">
        <!-- Statistics Cards Row -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
            <!-- RTO Reminder Card -->
            <!--<div class="col">-->
            <!--    <div class="card radius-10 border-start border-0 border-3 border-info">-->
            <!--        <div class="card-body">-->
            <!--            <div class="d-flex align-items-center">-->
            <!--                <div class="flex-grow-1">-->
            <!--                    <p class="mb-1 text-secondary">RTO Reminder</p>-->
            <!--                    <h4 class="my-1 text-info"><?php echo $license_count; ?></h4>-->
            <!--                    <p class="mb-0 font-13 text-truncate">Total Renewal Reminders</p>-->
            <!--                </div>-->
            <!--                <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-3">-->
            <!--                    <i class='bx bxs-calendar'></i>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            <!-- Total Loans Card -->
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-secondary">Total Loans</p>
                                <h4 class="my-1 text-danger"><?php echo $intrest_count; ?></h4>
                                <p class="mb-0 font-13 text-truncate">Active Loan Accounts</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-3">
                                <i class='bx bx-money'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Loan Amount Card -->
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-secondary">Total Loan Amount</p>
                                <h4 class="my-1 text-success">₹<?php echo number_format($total_loan_amount, 2); ?></h4>
                                <p class="mb-0 font-13 text-truncate">All Disbursed Loans</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-3">
                                <i class='bx bx-credit-card'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Remaining Loan Amount Card -->
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-secondary">Remaining Loan Amount</p>
                                <h4 class="my-1 text-warning">₹<?php echo number_format($remaining_loan_amount, 2); ?></h4>
                                <p class="mb-0 font-13 text-truncate">Outstanding Balance</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-3">
                                <i class='bx bx-line-chart'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row of Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mt-2">
            <!-- Total Payments Card -->
            <!--<div class="col">-->
            <!--    <div class="card radius-10 border-start border-0 border-3 border-primary">-->
            <!--        <div class="card-body">-->
            <!--            <div class="d-flex align-items-center">-->
            <!--                <div class="flex-grow-1">-->
            <!--                    <p class="mb-1 text-secondary">Total BC Amount</p>-->
            <!--                    <h4 class="my-1 text-primary">₹<?php echo number_format($payments_total, 2); ?></h4>-->
            <!--                    <p class="mb-0 font-13 text-truncate">All Payment Records</p>-->
            <!--                </div>-->
            <!--                <div class="widgets-icons-2 rounded-circle bg-gradient-ibiza text-white ms-3">-->
            <!--                    <i class='bx bx-wallet'></i>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            <!-- Total Received Payments Card -->
            <!--<div class="col">-->
            <!--    <div class="card radius-10 border-start border-0 border-3 border-info">-->
            <!--        <div class="card-body">-->
            <!--            <div class="d-flex align-items-center">-->
            <!--                <div class="flex-grow-1">-->
            <!--                    <p class="mb-1 text-secondary">Received BC Payments</p>-->
            <!--                    <h4 class="my-1 text-info">₹<?php echo number_format($payment_received_total, 2); ?></h4>-->
            <!--                    <p class="mb-0 font-13 text-truncate">Total Payments Received</p>-->
            <!--                </div>-->
            <!--                <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-3">-->
            <!--                    <i class='bx bx-money'></i>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            <!-- Payment Members Card -->
            <!--<div class="col">-->
            <!--    <div class="card radius-10 border-start border-0 border-3 border-success">-->
            <!--        <div class="card-body">-->
            <!--            <div class="d-flex align-items-center">-->
            <!--                <div class="flex-grow-1">-->
            <!--                    <p class="mb-1 text-secondary">BC Members</p>-->
            <!--                    <h4 class="my-1 text-success"><?php echo $payment_members_count; ?></h4>-->
            <!--                    <p class="mb-0 font-13 text-truncate">Registered Members</p>-->
            <!--                </div>-->
            <!--                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-3">-->
            <!--                    <i class='bx bx-user'></i>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            <!-- Upcoming Renewals Card -->
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-secondary">Upcoming Renewals</p>
                                <h4 class="my-1 text-warning"><?php echo $upcoming_renewals_count; ?></h4>
                                <p class="mb-0 font-13 text-truncate">Next 30 Days</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-3">
                                <i class='bx bx-alarm'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities Section -->
        <div class="row mt-4">
            <!-- Recent Loans -->
            <div class="col-12 col-lg-6 mb-3">
                <div class="card radius-10 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Recent Loans</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Customer</th>
                                        <th width="30%">Amount</th>
                                        <th width="30%">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
$recent_loans = mysqli_query($link, "SELECT c_name, loan_amount, loan_date FROM intrest ORDER BY id DESC LIMIT 5");
if (mysqli_num_rows($recent_loans) > 0) {
    while ($loan = mysqli_fetch_assoc($recent_loans)) {
        echo '<tr>
                                                <td class="text-truncate" title="' . htmlspecialchars($loan['c_name']) . '">' . htmlspecialchars($loan['c_name']) . '</td>
                                                <td>₹' . number_format($loan['loan_amount'], 2) . '</td>
                                                <td>' . date('M d, Y', strtotime($loan['loan_date'])) . '</td>
                                            </tr>';
    }
}
else {
    echo '<tr><td colspan="3" class="text-center py-3">No recent loans</td></tr>';
}
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Payments -->
            <div class="col-12 col-lg-6 mb-3">
                <div class="card radius-10 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Recent Loan Payments</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Amount</th>
                                        <th width="40%">Date</th>
                                        <th width="20%">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
// Get recent payments from both payments and payment_received tables
$recent_payments = mysqli_query($link, "
                                        (SELECT amount as amount, bc_start_date as date, 'Payment' as type FROM payments ORDER BY id DESC LIMIT 10)
                                        UNION ALL
                                        (SELECT amount as amount, received_date as date, 'Received' as type FROM payment_received ORDER BY id DESC LIMIT 10)
                                        ORDER BY date DESC LIMIT 8
                                    ");

if (mysqli_num_rows($recent_payments) > 0) {
    while ($payment = mysqli_fetch_assoc($recent_payments)) {
        echo '<tr>
                                                <td>₹' . number_format($payment['amount'], 2) . '</td>
                                                <td>' . date('M d, Y H:i', strtotime($payment['date'])) . '</td>
                                                <td><span class="badge bg-' . ($payment['type'] == 'Payment' ? 'primary' : 'success') . '">' . $payment['type'] . '</span></td>
                                            </tr>';
    }
}
else {
    echo '<tr><td colspan="3" class="text-center py-3">No recent payments</td></tr>';
}
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Data Section -->
        
<?php include('footer.php'); ?>