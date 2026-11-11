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
    .loan-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .loan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .loan-header {
        background: linear-gradient(45deg, #4e73df, #224abe);
        color: white;
        padding: 15px;
        border-radius: 10px 10px 0 0;
    }
    .loan-body {
        padding: 20px;
    }
    .loan-photo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e3e6f0;
    }
    .loan-info {
        margin-bottom: 10px;
    }
    .loan-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.85rem;
    }
    .loan-value {
        color: #2e2f37;
        font-weight: 500;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
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
    .see-more-btn {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        color: white;
        transition: all 0.3s ease;
    }
    .see-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state i {
        font-size: 80px;
        color: #dddfeb;
        margin-bottom: 20px;
    }
    .search-box {
        max-width: 500px;
        margin: 0 auto 30px;
    }
    .loan-stats {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #4e73df;
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
                            <h5 class="text-white mb-0">All Loan Customers</h5>
                            <div>
                                <a href="intrest.php" class="btn btn-light btn-sm me-2">
                                    <i class="fadeIn animated bx bx-plus"></i> Add New Loan
                                </a>
                                <a href="all_loans.php" class="btn btn-outline-light btn-sm">
                                    <i class="fadeIn animated bx bx-table"></i> Table View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="loan-stats text-center">
                            <div class="stats-number"><?php echo $total_loans; ?></div>
                            <div class="text-muted">Total Loans</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="loan-stats text-center">
                            <div class="stats-number text-success">
                                <?php 
                                $active_sql = "SELECT COUNT(*) as active_count FROM intrest WHERE remaining_amount > 0";
                                $active_result = mysqli_query($link, $active_sql);
                                echo mysqli_fetch_assoc($active_result)['active_count'];
                                ?>
                            </div>
                            <div class="text-muted">Active Loans</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="loan-stats text-center">
                            <div class="stats-number text-info">
                                <?php 
                                $completed_sql = "SELECT COUNT(*) as completed_count FROM intrest WHERE remaining_amount <= 0";
                                $completed_result = mysqli_query($link, $completed_sql);
                                echo mysqli_fetch_assoc($completed_result)['completed_count'];
                                ?>
                            </div>
                            <div class="text-muted">Completed Loans</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="loan-stats text-center">
                            <div class="stats-number text-warning">
                                ₹<?php 
                                $total_amount_sql = "SELECT SUM(loan_amount) as total_amount FROM intrest";
                                $total_amount_result = mysqli_query($link, $total_amount_sql);
                                $total_amount = mysqli_fetch_assoc($total_amount_result)['total_amount'];
                                echo number_format($total_amount ?: 0, 0);
                                ?>
                            </div>
                            <div class="text-muted">Total Amount</div>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="lonee.php" class="row g-3">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by Account No, Customer Name, Mobile, or Finance Name..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fadeIn animated bx bx-search"></i> Search
                                </button>
                            </div>
                        </form>
                        <?php if (!empty($search)): ?>
                            <div class="mt-3 text-center">
                                <span class="text-muted">Showing results for: "<?php echo htmlspecialchars($search); ?>"</span>
                                <a href="lonee.php" class="btn btn-sm btn-outline-secondary ms-2">Clear Search</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Loans Cards -->
                <div class="row">
                    <?php if ($total_loans > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
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

                            // Calculate progress percentage
                            $progress = 0;
                            if ($row['total_loan_amount'] > 0) {
                                $paid_amount = $row['total_loan_amount'] - $row['remaining_amount'];
                                $progress = min(100, max(0, ($paid_amount / $row['total_loan_amount']) * 100));
                            }
                        ?>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="loan-card">
                                    <div class="loan-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($row['account_no']); ?></h6>
                                                <small><?php echo htmlspecialchars($row['finance_name']); ?></small>
                                            </div>
                                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="loan-body">
                                        <div class="row align-items-center mb-3">
                                            <div class="col-3 text-center">
                                                <?php if (!empty($row['customer_photo_path'])): ?>
                                                    <img src="<?php echo htmlspecialchars($row['customer_photo_path']); ?>" 
                                                         alt="Customer Photo" class="loan-photo">
                                                <?php else: ?>
                                                    <div class="loan-photo bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fadeIn animated bx bx-user text-muted" style="font-size: 2rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-9">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($row['c_name']); ?></h6>
                                                <p class="text-muted mb-1 small">
                                                    <i class="fadeIn animated bx bx-phone"></i> 
                                                    <?php echo htmlspecialchars($row['c_mobile']); ?>
                                                </p>
                                                <p class="text-muted mb-0 small">
                                                    <i class="fadeIn animated bx bx-calendar"></i> 
                                                    <?php echo date('d M Y', strtotime($row['loan_date'])); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="loan-info">
                                            <div class="d-flex justify-content-between">
                                                <span class="loan-label">Loan Amount:</span>
                                                <span class="loan-value">₹<?php echo number_format($row['loan_amount'], 2); ?></span>
                                            </div>
                                        </div>

                                        <div class="loan-info">
                                            <div class="d-flex justify-content-between">
                                                <span class="loan-label">Interest Rate:</span>
                                                <span class="loan-value"><?php echo $row['interest_rate']; ?>%</span>
                                            </div>
                                        </div>

                                        <div class="loan-info">
                                            <div class="d-flex justify-content-between">
                                                <span class="loan-label">Total Amount:</span>
                                                <span class="loan-value">₹<?php echo number_format($row['total_loan_amount'], 2); ?></span>
                                            </div>
                                        </div>

                                        <div class="loan-info">
                                            <div class="d-flex justify-content-between">
                                                <span class="loan-label">Remaining Amount:</span>
                                                <span class="loan-value <?php echo $row['remaining_amount'] <= 0 ? 'text-success' : 'text-danger'; ?>">
                                                    ₹<?php echo number_format($row['remaining_amount'], 2); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="loan-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="loan-label">Monthly EMI:</span>
                                                <span class="loan-value">₹<?php echo number_format($row['monthly_emi'], 2); ?></span>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="loan-info">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="loan-label">Repayment Progress:</span>
                                                <span class="loan-value"><?php echo round($progress); ?>%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar 
                                                    <?php echo $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-info' : 'bg-warning'); ?>" 
                                                    role="progressbar" 
                                                    style="width: <?php echo $progress; ?>%" 
                                                    aria-valuenow="<?php echo $progress; ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="view_loan.php?loan_id=<?php echo $row['id']; ?>" 
                                               class="btn see-more-btn">
                                               <i class="fadeIn animated bx bx-show"></i> See More
                                            </a>
                                            <a href="loan_payment.php?loan_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-outline-success btn-sm">
                                               <i class="fadeIn animated bx bx-credit-card"></i> Pay EMI
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="empty-state">
                                        <?php if (!empty($search)): ?>
                                            <i class="fadeIn animated bx bx-search-alt"></i>
                                            <h4>No loans found</h4>
                                            <p class="text-muted">No loans match your search criteria "<?php echo htmlspecialchars($search); ?>"</p>
                                            <a href="lonee.php" class="btn btn-primary">View All Loans</a>
                                        <?php else: ?>
                                            <i class="fadeIn animated bx bx-file"></i>
                                            <h4>No loans found</h4>
                                            <p class="text-muted">No loans have been added yet. Start by adding your first loan.</p>
                                            <a href="intrest.php" class="btn btn-primary">Add New Loan</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination or Count -->
                <?php if ($total_loans > 0): ?>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <div class="text-muted">
                            Showing <?php echo $total_loans; ?> loan<?php echo $total_loans > 1 ? 's' : ''; ?>
                            <?php if (!empty($search)): ?>
                                matching your search criteria
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Add hover effects and animations
document.addEventListener('DOMContentLoaded', function() {
    const loanCards = document.querySelectorAll('.loan-card');
    
    loanCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
        });
    });

    // Add smooth scrolling for better UX
    const seeMoreButtons = document.querySelectorAll('.see-more-btn');
    seeMoreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add a small delay for smooth transition
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        });
    });
});

// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.focus();
    }
});
</script>

<?php include('footer.php'); ?>