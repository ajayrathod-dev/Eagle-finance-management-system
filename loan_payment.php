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
    header("Location: logout.php");
    exit;
}

// Process payment before any HTML output
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_amount'])) {
    $payment_amount = floatval($_POST['payment_amount']);
    $payment_date = mysqli_real_escape_string($link, $_POST['payment_date']);
    $loan_id = intval($_POST['loan_id']);
    
    // Get current loan details
    $sql = "SELECT * FROM intrest WHERE id = $loan_id";
    $result = mysqli_query($link, $sql);
    if ($result->num_rows > 0) {
        $loan = mysqli_fetch_assoc($result);
        $remaining_amount = floatval($loan['remaining_amount']) - $payment_amount;
        
        // Update loan remaining amount
        $update_sql = "UPDATE intrest SET remaining_amount = $remaining_amount WHERE id = $loan_id";
        mysqli_query($link, $update_sql);
        
        // Record payment
        $insert_sql = "INSERT INTO loan_payments (loan_id, payment_amount, payment_date, received_by) 
                       VALUES ($loan_id, $payment_amount, '$payment_date', '$u_uname')";
        mysqli_query($link, $insert_sql);
        
        // Redirect to intrest.php with success message
        header("Location: intrest.php?success=Payment processed successfully");
        exit;
    }
}

include('header.php');

// Get loan details if loan_id is provided
$loan_id = isset($_GET['loan_id']) ? intval($_GET['loan_id']) : 0;
$loan_details = null;

if ($loan_id > 0) {
    $sql = "SELECT * FROM intrest WHERE id = $loan_id";
    $result = mysqli_query($link, $sql);
    if ($result->num_rows > 0) {
        $loan_details = mysqli_fetch_assoc($result);
    }
}

// Get payment history for this loan
$payment_history = array();
if ($loan_id > 0) {
    $history_sql = "SELECT * FROM loan_payments WHERE loan_id = $loan_id ORDER BY payment_date DESC, created_at DESC";
    $history_result = mysqli_query($link, $history_sql);
    if ($history_result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($history_result)) {
            $payment_history[] = $row;
        }
    }
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="col-lg-12">
                    <div class="alert alert-dark border-0 bg-dark alert-dismissible fade show" style="height:60px;">
                        <div class="text-white"><h5 class="text-white">Process Loan Payment</h5></div>
                    </div>
                </div>
                
                <?php if ($loan_details): ?>
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Loan Details</h5>
                                <p><strong>Customer:</strong> <?php echo $loan_details['c_name']; ?></p>
                                <p><strong>Mobile:</strong> <?php echo $loan_details['c_mobile']; ?></p>
                                <p><strong>Total Loan Amount:</strong> ₹<?php echo number_format($loan_details['total_loan_amount'], 2); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Information</h5>
                                <p><strong>Monthly EMI:</strong> ₹<?php echo number_format($loan_details['monthly_emi'], 2); ?></p>
                                <p><strong>Remaining Amount:</strong> ₹<?php echo number_format($loan_details['remaining_amount'], 2); ?></p>
                                <p><strong>Next Due Date:</strong> <?php echo $loan_details['emi_date']; ?></p>
                            </div>
                        </div>
                        
                        <form method="post" action="loan_payment.php?loan_id=<?php echo $loan_id; ?>">
                            <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="payment_amount" class="form-label">Payment Amount (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="payment_amount" name="payment_amount" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4 align-self-end">
                                    <button type="submit" class="btn btn-success">Process Payment</button>
                                    <a href="intrest.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment History Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Payment History</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Payment Date</th>
                                        <th>Amount Paid (₹)</th>
                                        <th>Received By</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($payment_history)): ?>
                                        <?php $sr = 1; ?>
                                        <?php foreach ($payment_history as $payment): ?>
                                            <tr>
                                                <td><?php echo $sr++; ?></td>
                                                <td><?php echo $payment['payment_date']; ?></td>
                                                <td>₹<?php echo number_format($payment['payment_amount'], 2); ?></td>
                                                <td><?php echo $payment['received_by']; ?></td>
                                                <td><?php echo $payment['created_at']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No payment history found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card border-top border-0 border-4 border-danger">
                    <div class="card-body p-5">
                        <div class="alert alert-danger">No loan selected or loan not found.</div>
                        <a href="intrest.php" class="btn btn-primary">Back to Loans</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>