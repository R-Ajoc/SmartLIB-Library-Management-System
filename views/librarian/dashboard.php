<?php

require_once '../../helpers/auth_check.php'; // activated session here
require_once '../../models/AuthModel.php';
require_once '../../models/BookModel.php';
require_once '../../models/CategoryModel.php';

// Role-Based Access Control (RBAC)
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'librarian') {
    header("Location: ../login.php");
    exit();
}

$bookModel = new BookModel();
$categoryModel = new CategoryModel();

$totalArchivedBooks = $bookModel->countArchivedBooks();
$totalBooks = $bookModel->getTotalBooksCount(); 
$totalCategories = count($categoryModel->getAllCategories()); 
$totalCopies = $bookModel->getTotalCopiesInInventory(); 
$booksNeedingRestock = $bookModel->getLowStockCount(5); 
$recent_activities = $bookModel->getRecentActivity(5); 

$dashboard_title = "Welcome Back, " . htmlspecialchars($_SESSION['firstname']) . "!";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/librarian.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="dashboard-body">

    <?php require_once 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1><?= $dashboard_title ?></h1>
                <p>A quick overview of the library collection status.</p>
            </div>
        </div>

        <div class="row">
           <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-primary h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Total Books
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalBooks) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-success h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Available Books
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalCopies) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-info h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Categories
                                </div>
                                <div class="col-auto">
                                    <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalCategories) ?></div>
                                </div>
                            </div>
                        </div>   
                    </div>                    
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-warning h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Books Low in Stock
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($booksNeedingRestock) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-auto mb-4" style="flex: 1 1 18%;">
                <div class="card shadow border-left-warning h-100 py-2">
                    <div class="card-body text-center">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-2">
                                    Archived Books
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= number_format($totalArchivedBooks) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12 mb-4"> 
                <div class="card shadow h-100 p-3">
                    <h5 class="card-title">Recent Book Activity</h5>
                    <p class="card-text text-muted">A timeline of the last 5 books added or modified.</p>
                    
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <li class="list-group-item">
                                    <?php 
                                    $time = date("H:i", strtotime($activity['updated_at']));
                                    $date_text = (date("Y-m-d") == date("Y-m-d", strtotime($activity['updated_at']))) 
                                                ? "Today" : date("M d", strtotime($activity['updated_at']));
                                    $status = ($activity['updated_at'] == $activity['created_at']) ? 'Added' : 'Modified'; 
                                    ?>
                                    <strong><?= $time ?></strong> (<?= $date_text ?>) - <?= $status ?>: <?= htmlspecialchars($activity['title']) ?> (<?= htmlspecialchars($activity['author']) ?>)
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted">No recent book activity found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div style="height: 50px;"></div>

    <script src="../../assets/jQuery/jquery-ui.js"></script> 
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>