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
if (isset($_POST['submit_payment'])) {
    $bc_title = mysqli_real_escape_string($link, $_POST['bc_title']);
    $bc_description = mysqli_real_escape_string($link, $_POST['bc_description']);
    $bc_start_date = mysqli_real_escape_string($link, $_POST['bc_start_date']);
    $amount = mysqli_real_escape_string($link, $_POST['amount']);
    $created_at = date("Y-m-d H:i:s");
    
    $sql = "INSERT INTO payments (bc_title, bc_description, bc_start_date, amount, created_at) 
            VALUES ('$bc_title', '$bc_description', '$bc_start_date', '$amount', '$created_at')";
    
    if (mysqli_query($link, $sql)) {
        header("Location: payment.php?success=Payment Added Successfully");
    } else {
        header("Location: payment.php?error=Failed to add payment");
    }
}

// Delete payment record
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM payments WHERE id='$delete_id'";
    if (mysqli_query($link, $del_sql)) {
        header("Location: payment.php?success=Payment Deleted");
    } else {
        header("Location: payment.php?error=Delete Failed");
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
                            <h5 class="mb-0 text-primary">Add Payment</h5>
                        </div>
                    
                        <form class="row g-3" method="post" action="">
                            <div class="col-md-6">
                                <label for="bc_title" class="form-label">BC Title</label>
                                <input type="text" class="form-control" id="bc_title" name="bc_title" required>
                            </div>
                            <div class="col-md-6">
                                <label for="bc_description" class="form-label">BC Description</label>
                                <input type="text" class="form-control" id="bc_description" name="bc_description" required>
                            </div>
                            <div class="col-md-6">
                                <label for="bc_start_date" class="form-label">BC Start Date</label>
                                <input type="date" class="form-control" id="bc_start_date" name="bc_start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" name="submit_payment" class="btn btn-primary px-5">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Payment Records Table -->
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>BC Title</th>
                                        <th>BC Description</th>
                                        <th>BC Start Date</th>
                                        <th>Amount</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr=1;
                                    $sql = "SELECT * FROM payments ORDER BY id DESC";
                                    $result = mysqli_query($link, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $sr . "</td>";
                                            echo "<td>" . htmlspecialchars($row['bc_title']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['bc_description']) . "</td>";
                                            echo "<td>" . $row['bc_start_date'] . "</td>";
                                            echo "<td>" . number_format($row['amount'], 2) . "</td>";
                                            echo "<td>" . $row['created_at'] . "</td>";
                                            echo "<td>";
                                            echo "<a href='view_bc_details.php?bc_id=" . $row['id'] . "' class='btn btn-info btn-sm me-1' title='View Details'><i class='fadeIn animated bx bx-show'></i></a>";
                                            echo "<a href='payment.php?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this payment?')\"><i class='fadeIn animated bx bx-trash'></i></a>";
                                            echo "</td>";
                                            echo "</tr>";
                                            
                                            $sr++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No payment records found</td></tr>";
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