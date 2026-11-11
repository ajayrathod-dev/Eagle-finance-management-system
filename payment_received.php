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


if (isset($_POST['submit_payment_received'])) {
    $payment_id = !empty($_POST['payment_id']) ? intval($_POST['payment_id']) : null;
    $payment_member_id = !empty($_POST['payment_member_id']) ? intval($_POST['payment_member_id']) : null;
    $amount = mysqli_real_escape_string($link, $_POST['amount']);
    $received_date = date("Y-m-d H:i:s");

    // Query तयार करा
    $sql = "INSERT INTO payment_received (payment_id, payment_member_id, amount, received_date) 
            VALUES ('$payment_id', '$payment_member_id', '$amount', '$received_date')";

    // Query execute करा
    if (mysqli_query($link, $sql)) {
        header("Location: payment_received.php?success=Payment Received Record Added Successfully");
        exit;
    } else {
        $error = mysqli_error($link);
        header("Location: payment_received.php?error=Failed to add record: $error");
        exit;
    }
}



// Delete payment received record
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM payment_received WHERE id='$delete_id'";
    if (mysqli_query($link, $del_sql)) {
        header("Location: payment_received.php?success=Payment Received Record Deleted");
    } else {
        header("Location: payment_received.php?error=Delete Failed: " . mysqli_error($link));
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
                            <h5 class="mb-0 text-primary">Record Payment Received</h5>
                        </div>
                        <form class="row g-3" method="post" action="">
                            <div class="col-md-6">
                                <label for="payment_id" class="form-label">Select BC</label>
                                <select class="form-select" id="payment_id" name="payment_id" required>
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
                            <div class="col-md-6">
                                <label for="payment_member_id" class="form-label">Select Member</label>
                                <select class="form-select" id="payment_member_id" name="payment_member_id" required disabled>
                                    <option value="">-- Select BC first --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" name="submit_payment_received" class="btn btn-primary px-5">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Payment Received Records Table -->
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>BC</th>
                                        <th>Member</th>
                                        <th>Amount</th>
                                        <th>Received Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr=1;
                                    $sql = "SELECT pr.*, p.bc_title, pm.name, pm.number 
                                           FROM payment_received pr 
                                           LEFT JOIN payments p ON pr.payment_id = p.id 
                                           LEFT JOIN payment_members pm ON pr.payment_member_id = pm.id 
                                           ORDER BY pr.id DESC";
                                    $result = mysqli_query($link, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $sr. "</td>";
                                            echo "<td>" . ($row['payment_id'] ? htmlspecialchars($row['bc_title']) : "N/A") . "</td>";
                                            echo "<td>" . ($row['payment_member_id'] ? htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['number']) : "N/A") . "</td>";
                                            echo "<td>" . number_format($row['amount'], 2) . "</td>";
                                            echo "<td>" . $row['received_date'] . "</td>";
                                            echo "<td>";
                                            echo "<a href='payment_received.php?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this payment received record?')\"><i class='fadeIn animated bx bx-trash'></i></a>";
                                            echo "</td>";
                                            echo "</tr>";
                                            $sr++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No payment received records found</td></tr>";
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
          // Handle BC dropdown change to filter members
        $('#payment_id').change(function() {
            var payment_id = $(this).val();
            var memberSelect = $('#payment_member_id');

            // Disable member dropdown and show loading state
            memberSelect.prop('disabled', true).html('<option value="">Loading...</option>');

            if (payment_id) {
                // Fetch members associated with the selected BC
                $.ajax({
                    url: 'get_members_by_bc.php',
                    type: 'GET',
                    data: { payment_id: payment_id },
                    dataType: 'json',
                    success: function(data) {
                        // Clear existing options
                        memberSelect.html('<option value="">-- Select Member --</option>');

                        if (data.length > 0) {
                            $.each(data, function(index, member) {
                                memberSelect.append(
                                    $('<option></option>').val(member.id).text(member.name + ' - ' + member.number)
                                );
                            });
                            memberSelect.prop('disabled', false); // Enable dropdown
                        } else {
                            memberSelect.append(
                                $('<option></option>').val('').text('No members found for this BC')
                            );
                            memberSelect.prop('disabled', true); // Keep disabled if no members
                        }
                    },
                    error: function(xhr, status, error) {
                        memberSelect.html('<option value="">Error loading members</option>');
                        alert('Failed to load members: ' + error + '. Please try again.');
                    }
                });
            } else {
                // If no BC selected, reset to initial state
                memberSelect.html('<option value="">-- Select BC first --</option>');
                memberSelect.prop('disabled', true);
            }
        });
    });
</script>