<?php
session_start();

// Auto-login if a "Remember Me" cookie exists
if (isset($_COOKIE['userID']) && !isset($_SESSION['userID'])) {
    require_once '../config/Database.php';
    require_once '../models/User.php';

    $database = new Database();
    $db = $database->conn;
    $user = new User($db);

    $result = $user->getUserByID($_COOKIE['userID']); 

    if ($result && $result['status']) {
        $_SESSION['userID'] = $result['data']['userID'];
        $_SESSION['role'] = $result['data']['role'];

        switch ($result['data']['role']) {
            case 'student':
                header("Location: dashboard_student.php");
                exit;
            case 'teacher':
                header("Location: dashboard_teacher.php");
                exit;
            case 'librarian':
                header("Location: lib_dashboard.php");
                exit;
            case 'staff':
                header("Location: dashboard_staff.php");
                exit;
        }
    }
}


if (isset($_SESSION['userID'])) {
    switch ($_SESSION['role']) {
        case 'student':
            header("Location: dashboard_student.php");
            exit;
        case 'teacher':
            header("Location: dashboard_teacher.php");
            exit;
        case 'librarian':
            header("Location: dashboard_librarian.php");
            exit;
        case 'staff':
            header("Location: dashboard_staff.php");
            exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Library Login</title>
  <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <!-- MAIN SECTION -->
    <div class="container-fluid vh-100 p-0 main-section d-flex">
        
        <!-- LEFT SIDE -->
        <div class="left-side d-none d-md-block"></div>

        <!-- RIGHT SIDE -->
        <div class="right-side d-flex flex-column justify-content-center align-items-center text-center text-light p-5 bg-maroon">
            <h1 class="display-4 fw-bold text-warning mb-2 welcome-title">Welcome to the<br>Smart Library</h1>
            <p class="mb-4 subtitle">Log in to enter</p>

            <form class="login-form w-75" method="POST" action="../controller/AuthController.php?action=login">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control rounded-pill" placeholder="username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control rounded-pill" placeholder="password" required>
                </div>
                <div class="form-check mb-3 text-light text-start">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
                <button type="submit" class="btn btn-warning rounded-pill px-5 mt-3">Log in</button>
            </form>

            <p class="mt-2">
                <a href="signup.php" class="text-warning text-decoration-underline">Don’t have an account?</a>
            </p>
        </div>
    </div>


  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
