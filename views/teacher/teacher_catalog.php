<?php
session_start();

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/CatalogModel.php';
require_once __DIR__ . '/../../models/BorrowModel.php';
require_once __DIR__ . '/../../models/AuthModel.php';
require_once __DIR__ . '/../../models/ReservationModel.php'; 

if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../login.php");
    exit();
}

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

// --- Paging / Search ---
$resultsPerPage = 8;
$searchTerm = htmlspecialchars($_GET['search-input'] ?? '');
$categoryId = (int)($_GET['category-filter'] ?? 0);
$currentPage = (int)($_GET['page'] ?? 1);

// --- Init ---
$books = [];
$allCategories = [];
$totalBooksCount = 0;
$totalPages = 1;

try {
    $catalogModel = new CatalogModel();
    $borrowModel = new BorrowModel();
    $authModel = new AuthModel();
    $reservationModel = new ReservationModel(); 

    $allCategories = $catalogModel->getAllCategories();
    $totalBooksCount = $catalogModel->getTotalCatalogBooksCount(
        $searchTerm, 
        $categoryId,
        ['all']
    );
    $totalPages = ceil($totalBooksCount / $resultsPerPage);

    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $resultsPerPage;
    $books = $catalogModel->getCatalogBooks(
        $searchTerm, 
        $categoryId, 
        $resultsPerPage, 
        $offset, 
        ['all']
    );


    // --- Borrow Info for User ---
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'];
    $total_borrowed_books = count($borrowModel->getActiveBorrowsByUserId($user_id));
    $limits = $borrowModel->getBorrowLimits();
    $max_limit = $limits[$user_role] ?? 0;
    $is_limit_reached = ($total_borrowed_books >= $max_limit);

    $total_active_reservations = $reservationModel->getTotalActiveReservations($user_id);
    $max_reservation_limit = 3; 
    $is_reservation_limit_reached = ($total_active_reservations >= $max_reservation_limit);

} catch (Exception $e) {
    error_log("Catalog Data Error: " . $e->getMessage());
}


$root_path = "../../";
$page_title = "Library Catalog";
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

    <?php if (isset($_GET['message'])): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/../modals/library_catalog.php'; ?>
    <?php include __DIR__ . '/../modals/footer.php'; ?>

    <script src="<?= $root_path ?>assets/jQuery/jquery.js"></script>
    <script src="<?= $root_path ?>assets/jQuery/jquery-ui.js"></script>
    <script src="<?= $root_path ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    

</body>
</html>