<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['form_data']);
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
    <div class="main-container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="login-card">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-4" role="alert">
                    <strong>Login Failed:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="header-content text-center mb-4">
                <img src="../assets/images/index_logo.png" alt="Smart Library Logo" class="login-logo mb-2">
                <p class="text-muted">Log in to access your library account.</p>
            </div>

            <form class="login-form" method="POST" action="../controllers/AuthController.php?action=login">
                <input type="text" name="username" class="form-control mb-3" placeholder="Username" required
                       value="<?= htmlspecialchars($form_data['username'] ?? '') ?>">

                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                <div class="form-check d-flex justify-content-between align-items-center mb-4">
                    <label class="form-check-label" for="rememberMe">
                        <input class="form-check-input me-1" type="checkbox" name="remember_me" id="rememberMe" value="1">
                        Remember Me
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-custom w-100">Log In</button>

                <div class="login-link text-center mt-4">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
