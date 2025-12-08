<?php 

require_once '../../helpers/auth_check.php';
require_once '../../models/AuthModel.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/StaffModel.php';
require_once __DIR__ . '/../../models/ClearanceModel.php';
require_once __DIR__ . '/../../models/BorrowModel.php';
require_once __DIR__ . '/../../models/PenaltyModel.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}


$staff = new StaffModel();
$clearance = new ClearanceModel();
$borrowModel = new BorrowModel();
$penaltyModel = new PenaltyModel();


$borrowSummary        = $staff->getBorrowedSummaryByUser();  
$borrowRequests       = $staff->getBorrowRequests();        
$reservations         = $staff->getReservations();     
$borrowRequestsCount  = !empty($borrowRequests) ? count($borrowRequests) : 0;
$reservationsCount    = !empty($reservations) ? count($reservations) : 0;
$pendingCount         = $staff->countPendingBorrowRequests();
$activeReservations   = $staff->countActiveReservations();
$overdueLoans         = $staff->countOverdueLoans();
$totalCleared         = $staff->countTotalCleared();
$studentsWithPenalties = $staff->countStudentWithPenalties();
$allUsersClearance    = $clearance->getUsersForClearance();
$activeBorrows        = $borrowModel->getAllActiveBorrows();
$readyPickups         = $staff->getReadyForPickupReservations();
$allPenalties         = $penaltyModel->getAllPenaltiesSummary();


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    $displaySummary = $staff->searchBorrowers($search); 
} else {
    $displaySummary = $borrowSummary;
}


$penaltySummary = [];
foreach ($allPenalties as $row) {
    $penaltySummary[] = [
        'user_id' => $row['user_id'],
        'full_name' => $row['full_name'], 
        'total_penalties' => $row['total_amount'],
        'last_updated' => $row['last_updated'],
        'unpaid_count' => $row['unpaid_count'],
    ];
}

$firstname = $_SESSION['firstname'];
$user_role = $_SESSION['role'];
$activeTab = $_GET['tab'] ?? 'dashboard';
$activeInnerTab = $_GET['innerTab'] ?? null;

$root_path = '../../';
$page_title = "Welcome Back, " . htmlspecialchars($firstname) . "!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="<?= $root_path ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $root_path ?>assets/staff.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="dashboard-body">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="<?= $root_path ?>assets/images/logo.png" alt="Library Logo" style="max-height: 30px; margin-right: 10px;">
        </a>

        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars" style="color:#6c2c2c; font-size:1.5rem;"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item mx-3"><a class="nav-link" href="#" data-page="dashboard">Dashboard</a></li>
                <li class="nav-item mx-3"><a class="nav-link" href="#" data-page="loan">Loan Management</a></li>
                <li class="nav-item mx-3"><a class="nav-link" href="#" data-page="clearance">Clearance</a></li>
                <li class="nav-item mx-3"><a class="nav-link" href="#" data-page="penalties">Penalties</a></li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($firstname) ?> (<?= ucfirst($user_role) ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= $root_path ?>views/staff/staff_profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="<?= $root_path ?>views/staff/staff_setting.php">Settings</a></li>
                        <li><a class="dropdown-item" href="<?= $root_path ?>controllers/AuthController.php?action=logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Alerts -->
<?php if (isset($_GET['message']) && isset($_GET['type'])): ?>
    <div class="alert alert-<?= $_GET['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- MAIN CONTENT -->
<div class="container mt-5">

    <!-- Dashboard -->
    <div id="page-dashboard" class="<?= $activeTab === 'dashboard' ? '' : 'd-none' ?>">
        <div class="row mb-4">
            <div class="col-12">
                <h1><?= $page_title ?></h1>
                <p>A quick overview of the library collection status.</p>
            </div>
        </div>
        <?php 
        require 'partials/dashboard_partial.php'; 
        require 'partials/user_management_partial.php';
        ?>
    </div>

    <!-- Loan Management -->
    <div id="page-loan" class="<?= $activeTab === 'loan' ? '' : 'd-none' ?>">
        <?php require 'partials/loan_management.php'; ?>
    </div>

    <!-- Clearance -->
    <div id="page-clearance" class="<?= $activeTab === 'clearance' ? '' : 'd-none' ?>">
        <?php require 'partials/clearance_partial.php'; ?>
    </div>

    <!-- Penalties -->
    <div id="page-penalties" class="<?= $activeTab === 'penalties' ? '' : 'd-none' ?>">
        <?php require 'partials/penalties_partial.php'; ?>
    </div>

</div>

<!-- Borrow History Modal -->
<div class="modal fade" id="borrowHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Borrow History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr class="text-center text-sm text-muted">
                                <th>Book</th>
                                <th>Date Borrowed</th>
                                <th>Due Date</th>
                                <th>Date Returned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="borrow-history-table"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div style="height: 50px;"></div>

<script src="<?= $root_path ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $root_path ?>assets/staff.js"></script>


</body>
</html>
