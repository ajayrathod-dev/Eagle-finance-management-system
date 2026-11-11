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
if ($result4 && mysqli_num_rows($result4) > 0) {
    while ($row4 = mysqli_fetch_assoc($result4)) {
        $u_uname = mysqli_real_escape_string($link, $row4['user_id']);
        $br_id = mysqli_real_escape_string($link, $row4['user_id']);
    }
} else {
    //header("location:logout.php");
    //exit;
}

// Create table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS `mc_fund_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `given_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `mc_fund_members_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mc_fund_members_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `payment_members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

mysqli_query($link, $create_table_sql);

// Process form submission
if (isset($_POST['submit_mc_fund_member'])) {
    $payment_id = intval($_POST['payment_id']);
    $member_id = intval($_POST['member_id']);
    $amount = floatval($_POST['amount']);
    $given_date = mysqli_real_escape_string($link, $_POST['given_date']);
    $notes = mysqli_real_escape_string($link, $_POST['notes']);
    $created_at = date("Y-m-d H:i:s");
    
    // Debug: Check if values are received
    error_log("Payment ID: $payment_id, Member ID: $member_id, Amount: $amount, Date: $given_date");
    
    $sql = "INSERT INTO mc_fund_members (payment_id, member_id, amount, given_date, notes, created_at) 
            VALUES ($payment_id, $member_id, $amount, '$given_date', '$notes', '$created_at')";
    
    if (mysqli_query($link, $sql)) {
        // Redirect back to the same file with success message
        header("Location: gives_bc_funds.php?success=MC Funds Given to Member Successfully");
        exit;
    } else {
        $error = "Failed to add MC funds record: " . mysqli_error($link);
        error_log("SQL Error: " . mysqli_error($link));
    }
}

// Delete record
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM mc_fund_members WHERE id = $delete_id";
    if (mysqli_query($link, $del_sql)) {
        header("Location: gives_bc_funds.php?success=MC Funds Record Deleted");
        exit;
    } else {
        $error = "Delete Failed: " . mysqli_error($link);
    }
}

// Fetch data for JS: payments and members
$payments_data = [];
$payment_result = mysqli_query($link, "SELECT id, bc_title, bc_start_date, amount FROM payments ORDER BY bc_start_date DESC");
while ($p_row = mysqli_fetch_assoc($payment_result)) {
    $payments_data[$p_row['id']] = [
        'title' => $p_row['bc_title'],
        'date' => $p_row['bc_start_date'],
        'amount' => $p_row['amount']
    ];
}

$members_data = [];
$member_result = mysqli_query($link, "SELECT pm.id, pm.name, pm.number, pm.payment_id FROM payment_members pm ORDER BY pm.name ASC");
while ($m_row = mysqli_fetch_assoc($member_result)) {
    $members_data[$m_row['payment_id']][] = [
        'id' => $m_row['id'],
        'name' => $m_row['name'],
        'number' => $m_row['number']
    ];
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
        <?php 
        if (isset($_GET['success'])) { 
            echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_GET['success']) . '</div>';
        }
        if (isset($error)) { 
            echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
        }
        ?>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i></div>
                            <h5 class="mb-0 text-primary">MC Fund Member</h5>
                        </div>
                    
                        <form class="row g-3" method="post" action="gives_bc_funds.php" id="mcFundForm">
                            <div class="col-md-6">
                                <label for="payment_id" class="form-label">Select BC</label>
                                <select class="form-select" id="payment_id" name="payment_id" required onchange="updateMembers()">
                                    <option value="">-- Select BC --</option>
                                    <?php foreach ($payments_data as $id => $data): ?>
                                        <option value="<?php echo $id; ?>">
                                            <?php echo htmlspecialchars($data['title']) . " - Date: " . $data['date'] . " - Amount: ₹" . number_format($data['amount'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select class="form-select" id="member_id" name="member_id" required disabled>
                                    <option value="">-- Select Member After BC --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="col-md-4">
                                <label for="given_date" class="form-label">Given Date</label>
                                <input type="date" class="form-control" id="given_date" name="given_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="notes" class="form-label">Notes</label>
                                <input type="text" class="form-control" id="notes" name="notes">
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" name="submit_mc_fund_member" class="btn btn-primary px-5">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- MC Fund Members Records Table -->
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
                                        <th>Given Date</th>
                                        <th>Notes</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr=1;
                                    $sql = "SELECT mfm.*, p.bc_title as bc_title, p.bc_start_date as bc_date, 
                                                   pm.name as member_name, pm.number as member_number
                                            FROM mc_fund_members mfm 
                                            JOIN payments p ON mfm.payment_id = p.id 
                                            JOIN payment_members pm ON mfm.member_id = pm.id 
                                            ORDER BY mfm.id DESC";
                                    $result = mysqli_query($link, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $sr. "</td>";
                                            echo "<td>" . htmlspecialchars($row['bc_title'] . " - " . $row['bc_date']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['member_name'] . " (" . $row['member_number'] . ")") . "</td>";
                                            echo "<td>₹" . number_format($row['amount'], 2) . "</td>";
                                            echo "<td>" . $row['given_date'] . "</td>";
                                            echo "<td>" . ($row['notes'] ? htmlspecialchars($row['notes']) : 'N/A') . "</td>";
                                            echo "<td>" . $row['created_at'] . "</td>";
                                            echo "<td>";
                                            echo "<a href='gives_bc_funds.php?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?')\"><i class='fadeIn animated bx bx-trash'></i> Delete</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                            $sr++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No MC funds records found</td></tr>";
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
    // JS Data for members grouped by payment_id
    const membersData = <?php echo json_encode($members_data); ?>;
    
    function updateMembers() {
        const paymentId = document.getElementById('payment_id').value;
        const memberSelect = document.getElementById('member_id');
        const amountInput = document.getElementById('amount');
        
        // Clear and disable member select
        memberSelect.innerHTML = '<option value="">-- Select Member After BC --</option>';
        memberSelect.disabled = true;
        amountInput.disabled = true;
        
        if (paymentId && membersData[paymentId]) {
            // Populate members
            membersData[paymentId].forEach(member => {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name + ' - ' + member.number;
                memberSelect.appendChild(option);
            });
            memberSelect.disabled = false;
            amountInput.disabled = false;
        }
    }
    
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>