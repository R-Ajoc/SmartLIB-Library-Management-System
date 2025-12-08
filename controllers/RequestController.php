<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$userFolder = ($role === 'teacher') ? 'teacher' : 'student';
$catalogFile = "{$userFolder}_catalog.php";
$baseRedirectPath = "../views/{$userFolder}/{$catalogFile}";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$baseRedirectPath}?type=danger&message=Invalid+request+method");
    exit();
}

$action = $_POST['action'] ?? null;
$bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
$requestId = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT); // for cancel_request

if (!$action || (($action !== 'cancel_request') && !$bookId)) {
    header("Location: {$baseRedirectPath}?type=danger&message=Missing+action+or+book+ID");
    exit();
}


require_once __DIR__ . '/../models/BorrowRequestModel.php';
require_once __DIR__ . '/../models/BorrowModel.php';
require_once __DIR__ . '/../models/ReservationModel.php';

$borrowRequestModel = new BorrowRequestModel();
$borrowModel = new BorrowModel();
$reservationModel = new ReservationModel();


function back($type, $msg) {
    global $baseRedirectPath;
    $msg = urlencode($msg);
    header("Location: {$baseRedirectPath}?type=$type&message=$msg");
    exit();
}



switch ($action) {

    // Borrow Request 
    case 'borrow_request':
        if (!$borrowModel->isBookAvailable($bookId)) {
            back('warning', 'Book is not available. You may place a reservation instead.');
        }

        if ($borrowRequestModel->hasPendingRequest($userId, $bookId)) {
            back('warning', 'You already have a pending borrow request for this book.');
        }

        $requestId = $borrowRequestModel->createRequest($userId, $bookId, 'borrow');
        if ($requestId) {
            back('success', 'Borrow request submitted! Please wait for staff approval.');
        } else {
            back('danger', 'Failed to submit borrow request. Please try again.');
        }
        break;

    // Reservation Request
    case 'reserve_request':
        if (!$bookId) {
            back('danger', 'Invalid book ID.');
        }

        if ($reservationModel->hasPendingReservation($userId, $bookId)) {
            back('warning', 'You already have a pending reservation for this book.');
        }

        $reservationId = $reservationModel->createReservation($userId, $bookId);
        if ($reservationId) {
            back('success', 'Reservation submitted! Check your Reservations modal.');
        } else {
            back('danger', 'Failed to submit reservation. Please try again.');
        }
        break;

    // Cancel Request
    case 'cancel_request':
        if (!$requestId) {
            back('danger', 'Invalid request ID.');
        }

        $success = $borrowRequestModel->cancelRequest($userId, $requestId);
        if ($success) {
            back('success', 'Request cancelled successfully.');
        } else {
            back('warning', 'Unable to cancel the request. It may already be processed.');
        }
        break;

    default:
        back('danger', 'Invalid action.');
}
