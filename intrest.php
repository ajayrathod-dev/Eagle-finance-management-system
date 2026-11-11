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

include('header.php');

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM intrest WHERE id='$delete_id'";
    if (mysqli_query($link, $del_sql)) {
        header("Location: intrest.php?success=Loan Deleted");
    } else {
        header("Location: intrest.php?error=Delete Failed");
    }
}
?>

<style>
    .calculation-results {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-top: 20px;
        border-left: 4px solid #007bff;
    }
    
    .section-header {
        background-color: #e9ecef;
        color: #495057;
        padding: 10px 15px;
        border-radius: 5px;
        margin: 20px 0 15px 0;
        border-left: 4px solid #6c757d;
        font-weight: 600;
    }
    
    .photo-preview {
        width: 120px;
        height: 120px;
        border: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background-size: cover;
        background-position: center;
        margin-top: 8px;
    }
    
    .photo-label {
        font-size: 0.875rem;
        color: #ec770bff;
        margin-bottom: 5px;
    }
    
    .guarantee-section {
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #fdfdfd;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 5px;
    }
</style>

<script>
function calculateLoan() {
    // Get input values
    var loanAmount = parseFloat(document.getElementById('loan_amount').value) || 0;
    var interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;
    var emiTerms = parseInt(document.getElementById('loan_term').value) || 0;
    
    // Calculate total loan amount (principal + interest)
    var interestAmount = (loanAmount * interestRate) / 100;
    var totalLoanAmount = loanAmount + interestAmount;
    
    // Calculate monthly EMI
    var monthlyEMI = emiTerms > 0 ? totalLoanAmount / emiTerms : 0;
    
    // Update the form fields
    document.getElementById('total_loan_amount').value = totalLoanAmount.toFixed(2);
    document.getElementById('monthly_emi').value = monthlyEMI.toFixed(2);
    document.getElementById('daily_interest_amount').value = (interestAmount / (emiTerms * 30)).toFixed(2);
    
    // Display calculation summary
    document.getElementById('calculation_summary').innerHTML = `
        <h6>Calculation Summary:</h6>
        <p><strong>Loan Amount:</strong> ₹${loanAmount.toFixed(2)}</p>
        <p><strong>Interest Amount:</strong> ₹${interestAmount.toFixed(2)}</p>
        <p><strong>Total Loan Amount:</strong> ₹${totalLoanAmount.toFixed(2)}</p>
        <p><strong>Monthly EMI:</strong> ₹${monthlyEMI.toFixed(2)} for ${emiTerms} months</p>
    `;
}

function previewPhoto(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).style.backgroundImage = 'url(' + e.target.result + ')';
            document.getElementById(previewId).innerHTML = '';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleChequeFields() {
    var paidBy = document.getElementById('paid_by').value;
    var chequeFields = document.getElementById('cheque_fields');
    
    if (paidBy === 'Cheque') {
        chequeFields.style.display = 'block';
    } else {
        chequeFields.style.display = 'none';
        document.getElementById('cheque_no').value = '';
        document.getElementById('bank_name').value = '';
    }
}

function toggleGuaranteeFields() {
    var guaranteeCount = document.getElementById('guarantee_count').value;
    var secondGuaranteeSection = document.getElementById('second_guarantee_section');
    
    if (guaranteeCount === '2') {
        secondGuaranteeSection.style.display = 'block';
        // Make second guarantee fields required
        document.querySelectorAll('#second_guarantee_section [required]').forEach(function(element) {
            element.required = true;
        });
    } else {
        secondGuaranteeSection.style.display = 'none';
        // Remove required from second guarantee fields
        document.querySelectorAll('#second_guarantee_section [required]').forEach(function(element) {
            element.required = false;
        });
        // Clear second guarantee fields
        document.querySelectorAll('#second_guarantee_section input, #second_guarantee_section textarea').forEach(function(element) {
            if (element.type !== 'button' && element.type !== 'submit') {
                element.value = '';
            }
        });
    }
}
</script>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="col-lg-12">
                    <div class="alert alert-dark border-0 bg-dark alert-dismissible fade show" style="height:60px;">
                        <div class="text-white"><h5 class="text-white">Add New Loan Entry</h5></div>
                    </div>
                </div>
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <form class="row g-3" action="intrest1.php" method="post" enctype="multipart/form-data">
                            <!-- Basic Loan Information -->
                            <div class="section-header">
                                <h6 class="mb-0">Basic Loan Information</h6>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="account_no" class="form-label">Account No.</label>
                                <input type="text" class="form-control" id="account_no" name="account_no" required>
                            </div>
                            <div class="col-md-4">
                                <label for="finance_name" class="form-label">Finance Name</label>
                                <input type="text" class="form-control" id="finance_name" name="finance_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="c_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="c_name" name="c_name" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="c_father_name" class="form-label">Customer Father's Name</label>
                                <input type="text" class="form-control" id="c_father_name" name="c_father_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="c_pan_no" class="form-label">Customer PAN No.</label>
                                <input type="text" class="form-control" id="c_pan_no" name="c_pan_no">
                            </div>
                            <div class="col-md-4">
                                <label for="c_dep_chop_no" class="form-label">Customer Dep Chop No.</label>
                                <input type="text" class="form-control" id="c_dep_chop_no" name="c_dep_chop_no">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="phone_no" class="form-label">Phone No.</label>
                                        <input type="text" class="form-control" id="phone_no" name="phone_no" required>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label for="c_mobile" class="form-label">Customer Mobile</label>
                                        <input type="text" class="form-control" id="c_mobile" name="c_mobile" required>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="photo-label">Customer Photo</div>
                                        <div class="photo-preview" id="customer_photo_preview" onclick="document.getElementById('customer_photo').click()">
                                            <span style="font-size: 0.8rem;">Click to upload</span>
                                        </div>
                                        <input type="file" class="form-control d-none" id="customer_photo" name="customer_photo" accept="image/*" onchange="previewPhoto(this, 'customer_photo_preview')">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loan Calculation Section -->
                            <div class="section-header">
                                <h6 class="mb-0">Loan Calculation</h6>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="loan_amount" class="form-label">Loan Amount (₹)</label>
                                <input type="number" class="form-control" id="loan_amount" name="loan_amount" oninput="calculateLoan()" required>
                            </div>
                            <div class="col-md-4">
                                <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                                <input type="number" step="0.01" class="form-control" id="interest_rate" name="interest_rate" oninput="calculateLoan()" required>
                            </div>
                            <div class="col-md-4">
                                <label for="loan_term" class="form-label">Loan Term (months)</label>
                                <input type="number" class="form-control" id="loan_term" name="loan_term" oninput="calculateLoan()" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="total_loan_amount" class="form-label">Total Loan Amount (₹)</label>
                                <input type="number" step="0.01" class="form-control" id="total_loan_amount" name="total_loan_amount" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="monthly_emi" class="form-label">Monthly EMI (₹)</label>
                                <input type="number" step="0.01" class="form-control" id="monthly_emi" name="monthly_emi" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="daily_interest_amount" class="form-label">Daily Interest (₹)</label>
                                <input type="number" step="0.01" class="form-control" id="daily_interest_amount" name="daily_interest_amount" readonly>
                            </div>
                            
                            <!-- Guarantee Selection -->
                            <div class="section-header">
                                <h6 class="mb-0">Guarantee Details</h6>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="guarantee_count" class="form-label">Number of Guarantees</label>
                                <select class="form-control" id="guarantee_count" name="guarantee_count" onchange="toggleGuaranteeFields()" required>
                                    <option value="1">1 Guarantee</option>
                                    <option value="2">2 Guarantees</option>
                                </select>
                            </div>
                            
                            <!-- First Guarantee -->
                            <div class="col-md-12">
                                <div class="guarantee-section">
                                    <h6>First Guarantee</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="surety1_name" class="form-label">Guarantee Name</label>
                                            <input type="text" class="form-control" id="surety1_name" name="surety1_name" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="surety1_father_name" class="form-label">Father's Name</label>
                                            <input type="text" class="form-control" id="surety1_father_name" name="surety1_father_name" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="surety1_phone" class="form-label">Phone No.</label>
                                            <input type="text" class="form-control" id="surety1_phone" name="surety1_phone" required>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="photo-label">Guarantee Photo</div>
                                            <div class="photo-preview" id="surety1_photo_preview" onclick="document.getElementById('surety1_photo').click()">
                                                <span style="font-size: 0.8rem;">Click to upload</span>
                                            </div>
                                            <input type="file" class="form-control d-none" id="surety1_photo" name="surety1_photo" accept="image/*" onchange="previewPhoto(this, 'surety1_photo_preview')">
                                        </div>
                                        
                                        <div class="col-md-6 mt-2">
                                            <label for="surety1_address" class="form-label">Address</label>
                                            <textarea class="form-control" id="surety1_address" name="surety1_address" rows="2" required></textarea>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="surety1_pan" class="form-label">PAN No.</label>
                                            <input type="text" class="form-control" id="surety1_pan" name="surety1_pan">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="surety1_dep_chop_no" class="form-label">Dep Chop No.</label>
                                            <input type="text" class="form-control" id="surety1_dep_chop_no" name="surety1_dep_chop_no">
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="surety1_bank_name" class="form-label">Bank's Name</label>
                                            <input type="text" class="form-control" id="surety1_bank_name" name="surety1_bank_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Second Guarantee (Hidden by default) -->
                            <div class="col-md-12" id="second_guarantee_section" style="display: none;">
                                <div class="guarantee-section">
                                    <h6>Second Guarantee</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="surety2_name" class="form-label">Guarantee Name</label>
                                            <input type="text" class="form-control" id="surety2_name" name="surety2_name">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="surety2_father_name" class="form-label">Father's Name</label>
                                            <input type="text" class="form-control" id="surety2_father_name" name="surety2_father_name">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="surety2_phone" class="form-label">Phone No.</label>
                                            <input type="text" class="form-control" id="surety2_phone" name="surety2_phone">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="photo-label">Guarantee Photo</div>
                                            <div class="photo-preview" id="surety2_photo_preview" onclick="document.getElementById('surety2_photo').click()">
                                                <span style="font-size: 0.8rem;">Click to upload</span>
                                            </div>
                                            <input type="file" class="form-control d-none" id="surety2_photo" name="surety2_photo" accept="image/*" onchange="previewPhoto(this, 'surety2_photo_preview')">
                                        </div>
                                        
                                        <div class="col-md-6 mt-2">
                                            <label for="surety2_address" class="form-label">Address</label>
                                            <textarea class="form-control" id="surety2_address" name="surety2_address" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="surety2_pan" class="form-label">PAN No.</label>
                                            <input type="text" class="form-control" id="surety2_pan" name="surety2_pan">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label for="surety2_dep_chop_no" class="form-label">Dep Chop No.</label>
                                            <input type="text" class="form-control" id="surety2_dep_chop_no" name="surety2_dep_chop_no">
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="surety2_bank_name" class="form-label">Bank's Name</label>
                                            <input type="text" class="form-control" id="surety2_bank_name" name="surety2_bank_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Details -->
                            <div class="section-header">
                                <h6 class="mb-0">Payment Details</h6>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="loan_date" class="form-label">Loan Date</label>
                                <input type="date" class="form-control" id="loan_date" name="loan_date" required>
                            </div>
                            <div class="col-md-4">
                                <label for="emi_date" class="form-label">First EMI Date</label>
                                <input type="date" class="form-control" id="emi_date" name="emi_date" required>
                            </div>
                            <div class="col-md-4">
                                <label for="paid_by" class="form-label">Paid By</label>
                                <select class="form-control" id="paid_by" name="paid_by" onchange="toggleChequeFields()">
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6" id="cheque_fields" style="display: none;">
                                <label for="cheque_no" class="form-label">Cheque No.</label>
                                <input type="text" class="form-control" id="cheque_no" name="cheque_no">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name">
                            </div>
                            
                            <div class="col-12 calculation-results" id="calculation_summary"></div>
                            <div class="col-12" align="center">
                                <button type="submit" class="btn btn-outline-primary px-5">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary px-5">Clear All</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Account No</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Loan Amount</th>
                                <th>Interest Rate</th>
                                <th>Total Amount</th>
                                <th>Loan Date</th>
                                <th>EMI Date</th>
                                <th>Monthly EMI</th>
                                <th>Remaining Amount</th>
                                <th>By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sr = 1;
                            $sql1 = "SELECT * FROM intrest ORDER BY id DESC";
                            $result1 = mysqli_query($link, $sql1);
                            if ($result1->num_rows > 0) {
                                while ($row = mysqli_fetch_array($result1)) {
                            ?>
                                    <tr>
                                        <td><?php echo $sr; ?></td>
                                        <td><?php echo htmlspecialchars($row['account_no']); ?></td>
                                        <td><?php echo htmlspecialchars($row['c_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['c_mobile']); ?></td>
                                        <td>₹<?php echo number_format($row['loan_amount'], 2); ?></td>
                                        <td><?php echo $row['interest_rate']; ?>%</td>
                                        <td>₹<?php echo number_format($row['total_loan_amount'], 2); ?></td>
                                        <td><?php echo $row['loan_date']; ?></td>
                                        <td><?php echo $row['emi_date']; ?></td>
                                        <td>₹<?php echo number_format($row['monthly_emi'], 2); ?></td>
                                        <td>₹<?php echo number_format($row['remaining_amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['by']); ?></td>
                                        <td>
                                            <a href="intrest.php?delete_id=<?php echo $row['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this loan?');">
                                               Delete
                                            </a>
                                            <a href="loan_payment.php?loan_id=<?php echo $row['id']; ?>"
                                               class="btn btn-success btn-sm">
                                               Pay EMI
                                            </a>
                                            <a href="view_loan.php?loan_id=<?php echo $row['id']; ?>"
                                               class="btn btn-info btn-sm">
                                               View Details
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                    $sr++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set default dates
document.getElementById('loan_date').valueAsDate = new Date();
var nextMonth = new Date();
nextMonth.setMonth(nextMonth.getMonth() + 1);
document.getElementById('emi_date').valueAsDate = nextMonth;
</script>

<?php include('footer.php'); ?>