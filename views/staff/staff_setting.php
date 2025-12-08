<?php 
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}

$root_path = '../../'; 
$page_title = "Account Settings";

require_once $root_path . 'models/AuthModel.php';
$auth = new AuthModel();

$userId = $_SESSION['user_id'];
$user = $auth->getUserById($userId);

if (!$user) {
    die("User not found or session invalid.");
}

$firstname = $user['firstname'];
$user_role = $user['role']; 
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

    <style>
        .settings-container {
            max-width: 800px;
            margin-top: 50px;
        }
    </style>
</head>
<body class="dashboard-body">

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $root_path ?>views/staff/staff_main.php">
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
                        <li><a class="dropdown-item active" href="<?= $root_path ?>views/staff/staff_setting.php">Settings</a></li>
                        <li><a class="dropdown-item" href="<?= $root_path ?>controllers/AuthController.php?action=logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container settings-container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><i class="fas fa-cog me-2"></i> Account Settings</h1>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
        unset($_SESSION['message']); 
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>


    <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                <i class="fas fa-user-edit me-1"></i> General Information
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                <i class="fas fa-lock me-1"></i> Change Password
            </button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabContent">
        
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Update Account Details</h5>
                    <form action="<?= $root_path ?>controllers/AuthController.php?action=update_profile" method="POST">
                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                        
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="inputEmail" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="inputFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="inputFirstName" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                            </div>
                             <div class="col-md-2 mb-3">
                                <label for="inputMidInt" class="form-label">M.I</label>
                                <input type="text" class="form-control" id="inputMidInt" name="midint" value="<?= htmlspecialchars($user['midint']) ?>" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="inputLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="inputLastName" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                             <label for="inputUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="inputUsername" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Change Your Password</h5>
                    <form action="<?= $root_path ?>controllers/AuthController.php?action=change_password" method="POST">
                        <input type="hidden" name="user_id" value="<?= $userId ?>">

                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning"><i class="fas fa-key me-1"></i> Change Password</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
    
</div>

<div style="height: 50px;"></div>

<script src="<?= $root_path ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelector('#password-tab').addEventListener('click', function() {
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    });
</script>
</body>
</html>