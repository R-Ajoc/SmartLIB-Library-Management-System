<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}


require_once __DIR__ . '/../models/StaffModel.php';
require_once __DIR__ . '/../models/ClearanceModel.php';
require_once __DIR__ . '/../models/BorrowModel.php';
require_once __DIR__ . '/../models/PenaltyModel.php';

$staffModel      = new StaffModel();
$clearanceModel  = new ClearanceModel();
$borrowModel     = new BorrowModel();
$penaltyModel    = new PenaltyModel();

$staffId = $_SESSION['user_id'];

$action        = $_POST['action']        ?? null;
$userId        = $_POST['user_id']       ?? null;
$requestId     = $_POST['request_id']    ?? null;
$reservationId = $_POST['reservation_id']?? null;
$penaltyId     = $_POST['penalty_id']    ?? null;
$borrowId      = $_POST['borrow_id']     ?? null;

$redirect = "../views/staff/staff_main.php";

// Redirect helper
function back($path, $type, $msg, $mainTab = '', $innerTab = '') {
    $url = "{$path}?type={$type}&message=" . urlencode($msg);
    if ($mainTab !== '') {
        $url .= "&tab={$mainTab}";
    }
    if ($innerTab !== '') {
        $url .= "&innerTab={$innerTab}";
    }
    header("Location: {$url}");
    exit();
}


// Borrow History AJAX Handler
if (isset($_GET['action'], $_GET['user_id']) && $_GET['action'] === 'getBorrowHistory') {
    $history = $staffModel->getUserBorrowHistory((int) $_GET['user_id']);
    echo json_encode($history);
    exit();
}


switch ($action) {
    // Borrow Requests
    case 'approve_borrow':
        if ($requestId && $staffModel->approveBorrowRequest($requestId, $staffId))
            back($redirect, 'success', 'Borrow request approved.', 'loan', 'requests');
        back($redirect, 'danger', 'Failed to approve borrow request.', 'loan', 'requests');

    case 'reject_borrow':
        if ($requestId && $staffModel->rejectBorrowRequest($requestId, $staffId))
            back($redirect, 'success', 'Borrow request rejected.', 'loan', 'requests');
        back($redirect, 'danger', 'Failed to reject borrow request.', 'loan', 'requests');

    // Reservations
    case 'approve_reservation':
        if ($reservationId && $staffModel->approveReservation($reservationId, $staffId))
            back($redirect, 'success', 'Reservation approved.', 'loan', 'reservation');
        back($redirect, 'danger', 'Failed to approve reservation.', 'loan', 'reservation');

    case 'reject_reservation':
        if ($reservationId && $staffModel->rejectReservation($reservationId))
            back($redirect, 'success', 'Reservation rejected.', 'loan', 'reservation');
        back($redirect, 'danger', 'Failed to reject reservation.', 'loan', 'reservation');

    case 'pickup_reservation':
        if ($reservationId) {

            $staffModel->markReservationPickedUp($reservationId, $staffId);
            $reservation = $staffModel->getReservationById($reservationId);
            $borrowModel->createBorrow($reservation['user_id'], $reservation['book_id']);

            $clearanceModel->resetClearanceIfBorrowed($reservation['user_id']);

            back($redirect, 'success', 'Book picked up and added to borrowed books.', 'loan', 'pickups');
        }
        back($redirect, 'danger', 'Failed to record pick up.', 'loan', 'pickups');
        break;

    case 'mark_returned':
        if ($borrowId) {
        
            $borrow = $borrowModel->getAllActiveBorrows($borrowId);
            if ($borrowModel->markReturned($borrowId)) {
            
                $clearanceModel->resetClearanceIfBorrowed($borrow['user_id']);

                back($redirect, 'success', 'Book marked as returned.', 'loan', 'returnManagement');
            }
        }
        back($redirect, 'danger', 'Failed to mark book as returned.', 'loan', 'returnManagement');
        break;

    // Clearance
    case 'clear_user':
        if ($userId) {
            if ($clearanceModel->markUserCleared($userId, $staffId)) {
                back($redirect, 'success', 'User cleared successfully.', 'clearance');
            }
        }
        back($redirect, 'danger', 'Failed to clear user.', 'clearance');
        break;

    // Set Penalties 
    case 'set_all_penalties':
    $activeBorrows = $borrowModel->getAllActiveBorrows();
    $count = 0;

    foreach ($activeBorrows as $borrow) {
        if ($penaltyModel->getPenaltyByBorrowId($borrow['borrow_id'])) continue;

        $amount = $borrow['price'] ?? 0;
        $description = "Penalty for unreturned book: " . $borrow['title'];

        $penaltyModel->createPenalty(
            $borrow['borrow_id'],
            $borrow['user_id'],
            $amount,
            $description
        );

        $count++;
    }

    back($redirect, 'success', "Set penalties for {$count} unreturned book(s).", 'clearance');
    break;

    case 'mark_all_penalties_paid':
        if ($userId) {
            if ($penaltyModel->markAllPenaltiesPaidByUser($userId)) {
                back($redirect, 'success', 'All penalties for this user have been marked as paid.', 'clearance');
            }
        }
        back($redirect, 'danger', 'Failed to mark penalties as paid.', 'clearance');
        break;


    default:
        back($redirect, 'danger', 'Invalid action.');
        
}
?>
