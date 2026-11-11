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

include('header.php');

// Get loan ID from URL
if (isset($_GET['loan_id'])) {
    $loan_id = intval($_GET['loan_id']);
    $sql = "SELECT * FROM intrest WHERE id = '$loan_id'";
    $result = mysqli_query($link, $sql);
    
    if ($result->num_rows > 0) {
        $loan = mysqli_fetch_assoc($result);
    } else {
        header("Location: intrest.php?error=Loan not found");
        exit;
    }
} else {
    header("Location: intrest.php");
    exit;
}
?>

<style>
    .loan-details-card {
        border-left: 4px solid #007bff;
    }
    .detail-section {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .detail-label {
        font-weight: 600;
        color: #495057;
    }
    .photo-container {
        text-align: center;
        padding: 10px;
    }
    .customer-photo {
        max-width: 150px;
        max-height: 150px;
        border: 2px solid #dee2e6;
        border-radius: 5px;
    }
    .info-row {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f1f1;
    }
    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }
</style>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="col-lg-12">
                    <div class="alert alert-dark border-0 bg-dark alert-dismissible fade show">
                        <div class="text-white d-flex justify-content-between align-items-center">
                            <h5 class="text-white mb-0">Loan Details - <?php echo htmlspecialchars($loan['account_no']); ?></h5>
                            <a href="intrest.php" class="btn btn-light btn-sm">Back to Loans</a>
                        </div>
                    </div>
                </div>

                <div class="card loan-details-card">
                    <div class="card-body">
                        <!-- Customer Information with Photo -->
                        <div class="detail-section">
                            <h6 class="section-title">Customer Information</h6>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Account No:</span><br>
                                            <?php echo htmlspecialchars($loan['account_no']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Finance Name:</span><br>
                                            <?php echo htmlspecialchars($loan['finance_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Customer Name:</span><br>
                                            <?php echo htmlspecialchars($loan['c_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Father's Name:</span><br>
                                            <?php echo htmlspecialchars($loan['c_father_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">PAN No:</span><br>
                                            <?php echo htmlspecialchars($loan['c_pan_no']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Dep Chop No:</span><br>
                                            <?php echo htmlspecialchars($loan['c_dep_chop_no']); ?>
                                        </div>
                                        <div class="col-md-6 info-row">
                                            <span class="detail-label">Address:</span><br>
                                            <?php echo nl2br(htmlspecialchars($loan['address'])); ?>
                                        </div>
                                        <div class="col-md-3 info-row">
                                            <span class="detail-label">Phone No:</span><br>
                                            <?php echo htmlspecialchars($loan['phone_no']); ?>
                                        </div>
                                        <div class="col-md-3 info-row">
                                            <span class="detail-label">Mobile:</span><br>
                                            <?php echo htmlspecialchars($loan['c_mobile']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php if (!empty($loan['customer_photo_path'])): ?>
                                    <div class="photo-container">
                                        <img src="<?php echo htmlspecialchars($loan['customer_photo_path']); ?>" 
                                             alt="Customer Photo" class="customer-photo">
                                        <p class="mt-2"><strong>Customer Photo</strong></p>
                                    </div>
                                    <?php else: ?>
                                    <div class="photo-container">
                                        <div style="width:150px;height:150px;border:2px dashed #ddd;display:flex;align-items:center;justify-content:center;">
                                            <span class="text-muted">No Photo</span>
                                        </div>
                                        <p class="mt-2"><strong>Customer Photo</strong></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Details -->
                        <div class="detail-section">
                            <h6 class="section-title">Loan Details</h6>
                            <div class="row">
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Loan Amount:</span><br>
                                    ₹<?php echo number_format($loan['loan_amount'], 2); ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Interest Rate:</span><br>
                                    <?php echo $loan['interest_rate']; ?>%
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Total Amount:</span><br>
                                    ₹<?php echo number_format($loan['total_loan_amount'], 2); ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Remaining Amount:</span><br>
                                    ₹<?php echo number_format($loan['remaining_amount'], 2); ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Loan Term:</span><br>
                                    <?php echo $loan['loan_term']; ?> months
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Monthly EMI:</span><br>
                                    ₹<?php echo number_format($loan['monthly_emi'], 2); ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Daily Interest:</span><br>
                                    ₹<?php echo number_format($loan['daily_interest_amount'], 2); ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Loan Date:</span><br>
                                    <?php echo $loan['loan_date']; ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">First EMI Date:</span><br>
                                    <?php echo $loan['emi_date']; ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Paid By:</span><br>
                                    <?php echo htmlspecialchars($loan['paid_by']); ?>
                                </div>
                                <?php if ($loan['paid_by'] == 'Cheque'): ?>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Cheque No:</span><br>
                                    <?php echo htmlspecialchars($loan['cheque_no']); ?>
                                </div>
                                <div class="col-md-3 info-row">
                                    <span class="detail-label">Bank Name:</span><br>
                                    <?php echo htmlspecialchars($loan['bank_name']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- First Guarantee Details -->
                        <div class="detail-section">
                            <h6 class="section-title">First Guarantee Details</h6>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Name:</span><br>
                                            <?php echo htmlspecialchars($loan['surety1_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Father's Name:</span><br>
                                            <?php echo htmlspecialchars($loan['surety1_father_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Phone:</span><br>
                                            <?php echo htmlspecialchars($loan['surety1_phone']); ?>
                                        </div>
                                        <div class="col-md-6 info-row">
                                            <span class="detail-label">Address:</span><br>
                                            <?php echo nl2br(htmlspecialchars($loan['surety1_address'])); ?>
                                        </div>
                                        <div class="col-md-3 info-row">
                                            <span class="detail-label">PAN No:</span><br>
                                            <?php echo htmlspecialchars($loan['surety1_pan']); ?>
                                        </div>
                                        <div class="col-md-3 info-row">
                                            <span class="detail-label">Dep Chop No:</span><br>
                                            <?php echo htmlspecialchars($loan['surety1_dep_chop_no']); ?>
                                        </div>
                                        <div class="col-md-6 info-row">
                                            <span class="detail-label">Bank Name:</span><br>
                                            <?php echo htmlspecialchars($loan['surety1_bank_name']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php if (!empty($loan['surety1_photo_path'])): ?>
                                    <div class="photo-container">
                                        <img src="<?php echo htmlspecialchars($loan['surety1_photo_path']); ?>" 
                                             alt="First Guarantee Photo" class="customer-photo">
                                        <p class="mt-2"><strong>Guarantee Photo</strong></p>
                                    </div>
                                    <?php else: ?>
                                    <div class="photo-container">
                                        <div style="width:150px;height:150px;border:2px dashed #ddd;display:flex;align-items:center;justify-content:center;">
                                            <span class="text-muted">No Photo</span>
                                        </div>
                                        <p class="mt-2"><strong>Guarantee Photo</strong></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Second Guarantee Details -->
                        <?php if (!empty($loan['surety2_name'])): ?>
                        <div class="detail-section">
                            <h6 class="section-title">Second Guarantee Details</h6>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Name:</span><br>
                                            <?php echo htmlspecialchars($loan['surety2_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Father's Name:</span><br>
                                            <?php echo htmlspecialchars($loan['surety2_father_name']); ?>
                                        </div>
                                        <div class="col-md-4 info-row">
                                            <span class="detail-label">Phone:</span><br>
                                            <?php echo htmlspecialchars($loan['surety2_phone']); ?>
                                        </div>
                                        <div class="col-md-6 info-row">
                                            <span class="detail-label">Address:</span><br>
                                            <?php echo nl2br(htmlspecialchars($loan['surety2_address'])); ?>
                                        </div>
                                        <div class="col-md-3 info-row">
                                            <span class="detail-label">PAN No:</span><br>
                                            <?php echo htmlspecialchars($loan['surety2_pan']); ?>
                                        </div>
                                        <div class="col-md-3 info-row">
                                            <span class="detail-label">Dep Chop No:</span><br>
                                            <?php echo htmlspecialchars($loan['surety2_dep_chop_no']); ?>
                                        </div>
                                        <div class="col-md-6 info-row">
                                            <span class="detail-label">Bank Name:</span><br>
                                            <?php echo htmlspecialchars($loan['surety2_bank_name']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php if (!empty($loan['surety2_photo_path'])): ?>
                                    <div class="photo-container">
                                        <img src="<?php echo htmlspecialchars($loan['surety2_photo_path']); ?>" 
                                             alt="Second Guarantee Photo" class="customer-photo">
                                        <p class="mt-2"><strong>Guarantee Photo</strong></p>
                                    </div>
                                    <?php else: ?>
                                    <div class="photo-container">
                                        <div style="width:150px;height:150px;border:2px dashed #ddd;display:flex;align-items:center;justify-content:center;">
                                            <span class="text-muted">No Photo</span>
                                        </div>
                                        <p class="mt-2"><strong>Guarantee Photo</strong></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <a href="loan_payment.php?loan_id=<?php echo $loan['id']; ?>" class="btn btn-success">Pay EMI</a>
                                <a href="intrest.php" class="btn btn-secondary">Back to List</a>
                                <button onclick="window.print()" class="btn btn-primary">Print Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>