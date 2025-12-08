<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['is_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$root_path = '../../'; 
$current_username = $_SESSION['firstname'] ?? $_SESSION['username'] ?? 'Guest';
$current_role = $_SESSION['role'] ?? 'user';

// Determine profile and setting paths based on role
$profile_path = '';
if ($current_role === 'student') {
    $profile_path = $root_path . 'views/student/student_profile.php';
} elseif ($current_role === 'teacher') {
    $profile_path = $root_path . 'views/teacher/teacher_profile.php';
} else {
    $profile_path = $root_path . 'views/login.php';
}

// Setting path
$setting_path = '';
if ($current_role === 'student') {
    $setting_path = $root_path . 'views/student/student_setting.php';
} elseif ($current_role === 'teacher') {
    $setting_path = $root_path . 'views/teacher/teacher_setting.php';
} else {
    $setting_path = $root_path . 'views/login.php';
}

?>


<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <img src="<?= $root_path ?>assets/images/logo.png" alt="Library Logo" style="max-height: 30px; margin-right: 10px;">
        </a>

        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item mx-3"><a class="nav-link" href="dashboard.php">Dashboard</a></li>

                <?php if($current_role === 'student'): ?>
                    <li class="nav-item mx-2"><a class="nav-link" href="student_catalog.php">Library</a></li>
                    <li class="nav-item mx-2"><a class="nav-link" href="student_record.php">My Records</a></li>
                <?php elseif($current_role === 'teacher'): ?>
                    <li class="nav-item mx-2"><a class="nav-link" href="teacher_catalog.php">Library</a></li>
                    <li class="nav-item mx-2"><a class="nav-link" href="exclusive_catalog.php">Exclusive</a></li>
                    <li class="nav-item mx-2"><a class="nav-link" href="teacher_record.php">My Records</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($current_username) ?> (<?= ucfirst($current_role) ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?= htmlspecialchars($profile_path) ?>">Profile</a>
                        
                        <a class="dropdown-item" href="<?= htmlspecialchars($setting_path) ?>">Setting</a>
                        <a class="dropdown-item" href="<?= $root_path ?>controllers/AuthController.php?action=logout">Logout</a>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
