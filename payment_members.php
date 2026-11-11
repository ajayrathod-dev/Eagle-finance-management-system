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
	//header("location:logout.php");
	//exit;
}

// Process form submission
if (isset($_POST['submit_payment_member'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $number = mysqli_real_escape_string($link, $_POST['number']);
    $payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : null;
    $created_at = date("Y-m-d H:i:s");
    
    $sql = "INSERT INTO payment_members (name, number, payment_id, created_at) 
            VALUES ('$name', '$number', " . ($payment_id ? "'$payment_id'" : "NULL") . ", '$created_at')";
    
    if (mysqli_query($link, $sql)) {
        header("Location: payment_members.php?success=Payment Member Added Successfully");
    } else {
        header("Location: payment_members.php?error=Failed to add payment member: " . mysqli_error($link));
    }
}

// Delete payment member record
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM payment_members WHERE id='$delete_id'";
    if (mysqli_query($link, $del_sql)) {
        header("Location: payment_members.php?success=Payment Member Deleted");
    } else {
        header("Location: payment_members.php?error=Delete Failed: " . mysqli_error($link));
    }
}

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
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i></div>
                            <h5 class="mb-0 text-primary">Add BC Member</h5>
                        </div>
                       
                        <form class="row g-3" method="post" action="">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="number" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="number" name="number" required>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_id" class="form-label">Select BC</label>
                                <select class="form-select" id="payment_id" name="payment_id">
                                    <option value="">-- Select BC --</option>
                                    <?php
                                    $payment_sql = "SELECT id, bc_title, bc_start_date, amount FROM payments ORDER BY bc_start_date DESC";
                                    $payment_result = mysqli_query($link, $payment_sql);
                                    if (mysqli_num_rows($payment_result) > 0) {
                                        while ($payment_row = mysqli_fetch_assoc($payment_result)) {
                                            echo "<option value='" . $payment_row['id'] . "'>" . htmlspecialchars($payment_row['bc_title']) . " - Date: " . $payment_row['bc_start_date'] . " - Amount: " . number_format($payment_row['amount'], 2) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" name="submit_payment_member" class="btn btn-primary px-5">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Payment Members Records Table -->
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Mobile Number</th>
                                        <th>BC</th>
                                        <th>BC Start Date</th>
                                        <th>Amount</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr=1;
                                    $sql = "SELECT pm.*, p.bc_title, p.bc_start_date, p.amount 
                                           FROM payment_members pm 
                                           LEFT JOIN payments p ON pm.payment_id = p.id 
                                           ORDER BY pm.id DESC";
                                    $result = mysqli_query($link, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $sr . "</td>";
                                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['number']) . "</td>";
                                            echo "<td>" . ($row['payment_id'] ? htmlspecialchars($row['bc_title']) : "N/A") . "</td>";
                                            echo "<td>" . ($row['payment_id'] ? $row['bc_start_date'] : "N/A") . "</td>";
                                            echo "<td>" . ($row['payment_id'] ? number_format($row['amount'], 2) : "N/A") . "</td>";
                                            echo "<td>" . $row['created_at'] . "</td>";
                                            echo "<td>";
                                            echo "<a href='payment_members.php?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this payment member?')\"><i class='fadeIn animated bx bx-trash'></i></a>";
                                            echo "</td>";
                                            echo "</tr>";
                                            $sr++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No payment member records found</td></tr>";
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

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>