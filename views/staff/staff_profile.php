<?php 
session_start();

if (!isset($_SESSION['is_logged_in'])) {
    header("Location: ../../login.php");
    exit();
}

$root_path = '../../'; 
require_once $root_path . 'models/AuthModel.php';
$auth = new AuthModel();

$userId = $_SESSION['user_id'];
$user = $auth->getUserById($userId);

if (!$user) {
    die("User not found.");
}

$firstname = $user['firstname'];
$user_role = $user['role']; 
// this is hardcoded
$user_bio = $user['bio'] ?? 'Dedicated staff member of the library system. Available for loan management and member assistance.'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link href="<?= $root_path ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="<?= $root_path ?>assets/librarian.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-card {
            max-width: 600px;
            width: 100%;
        }
        .profile-card .form-control[readonly] {
            background-color: #e9ecef; 
            font-weight: 500;
        }
    </style>
</head>
<body class="dashboard-body">

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
                <li class="nav-item mx-3"><a class="nav-link" href="<?= $root_path ?>views/staff/staff_main.php?tab=dashboard">Dashboard</a></li>
                <li class="nav-item mx-3"><a class="nav-link" href="<?= $root_path ?>views/staff/staff_main.php?tab=loan">Loan Management</a></li>
                <li class="nav-item mx-3"><a class="nav-link" href="<?= $root_path ?>views/staff/staff_main.php?tab=clearance">Clearance</a></li>
                <li class="nav-item mx-3"><a class="nav-link" href="<?= $root_path ?>views/staff/staff_main.php?tab=penalties">Penalties</a></li>
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
<main class="container my-5 d-flex flex-column align-items-center">

    <h2 class="profile-title"><i class="fas fa-id-card"> </i> Staff Profile Summary</h2>

    <div class="profile-card card shadow-lg mt-3">
        <div class="card-header text-white text-center" style="background-color: #2C3E50;">
            <h5 class="mb-0">Personal Information</h5>
        </div>

        <div class="card-body">
            
            <div class="mb-4 text-center p-3 border rounded">
                <p class="mb-1 text-muted">Profile Description / Bio</p>
                <p class="lead mb-0 fst-italic">"<?= htmlspecialchars($user_bio); ?>"</p>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-bold"><i class="fas fa-signature"></i> First Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['firstname'] ?? ''); ?>" readonly>
                </div>
                <div class="col">
                    <label class="form-label fw-bold">M.I</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['midint'] ?? ''); ?>" readonly>
                </div>
                <div class="col">
                    <label class="form-label fw-bold">Last Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['lastname'] ?? ''); ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold"><i class="fas fa-user-tag"></i> Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold"><i class="fas fa-at"></i> Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" readonly>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-briefcase"></i> Role</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($user_role) ?? 'N/A'); ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-calendar-alt"></i> Member Since</label>
                    <input type="text" class="form-control" value="<?= isset($user['created_at']) ? date('F d, Y', strtotime($user['created_at'])) : 'N/A'; ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" value="********" readonly>
                <div class="form-text">Use the **Settings** page to update your password or profile details.</div>
            </div>
            
            </div>
    </div>
</main>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>