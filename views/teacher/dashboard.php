<?php

require_once '../../helpers/auth_check.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/AuthModel.php';
require_once __DIR__ . '/../../models/BorrowModel.php'; 
require_once __DIR__ . '/../../models/ReservationModel.php'; 
require_once __DIR__ . '/../../models/CatalogModel.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}


$authModel = new AuthModel();
$borrowModel = new BorrowModel(); 
$reservationModel = new ReservationModel(); 

$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'];
$user_role = $_SESSION['role'];

$outstanding_transactions = $borrowModel->getActiveBorrowsByUserId($user_id); 
$is_cleared = $authModel->isUserClearedForBorrowing($user_id, $user_role);
$overdueCount = $borrowModel->countOverdueBooks();
$total_borrowed_books = count($borrowModel->getActiveBorrowsByUserId($user_id));
$total_fines = $borrowModel->getTotalOutstandingFines($user_id); 
$total_reservations = $reservationModel->getTotalActiveReservations($user_id); 
$limits = $borrowModel->getBorrowLimits();
$max_limit = $limits[$user_role] ?? 0; 
$remaining = max(0, $max_limit - $total_borrowed_books);
$is_limit_reached = ($total_borrowed_books >= $max_limit);
$max_reservation_limit = 3; 
$is_reservation_limit_reached = ($total_reservations >= $max_reservation_limit);


// --- Placeholder Functions ---
if (!function_exists('getRandomHexColor')) {
    function getRandomHexColor() {
        return strtoupper(str_pad(dechex(mt_rand(0, 16777215)), 6, '0', STR_PAD_LEFT));
    }
    function createPlaceholderURL($title) {
        $width = 100; $height = 140;
        $bgColor = getRandomHexColor();
        $encodedTitle = urlencode(str_replace(' ', '+', $title));
        return "https://placehold.co/{$width}x{$height}/{$bgColor}/FFF?text={$encodedTitle}";
    }
}


try {
    $catalogModel = new CatalogModel();
    $books_preview = $catalogModel->getLatestBooks(6); 
} catch (Exception $e) {
    error_log("Dashboard Preview Books Error: " . $e->getMessage());
    $books_preview = [];
}

$root_path = '../../';
$dashboard_title = "Welcome Back, " . htmlspecialchars($firstname) . "!";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dashboard_title ?></title>
    <link href="<?= $root_path ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $root_path ?>assets/student.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include __DIR__ . '/../modals/navbar.php'; ?>

    <div class="main-content container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1><?= $dashboard_title ?></h1>
                <p>Here’s a summary of your library activity and resources.</p>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-6 col-lg-auto mb-2" style="flex: 0 0 25%;">
                <div class="card shadow border-left-<?= $is_cleared ? 'success' : 'danger' ?> h-80 py-2">
                    <div class="card-body text-center">
                        <div class="text-xs font-weight-bold text-uppercase mb-4">Clearance Status</div>
                        <div class="h5 mb-4 font-weight-bold text-gray-800">
                            <span class="text-<?= $is_cleared ? 'success' : 'danger' ?>">
                                <?= $is_cleared ? 'CLEARED' : 'UNCLEARED' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-2" style="flex: 0 0 25%;">
                <div class="card shadow border-left-warning h-80 py-2">
                    <div class="card-body text-center">
                        <div class="text-xs font-weight-bold text-uppercase mb-4">Borrowed Books</div>
                        <div class="h5 mb-4 font-weight-bold text-gray-800"><?= number_format($total_borrowed_books) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-2" style="flex: 0 0 25%;">
                <div class="card shadow border-left-warning h-80 py-2">
                    <div class="card-body text-center">
                        <div class="text-xs font-weight-bold text-uppercase mb-4">Reservations</div>
                        <div class="h5 mb-4 font-weight-bold text-gray-800"><?= number_format($total_reservations) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-2" style="flex: 0 0 25%;">
                <div class="card shadow border-left-warning h-80 py-2">
                    <div class="card-body text-center">
                        <div class="text-xs font-weight-bold text-uppercase mb-4">Overdue Books</div>
                        <div class="h5 mb-4 font-weight-bold text-gray-800"><?= $overdueCount ?></div>
                    </div>
                </div>
            </div>

        </div>

        <hr class="my-4">

        <div class="col-12 mb-3">
            <h2>Latest Books</h2>
            <p>Browse a few books from our library. <a href="<?= $root_path ?>views/teacher/teacher_catalog.php" class="btn btn-sm btn-warning">Go to Library</a></p>
        </div>

        <hr class="my-4">

        <div class="row mb-5">
            <?php foreach($books_preview as $book): 
                $cover = !empty($book['cover_image']) ? $book['cover_image'] : createPlaceholderURL($book['title']); 
            ?>
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= $cover ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title mb-1"><?= htmlspecialchars($book['title']) ?></h6>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($book['author']) ?></p>

                        <!-- Borrow Form -->
                        <form action="<?= $root_path ?>controllers/RequestController.php" method="POST" class="d-grid gap-1">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['book_id']) ?>">
                            <button type="submit" name="action" value="borrow_request" 
                                class="btn btn-sm btn-success" 
                                <?= $is_limit_reached ? 'disabled' : '' ?>>
                                <?= $is_limit_reached ? 'Limit Reached' : 'Borrow' ?>
                            </button>
                        </form>

                        <!-- Reserve Form -->
                        <form action="<?= $root_path ?>controllers/RequestController.php" method="POST" class="d-grid gap-1 mt-2">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['book_id']) ?>">
                            <button type="submit" name="action" value="reserve_request" 
                                class="btn btn-sm btn-warning" 
                                <?= $is_reservation_limit_reached ? 'disabled title="Reservation Limit Reached"' : '' ?>>
                                <?= $is_reservation_limit_reached ? 'Maxed Out' : 'Reserve' ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include __DIR__ . '/../modals/footer.php'; ?>

<script src="<?= $root_path ?>assets/jQuery/jquery.js"></script>
<script src="<?= $root_path ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>