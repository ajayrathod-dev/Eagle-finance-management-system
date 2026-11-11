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

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // First, check if the loan exists
    $check_sql = "SELECT * FROM intrest WHERE id = '$delete_id'";
    $check_result = mysqli_query($link, $check_sql);
    
    if ($check_result->num_rows > 0) {
        $del_sql = "DELETE FROM intrest WHERE id = '$delete_id'";
        if (mysqli_query($link, $del_sql)) {
            $success_msg = "Loan deleted successfully";
        } else {
            $error_msg = "Error deleting loan: " . mysqli_error($link);
        }
    } else {
        $error_msg = "Loan not found";
    }
    
    // Redirect to avoid resubmission on refresh
    header("Location: all_loans.php?success=" . urlencode($success_msg ?? '') . "&error=" . urlencode($error_msg ?? ''));
    exit;
}

// Handle search functionality
$search = '';
$where_condition = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($link, $_GET['search']);
    $where_condition = " WHERE account_no LIKE '%$search%' OR c_name LIKE '%$search%' OR c_mobile LIKE '%$search%' OR finance_name LIKE '%$search%'";
}

// Get all loans with search condition
$sql = "SELECT * FROM intrest $where_condition ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);
$total_loans = $result->num_rows;
?>

<style>
    .loans-table {
        font-size: 0.9rem;
    }
    .loans-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    .status-completed {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    .status-overdue {
        background-color: #f8d7da;
        color: #721c24;
    }
    .action-buttons .btn {
        margin: 2px;
        font-size: 0.8rem;
    }
    .search-box {
        max-width: 400px;
    }
    .summary-card {
        border-left: 4px solid #007bff;
    }
    .table-responsive {
        border-radius: 5px;
    }
</style>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <!-- Page Header -->
                <div class="col-lg-12">
                    <div class="alert alert-dark border-0 bg-dark alert-dismissible fade show">
                        <div class="text-white d-flex justify-content-between align-items-center">
                            <h5 class="text-white mb-0">All Loans Management</h5>
                            <a href="intrest.php" class="btn btn-light btn-sm">Add New Loan</a>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_GET['success']) && !empty($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card summary-card">
                            <div class="card-body">
                                <h6 class="card-title">Total Loans</h6>
                                <h3 class="text-primary"><?php echo $total_loans; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card summary-card">
                            <div class="card-body">
                                <h6 class="card-title">Active Loans</h6>
                                <h3 class="text-success">
                                    <?php 
                                    $active_sql = "SELECT COUNT(*) as active_count FROM intrest WHERE remaining_amount > 0";
                                    $active_result = mysqli_query($link, $active_sql);
                                    $active_count = mysqli_fetch_assoc($active_result)['active_count'];
                                    echo $active_count;
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card summary-card">
                            <div class="card-body">
                                <h6 class="card-title">Completed Loans</h6>
                                <h3 class="text-info">
                                    <?php 
                                    $completed_sql = "SELECT COUNT(*) as completed_count FROM intrest WHERE remaining_amount <= 0";
                                    $completed_result = mysqli_query($link, $completed_sql);
                                    $completed_count = mysqli_fetch_assoc($completed_result)['completed_count'];
                                    echo $completed_count;
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card summary-card">
                            <div class="card-body">
                                <h6 class="card-title">Total Loan Amount</h6>
                                <h3 class="text-warning">
                                    ₹<?php 
                                    $total_amount_sql = "SELECT SUM(loan_amount) as total_amount FROM intrest";
                                    $total_amount_result = mysqli_query($link, $total_amount_sql);
                                    $total_amount = mysqli_fetch_assoc($total_amount_result)['total_amount'];
                                    echo number_format($total_amount ?: 0, 2);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="all_loans.php" class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="search" placeholder="Search by Account No, Customer Name, Mobile, or Finance Name..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                            <div class="col-md-2">
                                <a href="all_loans.php" class="btn btn-secondary w-100">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Loans Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered loans-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Account No</th>
                                        <th>Customer Name</th>
                                        <th>Finance Name</th>
                                        <th>Mobile</th>
                                        <th>Loan Amount</th>
                                        <th>Interest Rate</th>
                                        <th>Total Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Loan Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total_loans > 0): ?>
                                        <?php 
                                        $sr = 1;
                                        while ($row = mysqli_fetch_assoc($result)): 
                                            // Determine status
                                            if ($row['remaining_amount'] <= 0) {
                                                $status_class = 'status-completed';
                                                $status_text = 'Completed';
                                            } else {
                                                $emi_date = new DateTime($row['emi_date']);
                                                $today = new DateTime();
                                                if ($emi_date < $today) {
                                                    $status_class = 'status-overdue';
                                                    $status_text = 'Overdue';
                                                } else {
                                                    $status_class = 'status-active';
                                                    $status_text = 'Active';
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <td><?php echo $sr; ?></td>
                                                <td><strong><?php echo htmlspecialchars($row['account_no']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($row['c_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['finance_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['c_mobile']); ?></td>
                                                <td>₹<?php echo number_format($row['loan_amount'], 2); ?></td>
                                                <td><?php echo $row['interest_rate']; ?>%</td>
                                                <td>₹<?php echo number_format($row['total_loan_amount'], 2); ?></td>
                                                <td>
                                                    <strong class="<?php echo $row['remaining_amount'] <= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        ₹<?php echo number_format($row['remaining_amount'], 2); ?>
                                                    </strong>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($row['loan_date'])); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="view_loan.php?loan_id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-info btn-sm" 
                                                       title="View Details">
                                                       <i class="fadeIn animated bx bx-show"></i> View
                                                    </a>
                                                    <a href="loan_payment.php?loan_id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-success btn-sm" 
                                                       title="Pay EMI">
                                                       <i class="fadeIn animated bx bx-credit-card"></i> Pay
                                                    </a>
                                                    <a href="all_loans.php?delete_id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       title="Delete Loan"
                                                       onclick="return confirm('Are you sure you want to delete this loan? This action cannot be undone.')">
                                                       <i class="fadeIn animated bx bx-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php 
                                        $sr++;
                                        endwhile; 
                                        ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
                                                <?php if (!empty($search)): ?>
                                                    <div class="text-muted">
                                                        <i class="bx bx-search-alt" style="font-size: 48px;"></i>
                                                        <h5>No loans found</h5>
                                                        <p>No loans match your search criteria "<?php echo htmlspecialchars($search); ?>"</p>
                                                        <a href="all_loans.php" class="btn btn-primary">View All Loans</a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-muted">
                                                        <i class="bx bx-file" style="font-size: 48px;"></i>
                                                        <h5>No loans found</h5>
                                                        <p>No loans have been added yet.</p>
                                                        <a href="intrest.php" class="btn btn-primary">Add New Loan</a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Export Button -->
                        <?php if ($total_loans > 0): ?>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing <?php echo $total_loans; ?> loan<?php echo $total_loans > 1 ? 's' : ''; ?>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fadeIn animated bx bx-printer"></i> Print Report
                                </button>
                                <a href="export_loans.php" class="btn btn-outline-success btn-sm">
                                    <i class="fadeIn animated bx bx-download"></i> Export Excel
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add confirmation for delete actions
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a[href*="delete_id"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this loan? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

<?php include('footer.php'); ?>