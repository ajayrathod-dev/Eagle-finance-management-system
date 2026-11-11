<?php
session_start();
include("connection.php");

// Check user authentication
if (isset($_COOKIE['uuid'])) {
    $u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);
} else {
    header("Location: logout.php");
    exit;
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = mysqli_query($link, $sql4);
if (!$result4) {
    die("Query failed: " . mysqli_error($link));
}
if ($result4->num_rows > 0) {
    $row4 = $result4->fetch_assoc();
    $u_uname = mysqli_real_escape_string($link, $row4['user_id']);
    $br_id = mysqli_real_escape_string($link, $row4['user_id']);
} else {
    header("Location: logout.php");
    exit;
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_sql = "DELETE FROM vehicle_license WHERE id='$delete_id'";
    if (mysqli_query($link, $del_sql)) {
        header("Location: license.php?success=License Deleted");
    } else {
        header("Location: license.php?error=Delete Failed");
    }
    exit;
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

    .rainbow {
        text-align: center;
        text-decoration: none;
        font-size: 14px;
        font-family: monospace;
        letter-spacing: 0px;
    }
    
    .rainbow_text_animated {
        background: linear-gradient(to right, #6666ff, #0099ff, #00ff00, #ff3399, #6666ff);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        animation: rainbow_animation 6s ease-in-out infinite;
        background-size: 400% 100%;
    }

    @keyframes rainbow_animation {
        0%,100% {
            background-position: 0 0;
        }
        50% {
            background-position: 100% 0;
        }
    }
</style>

<div class="page-wrapper">
    <div class="page-content">
        <!-- Display success/error messages -->

        
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="col-lg-12">
                    <div class="alert alert-dark border-0 bg-dark alert-dismissible fade show" style="height:60px;">
                        <div class="text-white"><h5 class="text-white">Create Renew Reminder</h5></div>
                    </div>
                </div>
                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <form class="row g-3" action="license1.php" method="post">
                            <div class="col-md-6">
                                <label for="vh_no" class="form-label">Vehicle Number</label>
                                <input type="text" class="form-control" id="vh_no" name="vh_no" required>
                            </div>
                            <div class="col-md-6">
                                <label for="c_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="c_name" name="c_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="c_mobile" class="form-label">Customer Mobile</label>
                                <input type="number" class="form-control" id="c_mobile" name="c_mobile" required>
                            </div>
                            <div class="col-md-6">
                                <label for="alt_mob" class="form-label">Alternate Mobile No</label>
                                <input type="text" class="form-control" id="alt_mob" name="alt_mob" required>
                            </div>
                            <!-- Change these input field names to match what you're accessing in license1.php -->
<div class="col-md-6">
    <label for="licence" class="form-label">Licence</label>
    <input type="text" class="form-control" id="licence" name="licence_number" required> <!-- Changed name -->
</div>
<div class="col-md-6">
    <label for="licence_renewal" class="form-label">Licence Renewal</label>
    <input type="date" class="form-control" id="licence_renewal" name="licence_renewal" required>
</div>
<div class="col-md-6">
    <label for="permit_renewal" class="form-label">Permit Renewal</label>
    <input type="date" class="form-control" id="permit_renewal" name="Permit_renual" required> <!-- Changed name -->
</div>
<div class="col-md-6">
    <label for="fitness_renewal" class="form-label">Fitness Renewal</label>
    <input type="date" class="form-control" id="fitness_renewal" name="fitness_renewal" required> <!-- Changed name -->
</div>
<div class="col-md-6">
    <label for="insurance_renewal" class="form-label">Insurance Renewal</label>
    <input type="date" class="form-control" id="insurance_renewal" name="insurance_renewal" required> <!-- Changed name -->
</div>
<div class="col-md-6">
    <label for="tax_renewal" class="form-label">Tax Renewal</label>
    <input type="date" class="form-control" id="tax_renewal" name="tax_renewal" required> <!-- Changed name -->
</div>
                            <div class="col-12" align="center">
                                <button type="submit" class="btn btn-outline-primary px-5">Submit</button>
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
                                 <th>ID</th>
                                 <th>Vehicle No</th>
                                 <th>Customer Name</th>
                                 <th>Mobile</th>
                                 <th>Alt Mobile</th>
                                 <th>Licence No</th>
                                 <th>Licence Renewal</th>
                                 <th>Permit Renewal</th>
                                 <th>Fitness Renewal</th>
                                 <th>Insurance Renewal</th>
                                 <th>Tax Renewal</th>
                                <th>By</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sr = 1;
                            $sql1 = "SELECT * FROM vehicle_license ORDER BY id DESC";
                            $result1 = mysqli_query($link, $sql1);
                            if (!$result1) {
                                die("Query failed: " . mysqli_error($link));
                            }
                            if ($result1->num_rows > 0) {
                                while ($row = mysqli_fetch_array($result1)) {
                            ?>
                                    <tr>
                                        <td><?php echo $sr; ?></td>
                                         <td><?= $row['vh_no'] ?></td>
                                        <td><?= $row['c_name'] ?></td>
                                        <td><?= $row['c_mobile'] ?></td>
                                        <td><?= $row['alt_mob'] ?></td>
                                        <td><?= $row['licence_number'] ?></td>
                                        <td><?= $row['licence_renewal'] ?></td>
                                        <td><?= $row['permit_renual'] ?></td>
                                        <td><?= $row['fitness_renewal'] ?></td>
                                        <td><?= $row['insurance_renewal'] ?></td>
                                        <td><?= $row['tax_renewal'] ?></td>
                                        <td><?php echo htmlspecialchars($row['by']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td>
                                            <a href="license.php?delete_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this license?');">
                                                <i class='fadeIn animated bx bx-trash'></i>
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

<?php include('footer.php'); ?>