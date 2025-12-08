
<?php 
session_start();

if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../models/BorrowRequestModel.php';
require_once __DIR__ . '/../../models/BorrowModel.php';
require_once __DIR__ . '/../../models/ReservationModel.php';

$borrowRequestModel = new BorrowRequestModel();
$borrowModel = new BorrowModel();
$reservationModel = new ReservationModel();

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$rawRequestData = $borrowRequestModel->getRequestsByUser($user_id);
$requestData = [];
if (!empty($rawRequestData)) {
    foreach ($rawRequestData as $req) {
        if ($req['request_type'] === 'borrow') { 
            $requestData[] = [
                'request_id' => $req['request_id'],
                'title' => $req['title'],
                'request_date' => (new DateTime($req['request_date']))->format('M j, Y'),
                'status' => ucfirst($req['status'])
            ];
        }
    }
}


$rawLoanData = $borrowModel->getActiveBorrowsByUserId($user_id); 
$loanData = [];
$outstandingCount = 0;
$today = new DateTime();
$timezone = new DateTimeZone(date_default_timezone_get()); 

if (!empty($rawLoanData)) {
    foreach ($rawLoanData as $loan) {
        $dueDate = new DateTime($loan['due_date']);
        
        // Calculate overdue status
        $isOverdue = $today > $dueDate;
        
        // Format the data for the view
        $loanData[] = [
            'title' => $loan['title'],
            'borrowed' => (new DateTime($loan['borrow_date']))->format('M j, Y'), // Assumes borrow_date is included in the query
            'due' => $dueDate->format('M j, Y'),
            'price' => number_format($loan['fine_amount'], 2),
            'isOverdue' => $isOverdue
        ];
    }
    // Set the count for the outstanding badge
    $outstandingCount = count($loanData);
}


$rawReservationData = $reservationModel->getReservationsByUser($user_id);
$reservationData = [];
if (!empty($rawReservationData)) {
    foreach ($rawReservationData as $res) {
        $reservationData[] = [
            'request_id' => $res['reservation_id'], 
            'title' => $res['title'],
            'author' => $res['author'],
            'date' => (new DateTime($res['reservation_date']))->format('M j, Y'),
            'status' => ucfirst($res['status'])
        ];
    }
} else {
    $reservationData = []; 
}

$root_path = "../../";
$page_title = "My Records";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="<?= $root_path ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $root_path ?>assets/jQuery/jquery-ui.css">
    <link href="<?= $root_path ?>assets/DataTables/datatables.min.css" rel="stylesheet">
    <link href="<?= $root_path ?>assets/DataTables/responsive.dataTables.min.css" rel="stylesheet">
    <link href="<?= $root_path ?>assets/student.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="dashboard-body">

    <?php include __DIR__ . '/../modals/navbar.php'; ?>
    
    <div class="container mt-5">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-book-open me-2"></i><?= $page_title ?></h2>
                <div>
                    <button class="btn btn-warning custom-add-text" data-bs-toggle="modal" data-bs-target="#reservationsModal">
                        <i class="fas fa-calendar-check"></i> Reservations
                    </button>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/../modals/tabbedTable.php'; ?>

        <div class="alert alert-reminders py-2 mt-4 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Reminders:</strong>
            <span class="d-block small">
                1. Employees must return all borrowed books by the end of the semester to be cleared.<br>
                2. If a book is lost or damaged and not replaced, its cost may be processed through standard payroll deduction procedures.
            </span>
        </div>

    </div>

    <?php include __DIR__ . '/../modals/reservations.php'; ?>
    <?php include __DIR__ . '/../modals/clearance.php'; ?>
    <?php include __DIR__ . '/../modals/footer.php'; ?>
    

    <script src="<?= $root_path ?>assets/jQuery/jquery.js"></script>
    <script src="<?= $root_path ?>assets/jQuery/jquery-ui.js"></script>
    <script src="<?= $root_path ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $root_path ?>assets/DataTables/datatables.min.js"></script>
    <script src="<?= $root_path ?>assets/DataTables/dataTables.responsive.min.js"></script>
</body>
</html>