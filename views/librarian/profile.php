<?php 
session_start();

// Security check
if (!isset($_SESSION['is_logged_in'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../models/AuthModel.php';
$auth = new AuthModel();

// Fetch user data
$userId = $_SESSION['user_id'];
$user = $auth->getUserById($userId);

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Profile</title>
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/librarian.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="dashboard-body">

<?php require __DIR__ . '/navbar.php'; ?>

<main class="container my-5 d-flex flex-column align-items-center">

    <h2 class="profile-title"><i class="fas fa-user"> </i> My Profile</h2>

    <div class="profile-card card shadow-sm mt-3">
        <div class="card-header"></div>

        <div class="card-body">

            <!-- Name row: Firstname | M.I | Lastname -->
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-bold">First Name</label>
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

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" readonly>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" readonly>
            </div>

            <!-- Member Since -->
            <div class="mb-3">
                <label class="form-label fw-bold">Member Since</label>
                <input type="text" class="form-control" value="<?= isset($user['created_at']) ? date('F d, Y', strtotime($user['created_at'])) : ''; ?>" readonly>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" value="********" readonly>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-warning change-password-btn">Change Password</button>
                <button class="btn btn-info edit-profile-btn">Edit Profile</button>
            </div>

        </div>
    </div>
</main>

<div style="height: 50px;"></div>

<script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelector('.change-password-btn').addEventListener('click', function() {
        alert('Trigger Change Password modal or redirect here');
    });
</script>
</body>
</html>
