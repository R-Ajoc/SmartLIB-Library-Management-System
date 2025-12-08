<?php

if (!defined('LIBRARIAN_PAGE')) {
    if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'librarian' && $_SESSION['role'] !== 'staff')) {
        header("Location: ../login.php"); 
        exit();
    }
}

$root_path = '../../'; // two levels up
$current_username = $_SESSION['firstname'] ?? $_SESSION['username'] ?? 'Guest';
$current_role = $_SESSION['role'] ?? 'librarian';

?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        
        <a class="navbar-brand" href="dashboard.php">
            <img src="<?= $root_path ?>assets/images/logo.png" alt="Library Logo" style="max-height: 30px; margin-right: 10px;">
        </a>

        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars" style="color:#6c2c2c; font-size:1.5rem;"></i>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item mx-2">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="book_management.php">Book Management</a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> 
                        <?= htmlspecialchars($current_username) ?> (<?= ucfirst($current_role) ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?= $root_path ?>views/librarian/profile.php">Profile</a>
                        <a class="dropdown-item" href="<?= $root_path ?>views/librarian/setting.php">Settings</a>
                        <a class="dropdown-item" href="<?= $root_path ?>controllers/AuthController.php?action=logout">Logout</a>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>