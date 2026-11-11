<?php
session_start();
include("connection.php");

$current_time = date("M d, Y H:i:s");

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

// Get BC ID from URL
if (!isset($_GET['bc_id']) || !is_numeric($_GET['bc_id'])) {
    header("Location: payment.php?error=Invalid BC ID");
    exit;
}
$bc_id = intval($_GET['bc_id']);

// Fetch BC details
$bc_sql = "SELECT * FROM payments WHERE id = '$bc_id'";
$bc_result = mysqli_query($link, $bc_sql);
if (mysqli_num_rows($bc_result) == 0) {
    header("Location: payment.php?error=BC not found");
    exit;
}
$bc = mysqli_fetch_assoc($bc_result);

include('header.php');
?>

<style>
    .blink_me {
        animation: blinker 1s linear infinite;
        color: white;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <!-- BC Details -->
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-detail me-1 font-22 text-primary"></i></div>
                            <h5 class="mb-0 text-primary">BC Details</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td><?php echo $bc['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>BC Title</th>
                                    <td><?php echo htmlspecialchars($bc['bc_title']); ?></td>
                                </tr>
                                <tr>
                                    <th>BC Description</th>
                                    <td><?php echo htmlspecialchars($bc['bc_description']); ?></td>
                                </tr>
                                <tr>
                                    <th>BC Start Date</th>
                                    <td><?php echo $bc['bc_start_date']; ?></td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td><?php echo number_format($bc['amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?php echo $bc['created_at']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Associated Members -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h5 class="mb-0">Associated Members</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Mobile Number</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $member_sql = "SELECT * FROM payment_members WHERE payment_id = '$bc_id' ORDER BY id DESC";
                                    $member_result = mysqli_query($link, $member_sql);
                                    if (mysqli_num_rows($member_result) > 0) {
                                        while ($member = mysqli_fetch_assoc($member_result)) {
                                            echo "<tr>";
                                            echo "<td>" . $member['id'] . "</td>";
                                            echo "<td>" . htmlspecialchars($member['name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($member['number']) . "</td>";
                                            echo "<td>" . $member['created_at'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>No members found for this BC</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Payment Records -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h5 class="mb-0">Payment Records</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Member Name</th>
                                        <th>Member Number</th>
                                        <th>Amount</th>
                                        <th>Received Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $payment_sql = "SELECT pr.*, pm.name, pm.number 
                                                  FROM payment_received pr 
                                                  LEFT JOIN payment_members pm ON pr.payment_member_id = pm.id 
                                                  WHERE pr.payment_id = '$bc_id' 
                                                  ORDER BY pr.id DESC";
                                    $payment_result = mysqli_query($link, $payment_sql);
                                    if (mysqli_num_rows($payment_result) > 0) {
                                        while ($payment = mysqli_fetch_assoc($payment_result)) {
                                            echo "<tr>";
                                            echo "<td>" . $payment['id'] . "</td>";
                                            echo "<td>" . ($payment['payment_member_id'] ? htmlspecialchars($payment['name']) : "N/A") . "</td>";
                                            echo "<td>" . ($payment['payment_member_id'] ? htmlspecialchars($payment['number']) : "N/A") . "</td>";
                                            echo "<td>" . number_format($payment['amount'], 2) . "</td>";
                                            echo "<td>" . $payment['received_date'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No payment records found for this BC</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<?php include('footer.php'); ?>